<?php
/*
Plugin Name: Recaptcha
Plugin URI: https://github.com/SignpostMarv/Aurora-WebUI-GPL
Description: Plugin for utilising the reCAPTCHA API in Aurora WebUI-GPL
Version: 0.1
Author: SignpostMarv
Author URI: https://github.com/SignpostMarv/
*/


namespace Aurora\Addon\WebUI\plugins\recaptcha{

	use Globals;


	function html($section){
		if(in_array($section, array('register')) === false){
			return;
		}
		echo '<fieldset class=recaptcha>', trim(wp_kses(recaptcha_get_html(Globals::i()->recaptchaPublicKey, Globals::i()->recaptchaError, true), array_merge(array(
			'iframe'   => array(
				'src'    => 'https://www.google.com/recaptcha/api/noscript?' . http_build_query(array('k'=>Globals::i()->recaptchaPublicKey)),
				'height' => array(),
				'width'  => array(),
				'frameborder' => array('0')
			),
			'textarea' => array(
				'name'   => array(
					'recaptcha_challenge_field'
				),
				'rows'   => array(
					'3'
				),
				'cols'   => array(
					'40'
				)
			),
			'input'    => array(
				'type'   => array(
					'hidden'
				),
				'name'   => array(
					'recaptcha_response_field'
				),
				'value'  => array(
					'manual_challenge'
				)
			),
			
		), Globals::i()->recaptchaEnableJavaScript === true ? array(
			'noscript' => array(),
			'script'   => array(
				'src'    => array(
					'https://www.google.com/recaptcha/api/challenge?' . http_build_query(array('k'=>Globals::i()->recaptchaPublicKey))
				)
			)
		) : array() ), array('https'))), '</fieldset>',"\n";
	}


	function POST($value, array $post){
		if(Globals::i()->recaptcha === true && $value === true){
			if(isset($post['recaptcha_response_field'], $post['recaptcha_challenge_field'], $post['recaptcha_response_field']) === false){
				add_action('pre_buttons_fieldset', function($section){
					if(in_array($section, array('register')) === false){
						return;
					}
					echo '<p class=error>',esc_html(__('Captcha was not entered')),'</p>';
				});
				return false;
			}else{
				$resp = recaptcha_check_answer (Globals::i()->recaptchaPrivateKey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
				if($resp->is_valid){
					return true;
				}else{
					Globals::i()->recaptchaError = $resp->error;
					return false;
				}
			}
		}
		return $value;
	}

	if(Globals::i()->recaptcha === true){
		add_action('pre_buttons_fieldset', __NAMESPACE__ . '\html');
		add_filter('registration_is_valid', __NAMESPACE__ . '\POST', 10, 2);
	}
}
?>