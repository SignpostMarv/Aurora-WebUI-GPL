<?php
//!	@file libs/Aurora/Addon/WebUI.php
//!	@brief WebUI code
//!	@author SignpostMarv

//	Defining exception classes in the top of the file for purposes of clarity.
namespace Aurora\Addon\WebUI{

	use Aurora\Addon;

//!	This interface exists purely to give client code the ability to detect all WebUI-specific exception classes in one go.
//!	The purpose of this behaviour is that instances of Aurora::Addon::WebUI::Exception will be more or less "safe" for public consumption.
	interface Exception extends Addon\Exception{
	}

//!	WebUI-specific RuntimeException
	class RuntimeException extends Addon\RuntimeException implements Exception{
	}

//!	WebUI-specific InvalidArgumentException
	class InvalidArgumentException extends Addon\InvalidArgumentException implements Exception{
	}

//!	WebUI-specific UnexpectedValueException
	class UnexpectedValueException extends Addon\UnexpectedValueException implements Exception{
	}

//!	WebUI-specific LengthException
	class LengthException extends Addon\LengthException implements Exception{
	}

//!	WebUI-specific BadMethodCallException
	class BadMethodCallException extends Addon\BadMethodCallException implements Exception{
	}
}

//!	Mimicking the layout of code in Aurora Sim here.
namespace Aurora\Addon{

	use DateTime;

	use Globals;

	use OpenMetaverse\Vector3;

