<?php
/*
Plugin Name: Friends List editor
Plugin URI: https://github.com/SignpostMarv/Aurora-WebUI-GPL
Description: Spits out a Friends List editing form based on an instance of Aurora::Addon::WebUI::FriendsList
Version: 0.1
Author: SignpostMarv
Author URI: https://github.com/SignpostMarv/
*/

namespace Aurora\Addon\WebUI\plugins{

	use OpenMetaverse\FriendRights;

	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\Configs;
	use Aurora\Addon\WebUI\FriendInfo;
	use Aurora\Addon\WebUI\FriendsList;

//!	Spits out a Friends List editing form based on an instance of Aurora::Addon::WebUI::FriendsList
/**
*	@param object instance of Aurora::Addon::WebUI::FriendsList
*	@param mixed $currentGrid NULL or instance of Aurora::Addon::WebUI corresponding to the currently selected grid.
*/
	function friends_list_editor(FriendsList $friendsList, WebUI $currentGrid=null){
		$friendsList->rewind();
		if(isset($currentGrid) === false){
			$currentGrid = Configs::d();
		}

		do_action('before_friends_list_editor', $friendsList);
		echo '<form method=post action=?edit-friends-list class="', esc_attr(implode(' ', array_unique(array_merge(array('friends-list-editor'), apply_filters('friends_list_editor_class', array()))))),'">';
		echo '<fieldset><legend>',esc_html(__('Friends')),'</legend>';
		do_action('before_friends_list_editor_table', $friendsList);
		echo '<table>';
		do_action('before_friends_list_editor_thead', $friendsList);
		echo '<thead><tr>',
			'<th>',esc_html(__('Online')),'</th>',
			'<th>',esc_html(__('Name')),'</th>',
			'<th abbr="',esc_attr(__('Can see you online')),'">',esc_html(__('Friend can see when you\'re online')),'</th>',
			'<th abbr="',esc_attr(__('Can see you on map')),'">',esc_html(__('Friend can locate you on the map')),'</th>',
			'<th abbr="',esc_attr(__('Can edit your objects')),'">',esc_html(__('Friend can edit, delete or take objects.')),'</th>',
			'<th abbr="',esc_attr(__('You can map them')),'">',esc_html(__('You can locate this friend on the map')),'</th>',
			'<th abbr="',esc_attr(__('Can edit their objects')),'">',esc_html(__('You can edit this friend\'s objects')),'</th>',
		'</tr></thead>';
		do_action('after_friends_list_editor_thead', $friendsList);
		do_action('before_friends_list_editor_tbody', $friendsList);
		echo '<tbody>';
		foreach($friendsList as $friendInfo){
			$canSeeMeOnline      = ($friendInfo->TheirFlags() & FriendRights::CanSeeOnline);
			$canMapMe            = ($friendInfo->TheirFlags() & FriendRights::CanSeeOnMap);
			$canEditMyObjects    = ($friendInfo->TheirFlags() & FriendRights::CanModifyObjects);
			$canSeeThemOnline    = ($friendInfo->MyFlags()    & FriendRights::CanSeeOnline);
			$canMapThem          = ($friendInfo->MyFlags()    & FriendRights::CanSeeOnMap);
			$canEditTheirObjects = ($friendInfo->MyFlags()    & FriendRights::CanModifyObjects);
			
			$isOnline         = $canSeeThemOnline ? $currentGrid->GetGridUserInfo($friendInfo)->Online() : false;
			echo '<tr>',
				'<td class=online-status online-status-',esc_attr($isOnline ? 'true' : 'false'),'>',esc_html(__($isOnline ? 'Online' : 'Offline')),'</td>',
				'<th scope=row class=name>',esc_html($friendInfo->Name()),'</th>',
				'<td class=see-me-online><input type=checkbox name="see-me-online[',esc_attr($friendInfo->PrincipalID()),']"',($canSeeMeOnline ? ' checked ' : ''),'></td>',
				'<td class=map-me><input type=checkbox name="map-me[',esc_attr($friendInfo->PrincipalID()),']"',($canMapMe ? ' checked ' : ''),'></td>',
				'<td class=edit-my-objects><input type=checkbox name="edit-my-objects[',esc_attr($friendInfo->PrincipalID()),']"',($canEditMyObjects ? ' checked ' : ''),'></td>',
				'<td class=map-them><input disabled type=checkbox name="map-them[',esc_attr($friendInfo->PrincipalID()),']"',($canMapThem ? ' checked ' : ''),'></td>',
				'<td class=edit-their-objects><input disabled type=checkbox name="edit-their-objects[',esc_attr($friendInfo->PrincipalID()),']"',($canEditTheirObjects ? ' checked ' : ''),'></td>',
			'</tr>';
		}
		echo '</tbody>';
		do_action('after_friends_list_editor_tbody', $friendsList);
		echo '</table>';
		do_action('after_friends_list_editor_table', $friendsList);
		echo '</fieldset><fieldset class=buttons>',wp_kses(apply_filters('friends_list_editor_buttons',
			'<button class=submit type=submit>' . esc_html(__('Submit')) . '</button>'
		, $friendsList), array('button'=>array('type'=>array(),'class'=>array()))),'</fieldset>';
		echo '</form>';
		do_action('after_friends_list_editor', $friendsList);
	}

	add_action('friends_list_editor', __NAMESPACE__ . '\friends_list_editor', 10, 2);
}
?>
