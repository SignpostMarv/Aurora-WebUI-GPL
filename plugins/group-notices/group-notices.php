<?php
/*
Plugin Name: Group Notices
Plugin URI: https://github.com/SignpostMarv/Aurora-WebUI-GPL
Description: Various output formats for Group Notices
Version: 0.1
Author: SignpostMarv
Author URI: https://github.com/SignpostMarv/
*/

namespace Aurora\Addon\WebUI\plugins{

	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\Configs;

//! We're going to use a reflection-based method for supporting various output formats, so we need an interface to look for.
	interface GroupNotices{

//!	@return object implementation of Aurora::Addon::WebUI::plugins::GroupNotices
		public static function r(WebUI $WebUI);

//!	@param object $news instance of Aurora::Addon::WebUI::GetGroupNotices
		public function group_notices(WebUI\GetGroupNotices $news);

//!	@param object $item instance of Aurora::Addon::WebUI::GroupNoticeData
		public function group_notice(WebUI\GroupNoticeData $item);
	}


	function group_notices(WebUI\GetGroupNotices $news, $format='hAtom', WebUI $WebUI=null){
		if(isset($WebUI) === false){
			$WebUI = Configs::d();
		}

		if(class_exists('\Aurora\Addon\WebUI\plugins\GroupNotices\\' . $format) === true){
			$class = call_user_func_array('\Aurora\Addon\WebUI\plugins\GroupNotices\\' . $format . '::r', array($WebUI));
			if(is_a($class, '\Aurora\Addon\WebUI\plugins\GroupNotices') === true){
				call_user_func_array(array($class, 'group_notices'), array($news));
			}
		}
	}


	function group_notice(WebUI\GroupNoticeData $item, $format='hAtom', WebUI $WebUI=null){
		if(isset($WebUI) === false){
			$WebUI = Configs::d();
		}

		if(class_exists('\Aurora\Addon\WebUI\plugins\GroupNotices\\' . $format) === true){
			$class = call_user_func_array('\Aurora\Addon\WebUI\plugins\GroupNotices\\' . $format . '::r', array($WebUI));
			if(is_a($class, '\Aurora\Addon\WebUI\plugins\GroupNotices') === true){
				call_user_func_array(array($class, 'group_notice'), array($item));
			}
		}
	}

	add_action('group_notice', __NAMESPACE__ . '\group_notice', 10, 3);
	add_action('group_notices', __NAMESPACE__ . '\group_notices', 10, 3);
}

namespace Aurora\Addon\WebUI\plugins\GroupNotices{

	use Globals;

	use OpenMetaverse\AssetType;

	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI\Template;
	use Aurora\Addon\WebUI\plugins\GroupNotices;


	abstract class abstractGroupNotices implements GroupNotices{

		protected $WebUI;


		protected function __construct(WebUI $WebUI){
			$this->WebUI = $WebUI;
		}


		public static function r(WebUI $WebUI){
			static $registry = array();

			if(isset($regsitry[spl_object_hash($WebUI)]) === false){
				$registry[spl_object_hash($WebUI)] = new static($WebUI);
			}

			return $registry[spl_object_hash($WebUI)];
		}
	}


	class hAtom extends abstractGroupNotices{


		public function group_notices(WebUI\GetGroupNotices $news){
?>
		<ol class=hfeed>
<?php
			foreach($news as $item){
				static::group_notice($item);
			}
?>
		</ol>
<?php
		}


		public function group_notice(WebUI\GroupNoticeData $item){
?>
			<li class=hentry>
				<h2 class=entry-title><?php echo esc_html($item->Subject()); ?></h2>
				<abbr class=published title="<?php echo esc_attr(date('c', $item->Timestamp())); ?>"><?php echo esc_html(date(apply_filters('news_date_format', 'F h:ia'), $item->Timestamp())); ?></abbr>
				<p class=entry-content><?php echo wp_kses(nl2br($item->Message()), array('br'=>array())); ?></p>
				<ul class="vcard author">
					<li class=user><?php echo esc_html(__('Author')); ?>: <a class="url fn" href="<?php echo esc_attr(Template\link('/world/user/' . urlencode($item->FromName()))); ?>"><?php echo esc_html($item->FromName()); ?></a></li>
					<li class=org><?php echo esc_html(__('Group')); ?>: <a class="url fn" href="<?php echo esc_attr(Template\link('/world/group/' . urlencode($this->WebUI->GetGroup($item->GroupID())->GroupName()))); ?>"><?php echo esc_html($this->WebUI->GetGroup($item->GroupID())->GroupName()); ?></a></li>
				</ul>
<?php
			if($item->HasAttachment()){
				switch($item->AssetType()){
					case AssetType::Texture: ?>
				<a class=attachment rel=enclosure href="<?php echo esc_attr($this->WebUI->GridTexture($item->ItemID())); ?>"><?php echo esc_html($item->ItemName()); ?></a>
<?php				break;
				}
			}
?>
				<span class="uuid uid"><?php echo esc_html($item->NoticeID()); ?></span>
			</li>
<?php
		}
	}


	class atom extends abstractGroupNotices{


		public function group_notices(WebUI\GetGroupNotices $news){
			$groups = $news->Groups();
			$groupNames = array();
			foreach($groups as $group){
				$groupNames[] = $group->GroupName();
			}
			$groupNamesString = array_shift($groupNames);
			$lastGroupName = null;
			if(count($groupNames) > 0){
				$lastGroupName = array_pop($groupNames);
			}
			if(count($groupNames) > 0){
				$groupNamesString .= implode(', ', $groupNames);
			}
			if(isset($lastGroupName) === true){
				$groupNamesString .= ' & ' . $lastGroupName;
			}

			$groupNotices = array();

			$i = 0;
			$j = $groups->count() * 10;
			foreach($news as $groupNotice){
				$groupNotices[$groupNotice->Timestamp()] = $groupNotice;
				if(++$i >= $j){
					break;
				}
			}

			ksort($groupNotices, SORT_NUMERIC);

			echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<feed xmlns="http://www.w3.org/2005/Atom">
	<title><?php echo esc_html(sprintf(__('Group Notices for %s'), $groupNamesString)); ?></title>
<?php		if(count($groupNotices) > 0){ ?>
	<updated><?php echo esc_html(date('c', key($groupNotices))); ?></updated>
<?php		}
			foreach($groupNotices as $item){
				static::group_notice($item);
			}
 ?>
</feed><?php
		}


		public function group_notice(WebUI\GroupNoticeData $item){
?>
	<entry>
		<title><?php echo esc_html($item->Subject()); ?></title>
		<id><?php echo esc_html($item->NoticeID()); ?></id>
		<published><?php echo esc_html(date('c', $item->Timestamp())); ?></published>
		<author>
			<name><?php echo esc_html($item->FromName()); ?></name>
			<uri><?php echo esc_html(Globals::i()->baseURI . Template\link('world/user/' . urlencode($item->FromName()))); ?></uri>
		</author>
		<content type="text"><?php echo esc_html($item->Message()); ?></content>
<?php
			if($item->HasAttachment()){
				switch($item->AssetType()){
					case AssetType::Texture: ?>
		<link rel="enclosure" type="image/jpeg" href="<?php echo esc_attr($this->WebUI->GridTexture($item->ItemID())); ?>" title="<?php echo esc_attr($item->ItemName()); ?>" />
<?php				break;
				}
			}
?>
	</entry>
<?php
		}
	}
}
?>