	use Aurora\Framework\RegionFlags;
	use Aurora\Services\Interfaces\User;

//!	Now you might think this class should be a singleton loading config values from constants instead of a registry method, but Marv has plans. MUAHAHAHAHA.
	class WebUI extends abstractAPI{
//!	string Regular expression for validating UUIDs (put here until this operation gets performed elsewhere.
		const regex_UUID = '/^[a-fA-F0-9]{8}\-[a-fA-F0-9]{4}\-[a-fA-F0-9]{4}\-[a-fA-F0-9]{4}\-[a-fA-F0-9]{12}$/';

//!	This is protected because we're going to use a registry method to access it.
/**
*	The WIREDUX_PASSWORD constant was never used without being passed as an md5() hash, so we immediately do this on instantiation.
*	@param string $serviceURL WebUI API end point.
*	@param string $password WebUI API password
*/
		protected function __construct($serviceURL, $password){
			if(is_string($serviceURL) === false){
				throw new InvalidArgumentException('WebUI API end point must be a string');
			}else if(strpos($serviceURL, 'http://') === false && strpos($serviceURL, 'https://') === false){ // for now, we're not doing any paranoid regex-based validation.
				throw new InvalidArgumentException('WebUI API end point must begin with http:// or https://');
			}else if(is_string($password) === false){
				throw new InvalidArgumentException('WebUI API password should be a string');
			}
			$this->serviceURL = $serviceURL;
			$this->password   = md5($password); // I suppose we could be extremely paranoid and unset $password after this if we really wanted.
		}

//!	string WebUI API end point.
		protected $serviceURL;

//!	string WebUI API password
		protected $password;

//!	registry method. Sets & gets instances of Aurora::Addon::WebUI
/**
*	@param string $serviceURL
*	@param mixed $password should be NULL if getting, otherwise should be string. defaults to NULL.
*	@return Aurora::Addon::WebUI
*	@see Aurora::Addon::WebUI::__construct()
*/
		public static function r($serviceURL, $password=null){
			static $registry = array();
			if(isset($registry[$serviceURL]) === false){
				if(isset($password) === false){
					throw new InvalidArgumentException('Cannot create an instance of WebUI API interface without a password');
				}
				$instance = new static($serviceURL, $password); // we're assigning it to a local variable as a lazy means of avoiding doing valid type checks for array keys. any errors that would crop up about that would trigger InvalidArgumentException in Aurora::Addon::WebUI::__construct()
				$registry[$serviceURL] = $instance; // any child implementation of Aurora::Addon::WebUI that breaks this laziness is on their own at this point.
			}
			return $registry[$serviceURL];
		}

//!	makes a call to the WebUI API end point running on an instance of Aurora.
/**
*	@param string $method
*	@param array $arguments being lazy and future-proofing API methods that have no arguments.
*	@return mixed All instances of do_post_request() in Aurora-WebUI that act upon the result call json_decode() on the $result prior to acting on it, so we save ourselves some time and execute json_decode() here.
*/
		protected function makeCallToAPI($method, array $arguments=null, array $expectedResponse){
			if(is_string($method) === false || ctype_graph($method) === false){
				throw new InvalidArgumentException('API method parameter was invalid.');
			}
			$arguments = isset($arguments) ? $arguments : array();
			$arguments = array_merge(array(
				'Method'      => $method,
				'WebPassword' => $this->password
			), $arguments);
			$ch = curl_init($this->serviceURL);
			curl_setopt_array($ch, array(
				CURLOPT_HEADER         => false,
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS     => implode(',', array(json_encode($arguments)))
			));
			$result = curl_exec($ch);
			curl_close($ch);
			if(is_string($result) === true){
				$result = json_decode($result);
				if(is_object($result) === false){
					throw new UnexpectedValueException('API result expected to be object, ' . gettype($result) . ' found.');
				}
				$exprsp = 0;
				foreach($expectedResponse as $k=>$v){
					++$exprsp;
					if(property_exists($result, $k) === false){
						throw new UnexpectedValueException('Call to API was successful, but required response properties were missing.', $exprsp * 6);
					}else if(in_array(gettype($result->{$k}), array_keys($v)) === false){
						throw new UnexpectedValueException('Call to API was successful, but required response property was of unexpected type.', ($exprsp * 6) + 1);
					}else if(count($v[gettype($result->{$k})]) > 0){
						$validValue = false;
						foreach($v[gettype($result->{$k})] as $_k => $possibleValue){
							if(is_integer($_k) === true){
								if(gettype($result->{$k}) === 'boolean'){
									if(is_bool($possibleValue) === false){
										throw new InvalidArgumentException('Only booleans can be given as valid values to a boolean type.');
									}else if($result->{$k} === $possibleValue){
										$validValue = true;
										break;
									}
								}else{
									$subPropertyKeys = array_keys($possibleValue);
									switch(gettype($result->{$k})){
										case 'array':
											foreach($result->{$k} as $_v){
												if(in_array(gettype($_v), $subPropertyKeys) === false){
													throw new UnexpectedValueException('Call to API was successful, but required response sub-property was of unexpected type.', ($exprsp * 6) + 3);
												}else if(gettype($_v) === 'object' && isset($possibleValue[gettype($_v)]) === true){
													foreach($possibleValue[gettype($_v)] as $__k => $__v){
														if(isset($__v['float']) == true){
															$possibleValue[gettype($_v)]['double'] = $__v['float'];
														}
													}
													$pos = $possibleValue[gettype($_v)];
													if(gettype($_v) === 'object'){
														$pos = current($pos);
														if($pos !== false){
															foreach($pos as $__k => $__v){
																if(isset($__v['float']) === true){
																	$pos[$__k]['double'] = $__v['float'];
																}
															}
														}
													}
													if($pos !== false){
														foreach($pos as $__k => $__v){
															if(isset($_v->{$__k}) === false){
																throw new UnexpectedValueException('Call to API was successful, but required response sub-property property was of missing.', ($exprsp * 6) + 4);
															}else{
																if(in_array(gettype($_v->{$__k}), array_keys($__v)) === false){
																	throw new UnexpectedValueException('Call to API was successful, but required response sub-property was of unexpected type.', ($exprsp * 6) + 5);
																}
															}
														}
													}
												}
											}
											$validValue = true;
										break;
										case 'object':
											foreach($possibleValue as $_k => $_v){
												if(isset($_v['float']) === true){
													$possibleValue[$_k]['double'] = $_v['float'];
												}
											}
											foreach($possibleValue as $_k => $_v){
												if(isset($result->{$k}->{$_k}) === false){
													throw new UnexpectedValueException('Call to API was successful, but required response sub-property property was of missing.', ($exprsp * 6) + 4);
												}else{
													if(in_array(gettype($result->{$k}->{$_k}), array_keys($possibleValue[$_k])) === false){
														throw new UnexpectedValueException('Call to API was successful, but required response sub-property was of unexpected type.', ($exprsp * 6) + 5);
													}
												}
											}
											$validValue = true;
										break;
									}
								}
							}else if($result->{$k} === $possibleValue){
								$validValue = true;
								break;
							}
						}
						if($validValue === false){
							throw new UnexpectedValueException('Call to API was successful, but required response property had an unexpected value.', ($exprsp * 6) + 2);
						}
					}
				}
				return $result;
			}
			throw new RuntimeException('API call failed to execute.'); // if this starts happening frequently, we'll add in some more debugging code.
		}

//!	Returns the URI for a grid texture
/**
*	@param string $uuid texture UUID
*	@return string full URL to texture
*/
		public function GridTexture($uuid){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('Texture UUID should be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('Texture UUID was invalid.');
			}

			return $this->get_grid_info('WebUIHandlerTextureServer') . '/index.php?' . http_build_query(array( 'method'=>'GridTexture', 'uuid'=>$uuid));
		}

//!	Returns the size of the specified texture
/**
*	WebUI has a call for this so we don't have to spend bandwidth on curling the texture.
*	@param string $uuid texture UUID
*	@return integer size of texture
*/
		public function GridTextureSize($uuid){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('Texture UUID should be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('Texture UUID was invalid.');
			}

			return $this->makeCallToAPI('SizeOfHTTPGetTextureImage', array(
				'Texture' => $uuid
			), array(
				'Size' => array('integer'=>array())
			))->Size;
		}

//!	Returns the URI for a region texture
/**
*	@param object $region instance of Aurora::Addon::WebUI::GridRegion
*	@param integer $zoomLevel map tile zoom level
*	@return string full URL to texture
*/
		public function MapTexture(WebUI\GridRegion $region){
			return $region->serverURI() . '/index.php?' . http_build_query(array( 'method'=>'regionImage' . str_replace('-','', $region->RegionID())));
		}

//!	Determines whether the specified username exists in the AuroraSim database.
/**
*	@param string $name the username we want to check exists
*	@return boolean TRUE if the user exists, FALSE otherwise.
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*/
		public function CheckIfUserExists($name){
			if(is_string($name) === false){
				throw new InvalidArgumentException('Name should be a string');
			}
			return $this->makeCallToAPI('CheckIfUserExists', array('Name'=>$name), array('Verified'=>array('boolean'=>array())))->Verified;
		}

//!	Attempts to create an account with the specified details.
/**
*	@param string $Name desired account name.
*	@param string $Password plain text password (we hash it before sending)
*	@param string $Email optional, must be valid email address or an empty string if none specified
*	@param string $HomeRegion optional, must be a valid region name or an empty string if none specified
*	@param integer $userLevel
*	@param string $RLDOB User's date of birth.
*	@param string $RLFirstName User's first name.
*	@param string $RLLastName Can be an empty string to support mononyms.
*	@param string $RLAddress Postal address
*	@param string $RLCity City
*	@param string $RLZip Zip/Postal code
*	@param string $RLCountry Country of origin
*	@param string $RLIP IP Address
*	@return object An instance of Aurora::Addon::WebUI::GridUserInfo corresponding to the UUID returned by the API end point.
*/
		public function CreateAccount($Name, $Password, $Email='', $HomeRegion='', $userLevel=0, $RLDOB='1970-01-01', $RLFirstName='', $RLLastName='', $RLAddress='', $RLCity='', $RLZip='', $RLCountry='', $RLIP=''){
			if(is_string($userLevel) === true && ctype_digit($userLevel) === true){
				$userLevel = (integer)$userLevel;
			}

			if(is_string($Name) === false){
				throw new InvalidArgumentException('Username must be a string.');
			}else if($this->CheckIfUserExists($Name) === true){
				throw new InvalidArgumentException('That username has already been taken.');
			}else if(is_string($Password) === false){
				throw new InvalidArgumentException('Password must be a string.');
			}else if(strlen($Password) < 8){
				throw new LengthException('Password cannot be less than 8 characters long.');
			}else if(is_string($Email) === false){
				throw new InvalidArgumentException('Email address must be a string.');
			}else if($Email !== '' && is_email($Email) === false){
				throw new InvalidArgumentException('Email address was specified but was found to be invalid.');
			}else if(is_string($HomeRegion) === false){
				throw new InvalidArgumentException('Home Region must be a string.');
			}else if(is_integer($userLevel) === false){
				throw new InvalidArgumentException('User Level must be an integer.');
			}else if($userLevel < -1){
				throw new InvalidArgumentException('User Level must be greater than or equal to minus one.');
			}else if(is_string($RLDOB) === false){
				throw new InvalidArgumentException('User Date of Birth must be a string.');
			}else if(strtotime($RLDOB) === false){
				throw new InvalidArgumentException('User Date of Birth was not valid.');
			}else if(is_string($RLIP) === false){
				throw new InvalidArgumentException('User RL IP Address must be a string.');
			}else if(Globals::i()->registrationPostalRequired === true){
				if(is_string($RLFirstName) === false){
					throw new InvalidArgumentException('User RL First name must be a string.');
				}else if(trim($RLFirstName) === ''){
					throw new InvalidArgumentException('User RL First name must not be an empty string.');
				}else if(is_string($RLLastName) === false){
					throw new InvalidArgumentException('User RL Last name must be a string.');
				}else if(is_string($RLAddress) === false){
					throw new InvalidArgumentException('User RL Address must be a string.');
				}else if(is_string($RLCity) === false){
					throw new InvalidArgumentException('User RL City must be a string.');
				}else if(is_string($RLZip) === false){
					throw new InvalidArgumentException('User RL Zip code must be a string.');
				}else if(is_string($RLCountry) === false){
					throw new InvalidArgumentException('User RL Country must be a string.');
				}
			}

			$Name        = trim($Name);
			$Password    = '$1$' . md5($Password);
			$RLDOB      = date('Y-m-d', strtotime($RLDOB));
			$RLFirstName = trim($RLFirstName);
			$RLLastName  = trim($RLLastName);
			$RLAddress   = trim($RLAddress);
			$RLCity      = trim($RLCity);
			$RLZip       = trim($RLZip);
			$RLCountry   = trim($RLCountry);
			$RLIP        = trim($RLIP);

			$expectedResponse = array(
				'UUID' => array('string' => array())
			);
			if(Globals::i()->registrationActivationRequired === true){
				$expectedResponse['WebUIActivationToken'] = array('string' => array());
			}

			$result = $this->makeCallToAPI('CreateAccount', array(
				'Name'               => $Name,
				'PasswordHash'       => $Password,
				'Email'              => $Email,
				'RLDOB'              => $RLDOB,
				'RLFirstName'        => $RLFirstName,
				'RLLastName'         => $RLLastName,
				'RLAddress'          => $RLAddress,
				'RLCity'             => $RLCity,
				'RLZip'              => $RLZip,
				'RLCountry'          => $RLCountry,
				'RLIP'               => $RLIP,
				'ActivationRequired' => (Globals::i()->registrationActivationRequired === true)
			),	$expectedResponse);

			$ActivationToken = null;
			if(preg_match(self::regex_UUID, $result->UUID) === false){
				throw new UnexpectedValueException('Call to API was successful, but UUID response was not a valid UUID.');
			}else if($result->UUID === '00000000-0000-0000-0000-000000000000'){
				throw new UnexpectedValueException('Call to API was successful but registration failed.');
			}else if(Globals::i()->registrationActivationRequired === true){
				if(preg_match(self::regex_UUID, $result->WebUIActivationToken) !== 1){
					throw new UnexpectedValueException('Call to API was successful, but activation token was not a valid UUID.');
				}else{
					$ActivationToken = $result->WebUIActivationToken;
				}
			}
			return array($this->GetGridUserInfo($result->UUID), $ActivationToken);
		}

//!	Since admin login and normal login have the same response, we're going to use the same code for both here.
/**
*	@param string $username
*	@param string $password
*	@param boolean $asAdmin TRUE if attempt to login as admin, FALSE otherwise. defaults to FALSE.
*	@return object instance of Aurora::Addon::WebUI::genUser
*/
		private function doLogin($username, $password, $asAdmin=false){
			if(is_string($username) === false){
				throw new InvalidArgumentException('Username must be string.');
			}else if(trim($username) === ''){
				throw new InvalidArgumentException('Username was an empty string');
			}else if(is_string($password) === false){
				throw new InvalidArgumentException('Password must be a string');
			}else if(trim($password) === ''){
				throw new InvalidArgumentException('Password was an empty string');
			}
			$password = '$1$' . md5($password); // this is required so we don't have to transmit the plaintext password.
			$result = $this->makeCallToAPI($asAdmin ? 'AdminLogin' : 'Login', array('Name' => $username, 'Password' => $password), array(
				'Verified'  => array('boolean'=>array())
			));
			if($result->Verified === false){
				throw new InvalidArgumentException('Credentials incorrect');
			}else if(isset($result->UUID, $result->FirstName, $result->LastName) === false){
				throw new UnexpectedValueException('API call was made, credentials were correct but required response properties were missing');
			}
			return WebUI\genUser::r($result->UUID, $result->FirstName, $result->LastName); // we're leaving validation up to the genUser class.
		}

//!	Attempts a login as a normal user.
/**
*	@param string $username
*	@param string $password
*	@return object instance of Aurora::Addon::WebUI::genUser
*/
		public function Login($username, $password){
			return $this->doLogin($username, $password);
		}

//!	Attempts to login as an admin user.
/**
*	@param string $username
*	@param string $password
*	@return object instance of Aurora::Addon::WebUI::genUser
*/
		public function AdminLogin($username, $password){
			return $this->doLogin($username, $password, true);
		}

//!	Determines the online status of the grid and whether logins are enabled.
/**
*	@return Aurora::Addon::WebUI::OnlineStatus
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*/
		public function OnlineStatus(){
			$result = $this->makeCallToAPI('OnlineStatus', null, array(
				'Online'       => array('boolean'=>array()),
				'LoginEnabled' => array('boolean'=>array())
			));
			return new WebUI\OnlineStatus($result->Online, $result->LoginEnabled);
		}

//!	Determines whether or not the account has been authenticated/verified.
/**
*	@param mixed $uuid either a string UUID of the user we wish to check, or an instance of Aurora::Services::Interfaces::User
*	@param boolean TRUE if the account has been authenticated/verified, FALSE otherwise.
*/
		public function Authenticated($uuid){
			if($uuid instanceof User){
				$uuid = $uuid->PrincipalID();
			}
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID should be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('UUID supplied was not a valid UUID.');
			}
			$result = $this->makeCallToAPI('Authenticated', array('UUID'=>$uuid), array(
				'Verified' => array('boolean'=>array())
			));
			return $result->Verified;
		}

//!	Attempts to activate the account via an activation token.
/**
*	@param string $username Account username
*	@param string $password Account password
*	@param string $token Activation token
*/
		public function ActivateAccount($username, $password, $token){
			if(is_string($username) === false){
				throw new InvalidArgumentException('Username must be a string.');
			}else if($this->CheckIfUserExists($username) === false){
				throw new InvalidArgumentException('Cannot activate a non-existant account.');
			}else if(is_string($password) === false){
				throw new InvalidArgumentException('Password must be a string.');
			}else if(is_string($token) === false){
				throw new InvalidArgumentException('Token must be a string.');
			}else if(preg_match(self::regex_UUID, $token) !== 1){
				throw new InvalidArgumentException('Token must be a valid UUID.');
			}
			$password = '$1$' . md5($password);

			$result = $this->makeCallToAPI('ActivateAccount', array('UserName' => $username, 'PasswordHash' => $password, 'ActivationToken' => $token), array(
				'Verified' => array('boolean'=>array())
			));

			return $result->Verified;
		}

//!	Get the GetGridUserInfo for the specified user.
/**
*	@param mixed $uuid either a string UUID of the user we wish to check, or an instance of Aurora::Services::Interfaces::User
*	@return object Aurora::Addon::WebUI::GridUserInfo
*/
		public function GetGridUserInfo($uuid){
			if($uuid instanceof User){
				$uuid = $uuid->PrincipalID();
			}
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID should be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('UUID supplied was not a valid UUID.');
			}
			$result = $this->makeCallToAPI('GetGridUserInfo', array('UUID'=>$uuid), array(
				'UUID'      => array('string' =>array()),
				'HomeUUID'  => array('string' =>array()),
				'HomeName'  => array('string' =>array()),
				'Online'    => array('boolean'=>array()),
				'Email'     => array('string' =>array()),
				'Name'      => array('string' =>array()),
				'FirstName' => array('string' =>array()),
				'LastName'  => array('string' =>array())
			));
			// this is where we get lazy and leave validation up to the GridUserInfo class.
			return	WebUI\GridUserInfo::r($result->UUID, $result->Name, $result->HomeUUID, $result->HomeName, $result->Online, $result->Email);
		}

//!	Save email address, set user level to zero.
/**
*	@param mixed $uuid either a string UUID or an instance of Aurora::Services::Interfaces::User of the user we wish to save the email address for.
*	@param string $email email address.
*	@return boolean TRUE if successful, FALSE otherwise.
*/
		public function SaveEmail($uuid, $email){
			if($uuid instanceof User){
				$uuid = $uuid->PrincipalID();
			}

			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(self::regex_UUID, $uuid)!== 1){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}else if(is_string($email) === false){
				throw new InvalidArgumentException('Email address must be a string.');
			}else if(is_email($email) === false){
				throw new InvalidArgumentException('Email address was not valid.');
			}

			$result = $this->makeCallToAPI('SaveEmail', array('UUID' => $uuid, 'Email' => $email), array(
				'Verified' => array('boolean'=>array())
			));

			return $result->Verified;
		}

