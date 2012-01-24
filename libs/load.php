<?php
//!	This is where we load all PHP libs we explicitly need.
namespace{
	require_once('phar://../libs/backpress-full.phar.gz/includes/functions.plugin-api.php'); // we need this for the is_email() function.
	require_once('phar://../libs/backpress-full.phar.gz/includes/functions.formatting.php'); // we need this for the is_email() function.
	require_once('phar://../libs/backpress-full.phar.gz/includes/functions.bp-options.php'); // we need this for the esc_attr() function.
	require_once('phar://../libs/backpress-full.phar.gz/includes/functions.kses.php'); // we need this for the wp_kses() function.
	require_once('phar://../libs/backpress-full.phar.gz/includes/pomo/mo.php'); // we need this for the esc_attr() function.
	require_once('recaptcha/recaptchalib.php');
	require_once('phar://../libs/Aurora-Sim.php.phar.gz/Aurora/load.php');

	use Aurora\Addon\WebUI;

//!	interface to the pomo library in backpress
	class pomo{

//!	array stores instances ot Translations
		protected $translations = array();

//!	object stores an instance of Translations
		protected $emptyTranslation;

//!	we're hiding this behind a singleton method
//!	@see Aurora::Addon::WebUI::pomo::$emptyTranslation
		protected function __construct(){
			$this->emptyTranslation = new Translations;
		}

//!	singleton method
//!	@return object an instance of Aurora::Addon::WebUI::pomo
		public static function i(){
			static $instance;
			if(isset($instance) === false){
				$instance = new static();
			}
			return $instance;
		}

//!	loads the .mo file
/**
*	@param string $domain text domain
*	@param string $mofile path to .mo file
*/
		public function load_textdomain($domain, $mofile){
			$this->translations[$domain] = $this->load_translations($mofile);
		}

//!	gets the translations from the .mo file
/**
*	@param string $mo_filename path to .mo file
*	@return object an instance of Translations
*/
		protected function load_translations($mo_filename){
			if (is_readable($mo_filename)) {
				$translations = new MO();
				$translations->import_from_file($mo_filename);
			} else {
				$translations = new Translations();
			}
			return $translations;
		}

//!	attempts to get the translations for a specified text domain
/**
*	@param string $domain text domain
*	@return object instance of Translations
*	@see Aurora::Addon::WebUI::pomo::$translations
*	@see Aurora::Addon::WebUI::pomo::$emptyTranslation
*/
		public function get_translations($domain){
			return isset($this->translations[$domain])? $this->translations[$domain] : $this->emptyTranslation;
		}
	}

	pomo::i()->load_textdomain('default', 'languages/en-GB.mo');
	
	function __($text, $domain = 'default') {
		return pomo::i()->get_translations($domain)->translate($text);
	}
}
?>