//!	Change account name.
/**
*	@param mixed $uuid either a string UUID or an instance of Aurora::Services::Interfaces::User of the user we wish to change the name for.
*	@param string $name new name
*/
		public function ChangeName($uuid, $name){
			if($uuid instanceof User){
				$uuid = $uuid->PrincipalID();
			}
			if(is_string($name) === true){
				$name = trim($name);
			}

			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(self::regex_UUID, $uuid)!== 1){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}else if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be a string.');
			}else if($name === ''){
				throw new InvalidArgumentException('Name cannot be an empty string.');
			}

			if($this->GetGridUserInfo($uuid)->Name() === $name){ // if the name is already the same, we're not going to bother making the call.
				return true;
			}

			$result = $this->makeCallToAPI('ChangeName', array('UUID' => $uuid, 'Name' => $name), array(
				'Verified' => array('boolean'=>array()),
				'Stored'   => array('boolean'=>array())
			));
			if($result->Verified === true && $result->Stored === false){
				throw new RuntimeException('Call to API was successful, but name change was not stored by the server.');
			}

			return $result->Verified;
		}

//!	Change password. NOTE: currently requires passwords to be sent to WebUI as plaintext.
/**
*	@param mixed $uuid either a string UUID or an instance of Aurora::Services::Interfaces::User of the user we wish to change the name for.
*	@param mixed $oldPassword old password
*	@param mixed $newPassword new password
*/
		public function ChangePassword($uuid, $oldPassword, $newPassword){
			if($uuid instanceof User){
				$uuid = $uuid->PrincipalID();
			}

			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}else if(is_string($oldPassword) === false){
				throw new InvalidArgumentException('Old password must be a string.');
			}else if(is_string($newPassword) === false){
				throw new InvalidArgumentException('New password must be a string.');
			}else if(trim($newPassword) === ''){
				throw new InvalidArgumentException('New password cannot be an empty string.');
			}else if(strlen($newPassword) < 8){
				throw new LengthException('New password cannot be less than 8 characters long.');
			}

			$result = $this->makeCallToAPI('ChangePassword', array('UUID' => $uuid, 'Password' => $oldPassword, 'NewPassword' => $newPassword), array(
				'Verified' => array('boolean'=>array())
			));

			return $result->Verified;
		}

//!	Confirm the account name and email address (used by forgotten password activities)
/**
*	@param string $name Account name
*	@param string $email Account email address
*	@return boolean TRUE if successful, FALSE otherwise.
*/
		public function ConfirmUserEmailName($name, $email){
			if(is_string($name) === true){
				$name = trim($name);
			}

			if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be a string.');
			}else if($name === ''){
				throw new InvalidArgumentException('Name cannot be an empty string.');
			}else if(is_string($email) === false){
				throw new InvalidArgumentException('Email address must be a string.');
			}else if(is_email($email) === false){
				throw new InvalidArgumentException('Email address is invalid.');
			}

			$result = $this->makeCallToAPI('ConfirmUserEmailName', array('Name' => $name, 'Email' => $email), array(
				'Verified'=>array('boolean'=>array())
			));
			if(isset($result->ErrorCode) === true && is_integer($result->ErrorCode) === false){
				throw new UnexpectedValueException('Call to API was successful but required response property was of unexpected type.',2);
			}else if(isset($result->ErrorCode) === true){
				if($result->ErrorCode === 1){
					throw new InvalidArgumentException('No account was found with the specified name.');
				}else if($result->ErrorCode === 2){
					throw new InvalidArgumentException('The specified account is disabled.');
				}else if($result->ErrorCode === 3){
					throw new InvalidArgumentException('The specified email address does not match the email address associated with the specified account.');
				}else{
					throw new UnexpectedValueException('Unknown error occurred when checking email address of specified account.');
				}
			}

			return $result->Verified;
		}

//!	Attempt to get the profile object for the specified user.
/**
*	If $name is an instance of Aurora::Addon::WebUI::abstractUser, $uuid will be set to Aurora::Addon::WebUI::abstractUser::PrincipalID() and $name will be set to an empty string.
*	@param mixed $name Either a string of the account name, or an instance of Aurora::Addon::WebUI::abstractUser
*	@param string $uuid Account UUID
*	@return object instance of Aurora::Addon::WebUI::UserProfile
*/
		public function GetProfile($name='', $uuid='00000000-0000-0000-0000-000000000000'){
			if($name instanceof WebUI\abstractUser){
				$uuid = $name->PrincipalID();
				$name = '';
			}
			if(is_string($name) === false){
				throw new InvalidArgumentException('Account name must be a string.');
			}else if(is_string($uuid) === false){
				throw new InvalidArgumentException('Account UUID must be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('Account UUID was not a valid UUID.');
			}

			$result = $this->makeCallToAPI('GetProfile', array('Name' => $name, 'UUID' => $uuid), array(
				'account'=> array('object'=>array(array(
					'Created' => array('integer'=>array()),
					'Name' => array('string'=>array()),
					'PrincipalID' => array('string'=>array()),
					'Email' => array('string'=>array()),
					'TimeSinceCreated' => array('string'=>array())
				)))
			));

			$account = $result->account;

			$allowPublish = $maturePublish  = $visible     = false;
			$wantToMask   = $canDoMask      = 0;
			$wantToText   = $canDoText      = $languages   = $aboutText = $firstLifeAboutText = $webURL = $displayName = $customType = '';
			$image        = $firstLifeImage = '00000000-0000-0000-0000-000000000000';
			$notes        = json_encode('');
			if(isset($result->profile) === true){
				$profile = $result->profile;
				if(isset(
					$profile->AllowPublish, $profile->MaturePublish, $profile->Visible,
					$profile->WantToMask, $profile->CanDoMask,
					$profile->WantToText, $profile->CanDoText, $profile->Languages, $profile->AboutText, $profile->FirstLifeAboutText, $profile->WebURL, $profile->DisplayName, $profile->CustomType,
					$profile->Image, $profile->FirstLifeImage,
					$profile->Notes
				) === false){
					throw new UnexpectedValueException('Call to API was successful, but optional response properties were missing.');
				}

				$allowPublish       = $profile->AllowPublish;
				$maturePublish      = $profile->MaturePublish;
				$visible            = $profile->Visible;
				$wantToMask         = $profile->WantToMask;
				$canDoMask          = $profile->CanDoMask;
				$wantToText         = $profile->WantToText;
				$canDoText          = $profile->CanDoText;
				$languages          = $profile->Languages;
				$aboutText          = $profile->AboutText;
				$firstLifeAboutText = $profile->FirstLifeAboutText;
				$webURL             = $profile->WebURL;
				$displayName        = $profile->DisplayName;
				$customType         = $profile->CustomType;
				$image              = $profile->Image;
				$firstLifeImage     = $profile->FirstLifeImage;
				$notes              = $profile->Notes;
			}

			$RLName = $RLAddress = $RLZip = $RLCity = $RLCountry = null;
			if(isset($result->agent) === true){
				$agent = $result->agent;
				$properties = array(
					'RLName',
					'RLAddress',
					'RLZip',
					'RLCity',
					'RLCountry'
				);
				foreach($properties as $v){
					if(property_exists($result->agent, $v) === false){
						throw new UnexpectedValueException('Call to API was successful, but optional response properties were missing.');
					}
				}

				$RLName    = $agent->RLName;
				$RLAddress = $agent->RLAddress;
				$RLZip     = $agent->RLZip;
				$RLCity    = $agent->RLCity;
				$RLCountry = $agent->RLCountry;
			}

			return WebUI\UserProfile::r($account->PrincipalID, $account->Name, $account->Email, $account->Created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, isset($account->PartnerUUID) ? $account->PartnerUUID : '00000000-0000-0000-0000-000000000000', $visible, $customType, $notes, $RLName, $RLAddress, $RLZip, $RLCity, $RLCountry);
		}

//!	Attempt to edit the account name, email address and real-life info.
/**
*	If $uuid is an instance of Aurora::Addon::WebUI::abstractUser, $name is set to Aurora::Addon::WebUI::abstractUser::Name() and $uuid is set to Aurora::Addon::WebUI::abstractUser::PrincipalID()
*	@param mixed $uuid either the account ID or an instance of Aurora::Addon::WebUI::abstractUser
*	@param mixed either a string of the account name or NULL when $uuid is an instance of Aurora::Addon::WebUI::abstractUser
*	@param string $email Email address for the account
*	@param mixed either an instance of Aurora::Addon::WebUI::RLInfo or NULL
*	@return boolean TRUE if successful, FALSE otherwise. Also returns FALSE if the operation was partially successful.
*/
		public function EditUser($uuid, $name=null, $email='', WebUI\RLInfo $RLInfo=null){
			if($uuid instanceof WebUI\abstractUser){
				if(is_null($name) === true){
					$name = $uuid->Name();
				}
				$uuid = $uuid->PrincipalID();
			}
			if(is_string($name)){
				$name = trim($name);
			}
			if(is_string($email)){
				$email = trim($email);
			}

			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}else if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be a string.');
			}else if($name === ''){
				throw new InvalidArgumentException('Account name cannot be an empty string.');
			}else if(is_string($email) === false){
				throw new InvalidArgumentException('Email address must be a string.');
			}else if($email !== '' && is_email($email) === false){
				throw new InvalidArgumentException('Email address was not valid.');
			}

			$data = array(
				'UserID' => $uuid,
				'Name'   => $name,
				'Email'  => $email
			);
			if($RLInfo instanceof WebUI\RLInfo){
				foreach($RLInfo as $k=>$v){
					$data[$k] = $v;
				}
			}

			$result = $this->makeCallToAPI('EditUser', $data, array(
				'agent'   => array('boolean'=>array()),
				'account' => array('boolean'=>array())
			));

			return ($result->agent && $result->account);
		}

//!	Attempt to fetch the public avatar archives.
/**
*	@return an instance of Aurora::Addon::WebUI::AvatarArchives corresponding to the result returned by the API end point.
*/
		public function GetAvatarArchives(){
			$result = $this->makeCallToAPI('GetAvatarArchives', null, array(
				'names'    => array('array'=>array()),
				'snapshot' => array('array'=>array())
			));

			if(count($result->names) !== count($result->snapshot)){
				throw new LengthException('Call to API was successful, but the number of names did not match the number of snapshots');
			}

			$archives = array();
			foreach($result->names as $k=>$v){
				$archives[] = new WebUI\basicAvatarArchive($v, $result->snapshot[$k]);
			}

			return new WebUI\AvatarArchives($archives);
		}

//!	Attempt to delete the user
/**
*	If $uuid is an instance of Aurora::Addon::WebUI::abstractUser, $uuid is set to Aurora::Addon::WebUI::abstractUser::PrincipalID()
*	@param mixed $uuid Either an account UUID, or an instance of Aurora::Addon::WebUI::abstractUser
*	@return boolean Should always return TRUE
*/
		public function DeleteUser($uuid){
			if($uuid instanceof WebUI\abstractUser){
				$uuid = $uuid->PrincipalID();
			}
			if(is_string($uuid) === true){
				$uuid = trim($uuid);
			}

			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}

			$result = $this->makeCallToAPI('DeleteUser', array('UserID' => $uuid), array(
				'Finished' => array('boolean'=>array())
			));

			return $result->Finished;
		}

//!	Attempts to ban a user permanently or temporarily
/**
*	If $uuid is an instance of Aurora::Addon::WebUI::abstractUser, $uuid is set to Aurora::Addon::WebUI::abstractUser::PrincipalID()
*	If $until is an instance of DateTime, $until is set to DateTime::format('c')
*	@param mixed $uuid Either an account UUID, or an instance of Aurora::Addon::WebUI::abstractUser
*	@param mixed $until Either NULL (in which case it's a permanent ban) or if a temporary ban should be an instance of DateTime or a date string.
*	@return boolean
*/
		public function BanUser($uuid, $until=null){
			if($uuid instanceof WebUI\abstractUser){
				$uuid = $uuid->PrincipalID();
			}
			if($until instanceof DateTime){
				$until = $until->format('c');
			}

			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}else if(isset($until) === true){
				if(is_string($until) === false){
					throw new InvalidArgumentException('temporary ban date must be a string.');
				}else if(strtotime($until) === false){
					throw new InvalidArgumentException('temporary ban date must be a valid date.');
				}
			}

			$result = $this->makeCallToAPI(isset($until) ? 'TempBanUser' : 'BanUser', array('UserID' => $uuid, 'BannedUntil' => $until), array(
				'Finished' => array('boolean'=>array())
			));

			return $result->Finished;
		}

//!	Attempts to temporarily ban a user.
/**
*	This method is only here for completeness, in practice Aurora::Addon::WebUI::BanUser() should be called with $until specified
*	If $uuid is an instance of Aurora::Addon::WebUI::abstractUser, $uuid is set to Aurora::Addon::WebUI::abstractUser::PrincipalID()
*	If $until is an instance of DateTime, $until is set to DateTime::format('c')
*	@param mixed $uuid Either an account UUID, or an instance of Aurora::Addon::WebUI::abstractUser
*	@param mixed $until should be an instance of DateTime or a date string.
*	@return boolean
*/
		public function TempBanUser($uuid, $until){
			if(isset($until) === false){
				throw new InvalidArgumentException('Temporary ban time must be specified.');
			}

			return $this->BanUser($uuid, $until);
		}

//!	Attempts to unban a user.
/**
*	If $uuid is an instance of Aurora::Addon::WebUI::abstractUser, $uuid is set to Aurora::Addon::WebUI::abstractUser::PrincipalID()
*	@param mixed $uuid Either an account UUID, or an instance of Aurora::Addon::WebUI::abstractUser
*	@return boolean
*/
		public function UnBanUser($uuid){
			if($uuid instanceof WebUI\abstractUser){
				$uuid = $uuid->PrincipalID();
			}

			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(self::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('UUID must be a valid UUID.');
			}

			$result = $this->makeCallToAPI('UnBanUser', array('UserID' => $uuid), array(
				'Finished' => array('boolean'=>array())
			));

			return $result->Finished;
		}

//!	Attempt to search for users
/**
*	@param mixed $start either an integer start point for results, or $query when we're being lazy.
*	@param integer $end end point for results
*	@param string $query search filter
*	@return object an instance of Aurora::Addon::WebUI::SearchUserResults
*/
		public function FindUsers($start=0, $end=25, $query=''){
			if(is_string($start) === true){
				if(ctype_digit($start) === true){
					$start = (integer)$start;
				}else if($query === ''){
					$query = $start;
					$start = 0;
				}
			}
			if(is_string($end) === true && ctype_digit($end) === true){
				$end = (integer)$end;
			}
			if(is_string($query) === true){
				$query = trim($query);
			}

			if(is_integer($start) === false){
				throw new InvalidArgumentException('Start point must be an integer.');
			}else if(is_integer($end) === false){
				throw new InvalidArgumentException('End point must be an integer.');
			}else if(is_string($query) === false){
				throw new InvalidArgumentException('Query filter must be a string.');
			}

			$result = $this->makeCallToAPI('FindUsers', array('Start'=>$start, 'End'=>$end, 'Query'=>$query), array(
				'Users' => array('array'=>array())
			));

			$results = array();
			foreach($result->Users as $userdata){
				if(isset($userdata->PrincipalID, $userdata->UserName, $userdata->Created, $userdata->UserFlags) === false){
					throw new UnexpectedValueException('Call to API was successful but required response sub-properties were missing.');
				}
				$results[] = WebUI\SearchUser::r($userdata->PrincipalID, $userdata->UserName, $userdata->Created, $userdata->UserFlags);
			}

			return new WebUI\SearchUserResults($results);
		}

//!	Attempt to fetch all Abuse Reports.
/**
*	@param integer $start start point for abuse reports
*	@param integer $count maximum number of abuse reports to retrieve
*	@param boolean $active TRUE to get open abuse reports, FALSE to get closed abuse reports
*/
		public function GetAbuseReports($start=0, $count=25, $active=true){
			if(is_integer($start) === false){
				throw new InvalidArgumentException('Start point must be an integer.');
			}else if(is_integer($count) === false){
				throw new InvalidArgumentException('Maximum abuse report count must be an integer.');
			}else if(is_bool($active) === false){
				throw new InvalidArgumentException('Activity flag must be a boolean.');
			}

			$result = $this->makeCallToAPI('GetAbuseReports', array('Start' => $start, 'Count' => $count, 'Active' => $active), array(
				'AbuseReports' => array('array'=>array())
			));

			$results = array();
			foreach($result->AbuseReports as $AR){
				if(isset($AR->Number, $AR->Details, $AR->Location, $AR->UserName, $AR->Summary, $AR->Active, $AR->AssignedTo, $AR->Category, $AR->Checked, $AR->Notes, $AR->ObjectName, $AR->ObjectPosition, $AR->ObjectUUID, $AR->RegionName, $AR->ReporterName, $AR->Screenshot) === false){
					throw new UnexpectedValueException('Call to API was successful, but required response sub-properties were missing.');
				}
				$results[] = WebUI\AbuseReport::r($AR->Number, $AR->Details, $AR->Location, $AR->UserName, $AR->Summary, $AR->Active, $AR->AssignedTo, $AR->Category, $AR->Checked, $AR->Notes, $AR->ObjectName, $AR->ObjectPosition, $AR->ObjectUUID, $AR->RegionName, $AR->ReporterName, $AR->Screenshot);
			}

			return new WebUI\AbuseReports($results);
		}

//!	Attempts to mark the specified Abuse Report as complete
/**
*	@param mixed $abuseReport Either an integer corresponding to Aurora::Addon::WebUI::AbuseReport::Number() or an instance of Aurora::Addon::WebUI::AbuseReport
*	@return boolean TRUE on success, FALSE on failure (usually because the specified abuse report doesn't exist).
*/
		public function AbuseReportMarkComplete($abuseReport){
			if($abuseReport instanceof WebUI\AbuseReport){
				$abuseReport = $abuseReport->Number();
			}

			if(is_integer($abuseReport) === false){
				throw new InvalidArgumentException('Abuse report number must be specified as an integer.');
			}

			$result = $this->makeCallToAPI('AbuseReportMarkComplete', array('Number' => $abuseReport), array(
				'Finished' => array('boolean'=>array())
			));

			return $result->Finished;
		}

//!	Attempt to update the notes for the specified abuse report
/**
*	@param mixed $abuseReport Either an integer corresponding to Aurora::Addon::WebUI::AbuseReport::Number() or an instance of Aurora::Addon::WebUI::AbuseReport
*	@param string $notes Notes on the abuse report
*	@return boolean TRUE on success, FALSE on failure (usually because the specified abuse report doesn't exist).
*/
		public function AbuseReportSaveNotes($abuseReport, $notes){
			if($abuseReport instanceof WebUI\AbuseReport){
				$abuseReport = $abuseReport->Number();
			}

			if(is_integer($abuseReport) === false){
				throw new InvalidArgumentException('Abuse report number must be specified as an integer.');
			}else if(is_string($notes) === false){
				throw new InvalidArgumentException('Abuse report notes must be specified as a string.');
			}

			$result = $this->makeCallToAPI('AbuseReportSaveNotes', array('Number' => $abuseReport, 'Notes' => trim($notes)), array(
				'Finished' => array('boolean'=>array())
			));

			return $result->Finished;
		}

//!	Attempt to set the WebLoginKey for the specified user
/**
*	@param string $for UUID of the desired user to specify a WebLoginKey for.
*	@return string the WebLoginKey generated by the server.
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*/
		public function SetWebLoginKey($for){
			if(is_string($for) === false){
				throw new InvalidArgumentException('UUID of user must be specified as a string');
			}else if(preg_match(self::regex_UUID, $for) !== 1){
				throw new InvalidArgumentException('Specified string was not a valid UUID');
			}
			$result = $this->makeCallToAPI('SetWebLoginKey', array('PrincipalID'=>$for), array(
				'WebLoginKey' => array('string'=>array())
			));
			if(preg_match(self::regex_UUID, $result->WebLoginKey) !== 1){
				throw new UnexpectedValueException('WebLoginKey value present on API result, but value was not a valid UUID.');
			}
			return $result->WebLoginKey;
		}

//!	Gets the array used as the expected response parameter for Aurora::Addon::WebUI::makeCallToAPI()
/**
*	@return array
*/
		private static function GridRegionValidator(){
			return array('object' => array(array(
				'uuid'                 => array('string'  => array()),
				'locX'                 => array('integer' => array()),
				'locY'                 => array('integer' => array()),
				'locZ'                 => array('integer' => array()),
				'regionName'           => array('string'  => array()),
				'regionType'           => array('string'  => array()),
				'serverIP'             => array('string'  => array()),
				'serverHttpPort'       => array('integer' => array()),
				'serverURI'            => array('string'  => array()),
				'serverPort'           => array('integer' => array()),
				'regionMapTexture'     => array('string'  => array()),
				'regionTerrainTexture' => array('string'  => array()),
				'access'               => array('integer' => array()),
				'owner_uuid'           => array('string'  => array()),
				'AuthToken'            => array('string'  => array()),
				'sizeX'                => array('integer' => array()),
				'sizeY'                => array('integer' => array()),
				'sizeZ'                => array('integer' => array()),
				'LastSeen'             => array('integer' => array()),
				'SessionID'            => array('string'  => array()),
				'Flags'                => array('integer' => array()),
				'GenericMap'           => array('object'  => array()),
				'EstateOwner'          => array('string'  => array()),
				'EstateID'             => array('integer' => array()),
				'remoteEndPointIP'     => array('array'   => array()),
				'remoteEndPointPort'   => array('integer' => array()),
			)));
		}

//!	Get a single region
/**
*	@param string $region either a UUID or a region name.
*	@return object instance of Aurora::Addon::WebUI::GridRegion
*/
		public function GetRegion($region, $scopeID='00000000-0000-0000-0000-000000000000'){
			if(is_string($region) === false){
				throw new InvalidArgumentException('Region must be specified as a string.');
			}else if(trim($region) === ''){
				throw new InvalidArgumentException('Region must not be an empty string.');
			}else if(is_string($scopeID) === false){
				throw new InvalidArgumentException('ScopeID must be specified as a string.');
			}else if(preg_match(self::regex_UUID, $scopeID) != 1){
				throw new InvalidArgumentException('ScopeID must be a valid UUID.');
			}

			$input = array(
				'ScopeID' => $scopeID
			);

			if(preg_match(self::regex_UUID, $region) != 1){
				$input['Region'] = trim($region);
			}else{
				$input['RegionID'] = $region;
			}

			return WebUI\GridRegion::fromEndPointResult($this->makeCallToAPI('GetRegion', $input, array(
				'Region' => static::GridRegionValidator()
			))->Region);
		}

//!	Get a list of regions in the AuroraSim install that match the specified flags.
/**
*	@param integer $flags A bitfield corresponding to constants in Aurora::Framework::RegionFlags
*	@param integer $start start point. If Aurora::Addon::WebUI::GetRegions is primed, then Aurora::Addon::WebUI::GetRegions::r() will auto-seek to start.
*	@param mixed $count Either an integer for the maximum number of regions to fetch from the API end point in a single batch, or NULL to use the end point's default value.
*	@param boolean $asArray
*	@return mixed If $asArray is TRUE returns an array, otherwise returns an instance of Aurora::Addon::WebUI::GetRegions
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*	@see Aurora::Addon::WebUI::fromEndPointResult()
*	@see Aurora::Addon::WebUI::GetRegions::r()
*/
		public function GetRegions($flags=null, $start=0, $count=null, $sortRegionName=null, $sortLocX=null, $sortLocY=null, $asArray=false){
			if(isset($flags) === false){
				$flags = RegionFlags::RegionOnline;
			}
			if(is_bool($asArray) === false){
				throw new InvalidArgumentException('asArray flag must be a boolean.');
			}else if(is_integer($flags) === false){
				throw new InvalidArgumentException('RegionFlags argument should be supplied as integer.');
			}else if($flags < 0){
				throw new InvalidArgumentException('RegionFlags cannot be less than zero');
			}else if(RegionFlags::isValid($flags) === false){ // Aurora::Framework::RegionFlags::isValid() does do a check for integerness, but we want to throw a different exception message if it is an integer.
				throw new InvalidArgumentException('RegionFlags value is invalid, aborting call to API');
			}else if(is_integer($start) === false){
				throw new InvalidArgumentException('Start point must be an integer.');
			}else if(isset($count) === true){
				if(is_integer($count) === false){
					throw new InvalidArgumentException('Count must be an integer.');
				}else if($count < 1){
					throw new InvalidArgumentException('Count must be greater than zero.');
				}
			}else if(isset($sortRegionName) === true && is_bool($sortRegionName) === false){
				throw new InvalidArgumentException('If set, the sort by region name flag must be a boolean.');
			}else if(isset($sortLocX) === true && is_bool($sortLocX) === false){
				throw new InvalidArgumentException('If set, the sort by x-axis flag must be a boolean.');
			}else if(isset($sortLocY) === true && is_bool($sortLocY) === false){
				throw new InvalidArgumentException('If set, the sort by y-axis flag must be a boolean.');
			}
			$response = array();
			$input = array(
				'RegionFlags' => $flags,
				'Start'       => $start,
				'Count'       => $count
			);
			if(isset($sortRegionName) === true){
				$input['SortRegionName'] = $sortRegionName;
			}
			if(isset($sortLocX) === true){
				$input['SortLocX'] = $sortLocX;
			}
			if(isset($sortLocY) === true){
				$input['SortLocY'] = $sortLocY;
			}
			$has = WebUI\GetRegions::hasInstance($this, null, $flags, $sortRegionName, $sortLocX, $sortLocY);
			if($asArray === true || $has === false){
				$result = $this->makeCallToAPI('GetRegions', $input, array(
					'Regions' => array('array'=>array(static::GridRegionValidator())),
					'Total'   => array('integer'=>array())
				));
				foreach($result->Regions as $val){
					$response[] = WebUI\GridRegion::fromEndPointResult($val);
				}
			}

			return $asArray ? $response : WebUI\GetRegions::r($this, null, $flags, $start, $has ? null : $result->Total, $sortRegionName, $sortLocX, $sortLocY, $response);
		}

//!	Get a list of regions in the specified estate that match the specified flags.
/**
*	@param object $Estate instance of Aurora::Addon::WebUI::EstateSettings
*	@param integer $flags A bitfield corresponding to constants in Aurora::Framework::RegionFlags
*	@param integer $start start point. If Aurora::Addon::WebUI::GetRegions is primed, then Aurora::Addon::WebUI::GetRegions::r() will auto-seek to start.
*	@param mixed $count Either an integer for the maximum number of regions to fetch from the API end point in a single batch, or NULL to use the end point's default value.
*	@param boolean $asArray
*	@return mixed If $asArray is TRUE returns an array, otherwise returns an instance of Aurora::Addon::WebUI::GetRegionsInEstate
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*	@see Aurora::Addon::WebUI::fromEndPointResult()
*	@see Aurora::Addon::WebUI::GetRegions::r()
*/
		public function GetRegionsInEstate(WebUI\EstateSettings $Estate, $flags=null, $start=0, $count=null, $sortRegionName=null, $sortLocX=null, $sortLocY=null, $asArray=false){
			if(isset($flags) === false){
				$flags = RegionFlags::RegionOnline;
			}
			if(is_bool($asArray) === false){
				throw new InvalidArgumentException('asArray flag must be a boolean.');
			}else if(is_integer($flags) === false){
				throw new InvalidArgumentException('RegionFlags argument should be supplied as integer.');
			}else if($flags < 0){
				throw new InvalidArgumentException('RegionFlags cannot be less than zero');
			}else if(RegionFlags::isValid($flags) === false){ // Aurora::Framework::RegionFlags::isValid() does do a check for integerness, but we want to throw a different exception message if it is an integer.
				throw new InvalidArgumentException('RegionFlags value is invalid, aborting call to API');
			}else if(is_integer($start) === false){
				throw new InvalidArgumentException('Start point must be an integer.');
			}else if(isset($count) === true){
				if(is_integer($count) === false){
					throw new InvalidArgumentException('Count must be an integer.');
				}else if($count < 1){
					throw new InvalidArgumentException('Count must be greater than zero.');
				}
			}else if(isset($sortRegionName) === true && is_bool($sortRegionName) === false){
				throw new InvalidArgumentException('If set, the sort by region name flag must be a boolean.');
			}else if(isset($sortLocX) === true && is_bool($sortLocX) === false){
				throw new InvalidArgumentException('If set, the sort by x-axis flag must be a boolean.');
			}else if(isset($sortLocY) === true && is_bool($sortLocY) === false){
				throw new InvalidArgumentException('If set, the sort by y-axis flag must be a boolean.');
			}
			$response = array();
			$input = array(
				'Estate'      => $Estate->EstateID(),
				'RegionFlags' => $flags,
				'Start'       => $start,
				'Count'       => $count
			);

			if(isset($sortRegionName) === true || isset($sortRegionName) === true || isset($sortLocY) === true){
				$input['Sort'] = array();
				if(isset($sortRegionName) === true){
					$input['Sort']['RegionName'] = $sortRegionName;
				}
				if(isset($sortLocX) === true){
					$input['Sort']['LocX'] = $sortLocX;
				}
				if(isset($sortLocY) === true){
					$input['Sort']['LocY'] = $sortLocY;
				}
			}

			$has = WebUI\GetRegions::hasInstance($this, $Estate, $flags, $sortRegionName, $sortLocX, $sortLocY);
			if($asArray === true || $has === false){
				$result = $this->makeCallToAPI('GetRegions', $input, array(
					'Regions' => array('array'=>array(static::GridRegionValidator())),
					'Total'   => array('integer'=>array())
				));
				foreach($result->Regions as $val){
					$response[] = WebUI\GridRegion::fromEndPointResult($val);
				}
			}

			return $asArray ? $response : WebUI\GetRegionsInEstate::r($this, $Estate, $flags, $start, $has ? null : $result->Total, $sortRegionName, $sortLocX, $sortLocY, $response);
		}

//!	object an instance of Aurora::Addon::WebUI::GridInfo
		protected $GridInfo;
//!	Processes should not be long-lasting, so we only fetch this once.
		public function get_grid_info($info=null){
			if(isset($this->GridInfo) === false){
				$result = $this->makeCallToAPI('get_grid_info', null, array(
					'GridInfo' => array('object'=>array())
				));

				$this->GridInfo = WebUI\GridInfo::f();
				foreach($result->GridInfo as $k=>$v){
					$this->GridInfo[$k] = $v;
				}
			}

			return (isset($info) && is_string($info) && ctype_graph($info)) ? $this->GridInfo[$info] : $this->GridInfo;
		}

//!	returns the friends list for the specified user.
/**
*	@param mixed $forUser Either a UUID string or an instance of Aurora::Addon::WebUI::abstractUser
*	@return object instance of Aurora::Addon::WebUI::FriendsList
*/
		public function GetFriends($forUser){
			if(($forUser instanceof WebUI\abstractUser) === false){
				if(is_string($forUser) === false){
					throw new InvalidArgumentException('forUser must be a string.');
				}else if(preg_match(self::regex_UUID, $forUser) !== 1){
					throw new InvalidArgumentException('forUser must be a valid UUID.');
				}
				$forUser = $this->GetProfile('', $forUser);
			}

			$result = $this->makeCallToAPI('GetFriends', array('UserID' => $forUser->PrincipalID()), array(
				'Friends' => array('array'=>array())
			));
			$response = array();
			foreach($result->Friends as $v){
				if(isset($v->PrincipalID, $v->Name, $v->MyFlags, $v->TheirFlags) === false){
					throw new UnexpectedValueException('Call to API was successful, but required response sub-properties were missing.');
				}
				$response[] = WebUI\FriendInfo::r($forUser, $v->PrincipalID, $v->Name, $v->MyFlags, $v->TheirFlags);
			}

			return new WebUI\FriendsList($response);
		}

//!	Converts an instances of stdClass from Aurora::Addon::WebUI::GetGroups() and Aurora::Addon::WebUI::GetGroup() results to an instance of Aurora::Addon::WebUI::GroupRecord
/**
*	@param object $group instance of stdClass with group properties
*	@return object corresponding instance of Aurora::Addon::WebUI::GroupRecord
*/
		private static function GroupResult2GroupRecord(\stdClass $group){
			if(isset(
				$group->GroupID,
				$group->GroupName,
				$group->Charter,
				$group->GroupPicture,
				$group->FounderID,
				$group->MembershipFee,
				$group->OpenEnrollment,
				$group->ShowInList,
				$group->AllowPublish,
				$group->MaturePublish,
				$group->OwnerRoleID
			) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response sub-properties were missing.');
			}
			return WebUI\GroupRecord::r(
				$group->GroupID,
				$group->GroupName,
				$group->Charter,
				$group->GroupPicture,
				$group->FounderID,
				$group->MembershipFee,
				$group->OpenEnrollment,
				$group->ShowInList,
				$group->AllowPublish,
				$group->MaturePublish,
				$group->OwnerRoleID
			);
		}

//!	Gets an iterator for the number of groups specified, with optional filters.
/**
*	@param integer $start start point of iterator. negatives are supported (kinda).
*	@param integer $count Maximum number of groups to fetch from the WebUI API end-point.
*	@param array $sort optional array of field names for keys and booleans for values, indicating ASC and DESC sort orders for the specified fields.
*	@param array $boolFields optional array of field names for keys and booleans for values, indicating 1 and 0 for field values.
*	@return object Aurora::Addon::WebUI::GetGroupRecords
*	@see Aurora::Addon::WebUI::GetGroupRecords::r()
*/
		public function GetGroups($start=0, $count=10, array $sort=null, array $boolFields=null){
			$input = array(
				'Start' => $start,
				'Count' => $count
			);
			if(isset($sort) === true){
				$input['Sort'] = $sort;
			}
			if(isset($boolFields) === true){
				$input['BoolFields'] = $boolFields;
			}

			$result = $this->makeCallToAPI('GetGroups', $input, array(
				'Start'  => array('integer'=>array()),
				'Total'  => array('integer'=>array()),
				'Groups' => array('array'=>array(array('object'=>array()))),
			));

			$groups = array();
			foreach($result->Groups as $group){
				$groups[] = self::GroupResult2GroupRecord($group);
			}

			return WebUI\GetGroupRecords::r($this, $result->Start, $result->Total, $sort, $boolFields, $groups);
		}

//!	Gets an iterator for the specified list of GroupIDs
/**
*	@param array $GroupIDs list of GroupIDs
*	@return object Aurora::Addon::WebUI::foreknowledgeGetGroupRecords
*/
		public function foreknowledgeGetGroupRecords(array $GroupIDs){

			$result = $this->makeCallToAPI('GetGroups', array(
				'Groups' => $GroupIDs
			), array(
				'Groups' => array('array'=>array(array('object'=>array()))),
			));

			$groups = array();
			foreach($result->Groups as $group){
				$groups[] = self::GroupResult2GroupRecord($group);
			}

			return WebUI\foreknowledgeGetGroupRecords::r($this, $result->Start, $result->Total, null, null, $groups);
		}

//!	Fetches the specified group
/**
*	@param string $nameOrUUID Either a group UUID, or a group name.
*	@return mixed either FALSE indicating no group was found, or an instance of Aurora::Addon::WebUI::GroupRecord
*	@see Aurora::Addon::WebUI::GroupRecord::r()
*/
		public function GetGroup($nameOrUUID){
			if(is_string($nameOrUUID) === true){
				$nameOrUUID = trim($nameOrUUID);
			}else if(is_string($nameOrUUID) === false){
				throw new InvalidArgumentException('Method argument should be a string.');
			}
			$name = '';
			$uuid = '00000000-0000-0000-0000-000000000000';
			if(preg_match(self::regex_UUID, $nameOrUUID) !== 1){
				$input = array(
					'Name' => $nameOrUUID
				);
			}else{
				$input = array(
					'UUID' => $nameOrUUID
				);
			}

			$result = $this->makeCallToAPI('GetGroup', $input, array(
				'Group' => array(
					'object'  => array(),
					'boolean' => array(false)
				)
			));

			return $result->Group ? self::GroupResult2GroupRecord($result->Group) : false;
		}

//!	Enables or disables the specified group as a news source for WebUI
/**
*	Throws an exception on failure, for laziness :P
*	@param object $group instance of Aurora::Addon::WebUI::GroupRecord
*	@param boolean $useAsNewsSource TRUE to enable, FALSE to disable
*/
		public function GroupAsNewsSource(WebUI\GroupRecord $group, $useAsNewsSource=true){
			if(is_bool($useAsNewsSource) === false){
				throw new InvalidArgumentException('flag must be a boolean.');
			}

			$this->makeCallToAPI('GroupAsNewsSource', array(
				'Group' => $group->GroupID(),
				'Use'   => $useAsNewsSource
			), array(
				'Verified' => array('boolean'=>array(true))
			));
		}

//!	Get group notices for the specified groups
/**
*	@param integer $start start point of iterator. negatives are supported (kinda).
*	@param integer $count Maximum number of group notices to fetch from the WebUI API end-point.
*	@param array $groups instances of GroupRecord
*	@return object instance of Aurora::Addon::WebUI::GetGroupNotices
*/
		public function GroupNotices($start=0, $count=10, array $groups, $asArray=false){
			$groupIDs = array();
			foreach($groups as $group){
				if($group instanceof WebUI\GroupRecord){
					$groupIDs[] = $group->GroupID();
				}else if(is_bool($group) === false){
					throw new InvalidArgumentException('Groups must be an array of Aurora::Addon::WebUI::GroupRecord instances');				
				}
			}

			$result = $this->makeCallToAPI('GroupNotices', array(
				'Start' => $start,
				'Count' => $count,
				'Groups' => $groupIDs
			), array(
				'Total' => array('integer'=>array()),
				'GroupNotices' => array('array'=>array(array('object'=>array(array(
					'GroupID'       => array('string'=>array()),
					'NoticeID'      => array('string'=>array()),
					'Timestamp'     => array('integer'=>array()),
					'FromName'      => array('string'=>array()),
					'Subject'       => array('string'=>array()),
					'Message'       => array('string'=>array()),
					'HasAttachment' => array('boolean'=>array()),
					'ItemID'        => array('string'=>array()),
					'AssetType'     => array('integer'=>array()),
					'ItemName'      => array('string'=>array())
				)))))
			));

			$groupNotices = array();
			foreach($result->GroupNotices as $groupNotice){
				$groupNotices[] = WebUI\GroupNoticeData::r(
					$groupNotice->GroupID,
					$groupNotice->NoticeID,
					$groupNotice->Timestamp,
					$groupNotice->FromName,
					$groupNotice->Subject,
					$groupNotice->Message,
					$groupNotice->HasAttachment,
					$groupNotice->ItemID,
					$groupNotice->AssetType,
					$groupNotice->ItemName
				);
			}

			return $asArray ? $groupNotices : WebUI\GetGroupNotices::r($this, $start, $result->Total, $groupIDs, $groupNotices);
		}

//!	Get group notices from groups flagged as being news sources.
/**
*	@param integer $start start point of iterator. negatives are supported (kinda).
*	@param integer $count Maximum number of group notices to fetch from the WebUI API end-point.
*	@return object instance of Aurora::Addon::WebUI::GetGroupNotices
*/
		public function NewsFromGroupNotices($start=0, $count=10, $asArray=false){

			$result = $this->makeCallToAPI('NewsFromGroupNotices', array(
				'Start' => $start,
				'Count' => $count
			), array(
				'Total' => array('integer'=>array()),
				'GroupNotices' => array('array'=>array(array('object'=>array(array(
					'GroupID'       => array('string'=>array()),
					'NoticeID'      => array('string'=>array()),
					'Timestamp'     => array('integer'=>array()),
					'FromName'      => array('string'=>array()),
					'Subject'       => array('string'=>array()),
					'Message'       => array('string'=>array()),
					'HasAttachment' => array('boolean'=>array()),
					'ItemID'        => array('string'=>array()),
					'AssetType'     => array('integer'=>array()),
					'ItemName'      => array('string'=>array())
				)))))
			));

			$groupNotices = array();
			foreach($result->GroupNotices as $groupNotice){
				$groupNotices[] = WebUI\GroupNoticeData::r(
					$groupNotice->GroupID,
					$groupNotice->NoticeID,
					$groupNotice->Timestamp,
					$groupNotice->FromName,
					$groupNotice->Subject,
					$groupNotice->Message,
					$groupNotice->HasAttachment,
					$groupNotice->ItemID,
					$groupNotice->AssetType,
					$groupNotice->ItemName
				);
			}

			return $asArray ? $groupNotices : WebUI\GetNewsFromGroupNotices::r($this, $start, $result->Total, array(), $groupNotices);
		}

//!	PHP doesn't do const arrays :(
/**
*	@return array The validator array to be passed to Aurora::Addon::WebUI::makeCallToAPI() when making parcel-related calls.
*/
		protected static function ParcelResultValidatorArray(){
			static $validator = array(
				'object' => array(array(
					'GroupID' => array('string' => array()),
					'OwnerID' => array('string' => array()),
					'Maturity' => array('integer' => array()),
					'Area' => array('integer' => array()),
					'AuctionID' => array('integer' => array()),
					'SalePrice' => array('integer' => array()),
					'InfoUUID' => array('string' => array()),
					'Dwell' => array('integer' => array()),
					'Flags' => array('integer' => array()),
					'Name' => array('string' => array()),
					'Description' => array('string' => array()),
					'UserLocation' => array('array' => array(array(
						array('float' => array()),
						array('float' => array()),
						array('float' => array())
					))),
					'LocalID' => array('integer' => array()),
					'GlobalID' => array('string' => array()),
					'RegionID' => array('string' => array()),
					'MediaDescription' => array('string' => array()),
					'MediaHeight' => array('integer' => array()),
					'MediaLoop' => array('boolean' => array()),
					'MediaType' => array('string' => array()),
					'ObscureMedia' => array('boolean' => array()),
					'ObscureMusic' => array('boolean' => array()),
					'SnapshotID' => array('string' => array()),
					'MediaAutoScale' => array('integer' => array()),
					'MediaLoopSet' => array('float' => array()),
					'MediaURL' => array('string' => array()),
					'MusicURL' => array('string' => array()),
					'Bitmap' => array('string' => array()),
					'Category' => array('integer' => array()),
					'FirstParty' => array('boolean' => array()),
					'ClaimDate' => array('integer' => array()),
					'ClaimPrice' => array('integer' => array()),
					'Status' => array('integer' => array()),
					'LandingType' => array('integer' => array()),
					'PassHours' => array('float' => array()),
					'PassPrice' => array('integer' => array()),
					'UserLookAt' => array('array' => array(array(
						array('float' => array()),
						array('float' => array()),
						array('float' => array())
					))),
					'AuthBuyerID' => array('string' => array()),
					'OtherCleanTime' => array('integer' => array()),
					'RegionHandle' => array('string' => array()),
					'Private' => array('boolean' => array()),
					'GenericData' => array('object' => array()),
				))
			);
			return $validator;
		}

//!	Converts an API result for parcels to an instance of Aurora::Addon::WebUI::LandData
/**
*	@param object API result
*	@return object instance of Aurora::Addon::WebUI::LandData
*/
		private static function ParcelResult2LandData(\stdClass $result){		
			$result->UserLookAt   = new Vector3($result->UserLookAt[0]  , $result->UserLookAt[1]  , $result->UserLookAt[2]  );
			$result->UserLocation = new Vector3($result->UserLocation[0], $result->UserLocation[1], $result->UserLocation[2]);
			return WebUI\LandData::r(
				$result->InfoUUID,
				$result->RegionID,
				$result->GlobalID,
				$result->LocalID,
				$result->SalePrice,
				$result->UserLocation,
				$result->UserLookAt,
				$result->Name,
				$result->Description,
				$result->Flags,
				$result->Dwell,
				$result->AuctionID,
				$result->Area,
				$result->Maturity,
				$result->OwnerID,
				$result->GroupID,
				$result->IsGroupOwned,
				$result->SnapshotID,
				$result->MediaDescription,
				$result->MediaWidth,
				$result->MediaHeight,
				$result->MediaLoop,
				$result->MediaType,
				$result->ObscureMedia,
				$result->ObscureMusic,
				$result->MediaLoopSet,
				$result->MediaAutoScale,
				$result->MediaURL,
				$result->MusicURL,
				$result->Bitmap,
				$result->Category,
				$result->FirstParty,
				$result->ClaimDate,
				$result->ClaimPrice,
				$result->LandingType,
				$result->PassHours,
				$result->PassPrice,
				$result->AuthBuyerID,
				$result->OtherCleanTime,
				$result->RegionHandle,
				$result->Private,
				$result->GenericData
			);
		}

//!	Gets all parcels in a region, optionally filtering by parcel owner and region scope ID
/**
*	@param integer $start start point for results (useful for paginated output)
*	@param integer $count maximum number of results to return in initial call.
*	@param object $region instance of Aurora::Addon::WebUI::GridRegion
*	@param string $owner Parcel owner UUID
*	@param string $scopeID Region scope ID
*	@param boolean $asArray if TRUE return array of parcels, if FALSE return Iterator object
*	@return mixed Either an array of Aurora::Addon::WebUI::LandData or an instance of Aurora::Addon::WebUI::GetParcelsByRegion
*/
		public function GetParcelsByRegion($start=0, $count=10, WebUI\GridRegion $region, $owner='00000000-0000-0000-0000-000000000000', $scopeID='00000000-0000-0000-0000-000000000000', $asArray=false){
			if(is_string($start) === true && ctype_digit($start) === true){
				$start = (integer)$start;
			}
			if(is_string($count) === true && ctype_digit($count) === true){
				$count = (integer)$count;
			}

			if(is_integer($start) === false){
				throw new InvalidArgumentException('Start point must be specified as integer.');
			}else if($start < 0){
				throw new InvalidArgumentException('Start point must be greater than or equal to zero.');
			}else if(is_integer($count) === false){
				throw new InvalidArgumentException('Count must be specified as integer.');
			}else if($count < 0){
				throw new InvalidArgumentException('Count must be greater than or equal to zero.');
			}else if(is_string($owner) === false){
				throw new InvalidArgumentException('Owner must be specified as string.');
			}else if(preg_match(self::regex_UUID, $owner) != 1){
				throw new InvalidArgumentException('Owner must be valid UUID.');
			}else if(is_string($scopeID) === false){
				throw new InvalidArgumentException('scopeID must be specified as string.');
			}else if(preg_match(self::regex_UUID, $scopeID) != 1){
				throw new InvalidArgumentException('scopeID must be valid UUID.');
			}

			$result = $this->makeCallToAPI('GetParcelsByRegion', array(
				'Start'   => $start,
				'Count'   => $count,
				'Region'  => $region->RegionID(),
				'Owner'   => $owner,
				'ScopeID' => $scopeID,
			), array(
				'Parcels' => array(
					'array' => array(self::ParcelResultValidatorArray())
				),
				'Total' => array('integer'=>array())
			));
			foreach($result->Parcels as $k=>$v){
				$result->Parcels[$k] = self::ParcelResult2LandData($v);
			}
			return $asArray ? $result->Parcels : WebUI\GetParcelsByRegion::r($this, $start, $result->Total, $region, $owner, $scopeID, $result->Parcels);
		}

//!	Gets a parcel either by infoID or by name, region and region scopeID
/**
*	@param string $parcel Either a parcel's infoID or a parcel name
*	@param mixed $region Either NULL when $parcel is a UUID, or an instance of Aurora::Addon::WebUI::GridRegion
*	@param string $scopeID Region ScopeID
*	@return object instance of Aurora::Addon::WebUI::LandData
*/
		public function GetParcel($parcel, WebUI\GridRegion $region=null, $scopeID='00000000-0000-0000-0000-000000000000'){
			if(is_string($parcel) === false){
				throw new InvalidArgumentException('Parcel argument must be specified as string.');
			}else if(is_string($scopeID) === false){
				throw new InvalidArgumentException('ScopeID must be specified as string.');
			}else if(preg_match(self::regex_UUID, $scopeID) != 1){
				throw new InvalidArgumentException('ScopeID must be a valid UUID.');
			}

			$input = array();
			if(preg_match(self::regex_UUID, $parcel) != 1){
				if(isset($region) === false){
					throw new InvalidArgumentException('When attempting to get a parcel by name, the region must be specified.');
				}
				$input['RegionID'] = $region->RegionID();
				$input['ScopeID'] = $scopeID;
				$input['Parcel'] = trim($parcel);
			}else{
				$input['ParcelInfoUUID'] = $parcel;
			}

			return self::ParcelResult2LandData($this->makeCallToAPI('GetParcel', $input, array(
				'Parcel' => self::ParcelResultValidatorArray()
			))->Parcel);
		}

//!	Gets all parcels in the specified region with the specified parcel name.
/**
*	@param integer $start start point for results (useful for paginated output)
*	@param integer $count maximum number of results to return in initial call.
*	@param string Parcel name
*	@param object $region instance of Aurora::Addon::WebUI::GridRegion
*	@param string $scopeID Region scope ID
*	@param boolean $asArray if TRUE return array of parcels, if FALSE return Iterator object
*	@return mixed Either an array of Aurora::Addon::WebUI::LandData or an instance of Aurora::Addon::WebUI::GetParcelsWithNameByRegion
*/
		public function GetParcelsWithNameByRegion($start=0, $count=10, $name, WebUI\GridRegion $region, $scopeID='00000000-0000-0000-0000-000000000000', $asArray=false){
			if(is_string($start) === true && ctype_digit($start) === true){
				$start = (integer)$start;
			}
			if(is_string($count) === true && ctype_digit($count) === true){
				$count = (integer)$count;
			}
			if(is_string($name) === true){
				$name = trim($name);
			}

			if(is_integer($start) === false){
				throw new InvalidArgumentException('Start point must be specified as integer.');
			}else if($start < 0){
				throw new InvalidArgumentException('Start point must be greater than or equal to zero.');
			}else if(is_integer($count) === false){
				throw new InvalidArgumentException('Count must be specified as integer.');
			}else if($count < 0){
				throw new InvalidArgumentException('Count must be greater than or equal to zero.');
			}else if(is_string($scopeID) === false){
				throw new InvalidArgumentException('scopeID must be specified as string.');
			}else if(preg_match(self::regex_UUID, $scopeID) != 1){
				throw new InvalidArgumentException('scopeID must be valid UUID.');
			}else if(is_string($name) === false){
				throw new InvalidArgumentException('Parcel name must be specified as string.');
			}else if($name === ''){
				throw new InvalidArgumentException('Parcel name must not be empty string.');
			}

			$result = $this->makeCallToAPI('GetParcelsWithNameByRegion', array(
				'Start'   => $start,
				'Count'   => $count,
				'Region'  => $region->RegionID(),
				'Parcel'   => $name,
				'ScopeID' => $scopeID,
			), array(
				'Parcels' => array(
					'array' => array(self::ParcelResultValidatorArray())
				),
				'Total' => array('integer'=>array())
			));
			foreach($result->Parcels as $k=>$v){
				$result->Parcels[$k] = self::ParcelResult2LandData($v);
			}
			return $asArray ? $result->Parcels : WebUI\GetParcelsWithNameByRegion::r($this, $start, $result->Total, $name, $region, $scopeID, $result->Parcels);
		}

//!	Gets the array used as the expected response parameter for Aurora::Addon::WebUI::makeCallToAPI()
/**
*	@return array
*/
		private static function EstateSettingsValidator(){
			return array(
				'object' => array(array(
					'EstateID' => array('integer'=>array()),
					'EstateName' => array('string'=>array()),
					'AbuseEmailToEstateOwner' => array('boolean'=>array()),
					'DenyAnonymous' => array('boolean'=>array()),
					'ResetHomeOnTeleport' => array('boolean'=>array()),
					'FixedSun' => array('boolean'=>array()),
					'DenyTransacted' => array('boolean'=>array()),
					'BlockDwell' => array('boolean'=>array()),
					'DenyIdentified' => array('boolean'=>array()),
					'AllowVoice' => array('boolean'=>array()),
					'UseGlobalTime' => array('boolean'=>array()),
					'PricePerMeter' => array('integer'=>array()),
					'TaxFree' => array('boolean'=>array()),
					'AllowDirectTeleport' => array('boolean'=>array()),
					'RedirectGridX' => array('integer'=>array(), 'null'=>array()),
					'RedirectGridY' => array('integer'=>array(), 'null'=>array()),
					'ParentEstateID' => array('integer'=>array()),
					'SunPosition' => array('float'=>array()),
					'EstateSkipScripts' => array('boolean'=>array()),
					'BillableFactor' => array('float'=>array()),
					'PublicAccess' => array('boolean'=>array()),
					'AbuseEmail' => array('string'=>array()),
					'EstateOwner' => array('string'=>array()),
					'DenyMinors' => array('boolean'=>array()),
					'AllowLandmark' => array('boolean'=>array()),
					'AllowParcelChanges' => array('boolean'=>array()),
					'AllowSetHome' => array('boolean'=>array()),
					'EstateBans' => array('array'=>array(array('string'=>array()))),
					'EstateManagers' => array('array'=>array(array('string'=>array()))),
					'EstateGroups' => array('array'=>array(array('string'=>array()))),
					'EstateAccess' => array('array'=>array(array('string'=>array()))),
				))
			);
		}

//!	Converts an API result into an EstateSettings object
/**
*	@return object instance of EstateSettings
*/
		private static function EstateSettingsFromResult(\stdClass $Estate){
			return WebUI\EstateSettings::r(
				$Estate->EstateID,
				$Estate->EstateName,
				$Estate->AbuseEmailToEstateOwner,
				$Estate->DenyAnonymous,
				$Estate->ResetHomeOnTeleport,
				$Estate->FixedSun,
				$Estate->DenyTransacted,
				$Estate->BlockDwell,
				$Estate->DenyIdentified,
				$Estate->AllowVoice,
				$Estate->UseGlobalTime,
				$Estate->PricePerMeter,
				$Estate->TaxFree,
				$Estate->AllowDirectTeleport,
				$Estate->RedirectGridX,
				$Estate->RedirectGridY,
				$Estate->ParentEstateID,
				$Estate->SunPosition,
				$Estate->EstateSkipScripts,
				$Estate->BillableFactor,
				$Estate->PublicAccess,
				$Estate->AbuseEmail,
				$Estate->EstateOwner,
				$Estate->DenyMinors,
				$Estate->AllowLandmark,
				$Estate->AllowParcelChanges,
				$Estate->AllowSetHome,
				$Estate->EstateBans,
				$Estate->EstateManagers,
				$Estate->EstateGroups,
				$Estate->EstateAccess
			);
		}

//!	Gets all estates with the specified owner and optional boolean filters
/**
*	@param string $Owner Owner UUID
*	@param array $boolFields optional array of field names for keys and booleans for values, indicating 1 and 0 for field values.
*	@return object instance of Aurora::Addon::WebUI::EstateSettingsIterator
*/
		public function GetEstates($Owner, array $boolFields=null){
			if(($Owner instanceof WebUI\abstractUser) === false){
				if(is_string($Owner) === false){
					throw new InvalidArgumentException('OwnerID must be a string.');
				}else if(preg_match(self::regex_UUID, $Owner) !== 1){
					throw new InvalidArgumentException('OwnerID must be a valid UUID.');
				}
				$Owner = $this->GetProfile('', $Owner);
			}
		
			$input = array(
				'Owner' => $Owner->PrincipalID()
			);
			if(isset($boolFields) === true){
				$input['BoolFields'] = $boolFields;
			}

			$Estates = $this->makeCallToAPI('GetEstates', $input, array(
				'Estates' => array('array' => array(
					static::EstateSettingsValidator()
				))
			))->Estates;
			$result = array();
			foreach($Estates as $Estate){
				$result[] = static::EstateSettingsFromResult($Estate);
			}

			return new WebUI\EstateSettingsIterator($result);
		}

//!	Gets a single estate by estate name
/**
*	@param mixed Estate ID or Estate Name
*	@return object instance of Aurora::Addon::WebUI::EstateSettings
*/
		public function GetEstate($Estate){
			if(is_string($Estate) === true){
				if(ctype_digit($Estate) === true){
					$Estate = (integer)$Estate;
				}else{
					$Estate = trim($Estate);
				}
			}

			if(is_integer($Estate) === false && is_string($Estate) === false){
				throw new InvalidArgumentException('Estate must be specified as integer or string.');
			}

			return static::EstateSettingsFromResult($this->makeCallToAPI('GetEstate', array('Estate' => $Estate), array(
				'Estate' => static::EstateSettingsValidator()
			))->Estate);
		}

//!	PHP doesn't do const arrays :(
/**
*	@return array The validator array to be passed to Aurora::Addon::WebUI::makeCallToAPI() when making event-related calls. 
*/
		private static function EventsResultValidatorArray(){
			return array('object' => array(array(
				'eventID'     => array('integer' => array()),
				'creator'     => array('string'  => array()),
				'name'        => array('string'  => array()),
				'category'    => array('string'  => array()),
				'description' => array('string'  => array()),
				'date'        => array('string'  => array()),
				'dateUTC'     => array('integer' => array()),
				'duration'    => array('integer' => array()),
				'cover'       => array('integer' => array()),
				'amount'      => array('integer' => array()),
				'simName'     => array('string' => array()),
				'globalPos'   => array('array'   => array()),
				'eventFlags'  => array('integer' => array()),
				'maturity'    => array('integer' => array())
			)));
		}

//!	Get a list of events with optional filters
/**
*	@param integer $start Start point
*	@param integer $count Maximum number of results to fetch in initial call
*	@param array $filter columns to filter by
*	@param array $sort fields to sort by
*	@return object instance of Aurora::Addon::WebUI::GetEvents
*/
		public function GetEvents($start=0, $count=10, array $filter=null, array $sort=null, $asArray=false){
			if(is_string($start) === true && ctype_digit($start) === true){
				$start = (integer)$start;
			}
			if(is_string($count) === true && ctype_digit($count) === true){
				$count = (integer)$count;
			}

			if(is_integer($start) === false){
				throw new InvalidArgumentException('Start point must be specified as integer.');
			}else if(is_integer($count) === false){
				throw new InvalidArgumentException('Count must be specified as integer.');
			}else if($count < 0){
				throw new InvalidArgumentException('Count must be greater than or equal to zero.');
			}
			
			$input = array(
				'Start' => $start,
				'Count' => $count
			);
			if(isset($filter) === true){
				$input['Filter'] = $filter;
			}
			if(isset($sort) === true){
				$input['Sort'] = $sort;
			}
			
			$result = $this->makeCallToAPI('GetEvents', $input, array(
				'Events' => array('array'=>array( static::EventsResultValidatorArray())),
				'Total'  => array('integer'=>array())
			));
			$events = array();
			foreach($result->Events as $event){
				$events[] = WebUI\EventData::r(
					$event->eventID,
					$event->creator,
					$event->name,
					$event->description,
					$event->category,
					DateTime::createFromFormat('U', $event->dateUTC),
					$event->duration,
					$event->cover,
					$event->simName,
					new Vector3($event->globalPos[0], $event->globalPos[1], $event->globalPos[2]) ,
					$event->eventFlags,
					$event->maturity
				);
			}
			return $asArray ? $events : WebUI\GetEvents::r($this, $start, $result->Total, $filter, $sort, $events);
		}

//!	Adds an event to the grid directory
/**
*	@param object $creator User to list as the creator
*	@param object $region Region the event is hosted in
*	@param object $date Date & Time the event will be held
*	@param integer $cover length of event
*	@param integer $maturity indicates content rating of event
*	@param integer $eventFlags bitfield
*	@param integer $duration number of minutes the event lasts for
*	@param object $localPos location of event within region
*	@param string $name event subject
*	@param string $description event description
*	@param string $category event category
*	@return object Instance of Aurora::Addon::WebUI::EventData
*/
		public function CreateEvent(WebUI\abstractUser $creator, WebUI\GridRegion $region, DateTime $date, $cover, $maturity, $eventFlags, $duration, Vector3 $localPos, $name, $description, $category){
			if(is_string($cover) === true && ctype_digit($cover) === true){
				$cover = (integer)$cover;
			}
			if(is_string($maturity) === true && ctype_digit($maturity) === true){
				$maturity = (integer)$maturity;
			}
			if(is_string($eventFlags) === true && ctype_digit($eventFlags) === true){
				$eventFlags = (integer)$eventFlags;
			}
			if(is_string($duration) === true && ctype_digit($duration) === true){
				$duration = (integer)$duration;
			}
			if(is_string($name) === true){
				$name = trim($name);
			}
			if(is_string($description) === true){
				$description = trim($description);
			}
			if(is_string($category) === true){
				$category = trim($category);
			}

			if(is_integer($cover) === false){
				throw new InvalidArgumentException('Cover must be specified as integer.');
			}else if($cover < 0){
				throw new InvalidArgumentException('Cover must be greater than or equal to zero');
			}else if(is_integer($maturity) === false){
				throw new InvalidArgumentException('Maturity must be specified as integer.');
			}else if($maturity < 0){
				throw new InvalidArgumentException('Maturity must be greater than or equal to zero');
			}else if(is_integer($eventFlags) === false){
				throw new InvalidArgumentException('Flags must be specified as integer.');
			}else if($eventFlags < 0){
				throw new InvalidArgumentException('Flags must be greater than or equal to zero');
			}else if(is_integer($duration) === false){
				throw new InvalidArgumentException('Duration must be specified as integer.');
			}else if($duration <= 0){
				throw new InvalidArgumentException('Duration must be greater than zero');
			}else if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be specified as string.');
			}else if($name === ''){
				throw new InvalidArgumentException('Name must be non-empty string.');
			}else if(is_string($description) === false){
				throw new InvalidArgumentException('Description must be specified as string.');
			}else if($description === ''){
				throw new InvalidArgumentException('Description must be non-empty string.');
			}else if(is_string($category) === false){
				throw new InvalidArgumentException('Category must be specified as string.');
			}else if($category === ''){
				throw new InvalidArgumentException('Category must be non-empty string.');
			}

			$event = $this->makeCallToAPI('CreateEvent', array(
				'Creator'     => $creator->PrincipalID(),
				'Region'      => $region->RegionID(),
				'Parcel'      => '00000000-0000-0000-0000-000000000000',
				'Date'        => $date->format('c'),
				'Cover'       => $cover,
				'Maturity'    => $maturity,
				'EventFlags'  => $eventFlags,
				'Duration'    => $duration,
				'Position'    => (string)$localPos,
				'Name'        => $name,
				'Description' => $description,
				'Category'    => $category
			), array(
				'Event' => static::EventsResultValidatorArray()
			))->Event;

			return WebUI\EventData::r(
				$event->eventID,
				$event->creator,
				$event->name,
				$event->description,
				$event->category,
				DateTime::createFromFormat('U', $event->dateUTC),
				$event->duration,
				$event->cover,
				$event->simName,
				new Vector3($event->globalPos[0], $event->globalPos[1], $event->globalPos[2]) ,
				$event->eventFlags,
				$event->maturity
			);
		}
	}
}

namespace{
	require_once('WebUI/abstracts.php');

	require_once('WebUI/GridInfo.php');
	require_once('WebUI/Regions.php');
	require_once('WebUI/Parcels.php');
	require_once('WebUI/User.php');
	require_once('WebUI/Group.php');
	require_once('WebUI/Events.php');

	require_once('WebUI/AbuseReports.php');
	require_once('WebUI/AvatarArchives.php');
	require_once('WebUI/Friends.php');
	require_once('WebUI/Template.php');
}

//!	Code specific to the WebUI
namespace Aurora\Addon\WebUI{

	use Aurora\Addon\WORM;

//!	Long-term goal of Aurora-WebUI-GPL is to support multiple grids on a single website, so we need an iterator to hold all the configs.
	class Configs extends WORM{

//!	singleton method.
/**
*	@return object an instance of Aurora::Addon::WebUI::Configs
*/
		public static function i(){
			static $instance;
			if(isset($instance) === false){
				$instance = new static();
			}
			return $instance;
		}

//!	Shorthand method for getting the default instance of Aurora::Addon::WebUI without having to call Aurora::Addon::WebUI::reset() all the time.
/**
*	@return object an instance of Aurora::Addon::WebUI
*/
		public static function d(){
			if(static::i()->offsetExists(0) === false){
				throw new BadMethodCallException('No configs have been set.');
			}
			return static::i()->offsetGet(0);
		}


		public function offsetSet($offset, $value){
			if(($value instanceof \Aurora\Addon\WebUI) === false){
				throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI can be added to instances of Aurora::Addon::WebUI::Configs');
			}else if(isset($offset) === true && is_integer($offset) === false){
				throw new InvalidArgumentException('Only integer offsets allowed.');
			}

			$offset = isset($offset) ? $offset : $this->count();

			if(isset($this[$offset]) === true){
				throw new InvalidArgumentException('Configs cannot be overwritten.');
			}

			$this->data[$offset] = $value;
		}
	}
}
?>
