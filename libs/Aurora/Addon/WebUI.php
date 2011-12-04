<?php
//!	Mimicking the layout of code in Aurora Sim here.
namespace Aurora\Addon{
	use RuntimeException;
	use InvalidArgumentException;
	use UnexpectedValueException;
	use LengthException;

	use DateTime;

	use Aurora\Framework\RegionFlags;
	use Aurora\Services\Interfaces\User;

//!	Now you might think this class should be a singleton loading config values from constants instead of a registry method, but Marv has plans. MUAHAHAHAHA.
	class WebUI{
//!	string Regular expression for validating UUIDs (put here until this operation gets performed elsewhere.
		const regex_UUID = '/^[a-fA-F0-9]{8}\-[a-fA-F0-9]{4}\-[a-fA-F0-9]{4}\-[a-fA-F0-9]{4}\-[a-fA-F0-9]{12}$/';

//!	This is protected because we're going to use a registry method to access it.
/**
*	The WIREDUX_PASSWORD constant is never used without being passed as an md5() hash, so we immediately do this on instantiation.
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
		protected function makeCallToAPI($method, array $arguments=null){
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
				return $result;
			}
			throw new RuntimeException('API call failed to execute.'); // if this starts happening frequently, we'll add in some more debugging code.
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
			$result = $this->makeCallToAPI('CheckIfUserExists', array('Name'=>$name));
			if(isset($result->Verified) === false){
				throw new UnexpectedValueException('Verified property was not set on API result');
			}else if(is_bool($result->Verified) === false){
				if(is_string($result->Verified) === true){
					$result = strtolower($result->Verified);
					if($result === 'true' || $result === 'false'){
						return ($result === 'true');
					}
				}
				throw new UnexpectedValueException('Verified property from API result should be a boolean or boolean as string (true/false)');
			}
			return $result->Verified;
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
			}else if(is_string($RLFirstName) === false){
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
			}else if(is_string($RLIP) === false){
				throw new InvalidArgumentException('User RL IP Address must be a string.');
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

			$result = $this->makeCallToAPI('CreateAccount', array(
				'Name'         => $Name,
				'PasswordHash' => $Password,
				'Email'        => $Email,
				'RLDOB'        => $RLDOB,
				'RLFirstName'  => $RLFirstName,
				'RLLastName'   => $RLLastName,
				'RLAddress'    => $RLAddress,
				'RLCity'       => $RLCity,
				'RLZip'        => $RLZip,
				'RLCountry'    => $RLCountry,
				'RLIP'         => $RLIP
			));

			if(isset($result->UUID) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response properties were missing.');
			}else if(is_string($result->UUID) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response property is of incorrect type.');
			}else if(preg_match(self::regex_UUID, $result->UUID) === false){
				throw new UnexpectedValueException('Call to API was successful, but UUID response was not a valid UUID.');
			}else if($result->UUID === '00000000-0000-0000-0000-000000000000'){
				throw new RuntimeException('Call to API was successful but registration failed.');
			}
			return $this->GetGridUserInfo($result->UUID);
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
			$result = $this->makeCallToAPI($asAdmin ? 'AdminLogin' : 'Login', array('Name' => $username, 'Password' => $password));
			if(isset($result->Verified) === false){
				throw new UnexpectedValueException('Could not determine if credentials were correct, API call was made but required response properties were missing');
			}else if($result->Verified === false){
				throw new InvalidArgumentException('Credentials incorrect');
			}else if(isset($result->UUID, $result->FirstName, $result->LastName) === false){
				throw new InvalidArgumentException('API call was made, credentials were correct but required response properties were missing');
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
			$result = $this->makeCallToAPI('OnlineStatus');
			if(isset($result->Online, $result->LoginEnabled) === false){
				$missing = array();
				if(isset($result->Online) === false){
					$missing[] = 'Online';
				}
				if(isset($result->LoginEnabled) === false){
					$missing[] = 'LoginEnabled';
				}
				throw new UnexpectedValueException('API result missing required properties: ' . implode(', ', $missing));
			}
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
			$result = $this->makeCallToAPI('Authenticated', array('UUID'=>$uuid));
			if(isset($result->Verified) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response properties were missing.');
			}else if(is_bool($result->Verified) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response property was not of expected type.');
			}
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
			$result = $this->makeCallToAPI('GetGridUserInfo', array('UUID'=>$uuid));
			if(isset($result->UUID, $result->HomeUUID, $result->HomeName, $result->Online, $result->Email, $result->Name, $result->FirstName, $result->LastName) === false){
				throw new InvalidArgumentException('Call to API was successful, but required response properties were missing.');
			}
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

			$result = $this->makeCallToAPI('SaveEmail', array('UUID' => $uuid, 'Email' => $email));
			if(isset($result->Verified) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response properties were missing.');
			}else if(is_bool($result->Verified) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response property was of unexpected type.');
			}

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

			$result = $this->makeCallToAPI('ChangeName', array('UUID' => $uuid, 'Name' => $name));
			if(isset($result->Verified, $result->Stored) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response properties were missing.');
			}else if(is_bool($result->Verified) === false || is_bool($result->Stored) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response property was of unexpected type.');
			}else if($result->Verified === true && $result->Stored === false){
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

			$result = $this->makeCallToAPI('ChangePassword', array('UUID' => $uuid, 'Password' => $oldPassword, 'NewPassword' => $newPassword));

			if(isset($result->Verified) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response properties were missing.');
			}else if(is_bool($result->Verified) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response property was of unexpected type.');
			}

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

			$result = $this->makeCallToAPI('ConfirmUserEmailName', array('Name' => $name, 'Email' => $email));
			if(isset($result->Verified) === false){
				throw new UnexpectedValueException('Call to API was successful but required response properties were missing.');
			}else if(is_bool($result->Verified) === false){
				throw new UnexpectedValueException('Call to API was successful but required response property was of unexpected type.',1);
			}else if(isset($result->ErrorCode) === true && is_integer($result->ErrorCode) === false){
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

			$result = $this->makeCallToAPI('GetProfile', array('Name' => $name, 'UUID' => $uuid));

			if(isset($result->account) === false){
				throw new InvalidArgumentException('Call to API was successful, but required response properties were missing.');
			}
			$account = $result->account;
			
			if(isset($account->Created, $account->Name, $account->PrincipalID, $account->Email, $account->PartnerUUID) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response properties were missing.');
			}

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

			return WebUI\UserProfile::r($account->PrincipalID, $account->Name, $account->Email, $account->Created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $account->PartnerUUID, $visible, $customType, $notes, $RLName, $RLAddress, $RLZip, $RLCity, $RLCountry);
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

			$result = $this->makeCallToAPI('EditUser', $data);

			if(isset($result->agent, $result->account) === false){
				throw new UnexpectedValueException('Call to API was successful but required response properties were missing.');
			}else if(is_bool($result->agent) === false){
				throw new UnexpectedValueException('Call to API was successful but required response property was of unexpected type.');
			}else if(is_bool($result->account) === false){
				throw new UnexpectedValueException('Call to API was successful but required response property was of unexpected type.',1);
			}

			return ($result->agent && $result->account);
		}

//!	Attempt to fetch the public avatar archives.
/**
*	@return an instance of Aurora::Addon::WebUI::AvatarArchives corresponding to the result returned by the API end point.
*/
		public function GetAvatarArchives(){
			$result = $this->makeCallToAPI('GetAvatarArchives');

			if(isset($result->names, $result->snapshot) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response properties were missing.');
			}else if(is_array($result->names) === false || is_array($result->snapshot) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response properties were of an unexpected type.');
			}else if(count($result->names) !== count($result->snapshot)){
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

			$result = $this->makeCallToAPI('DeleteUser', array('UserID' => $uuid));

			if(isset($result->Finished) === false){
				throw new UnexpectedValueException('Call to API was successful but required response properties were missing.');
			}else if(is_bool($result->Finished) === false){
				throw new UnexpectedValueException('Call to API was successful but required response property was of unexpected type.');
			}

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

			$result = $this->makeCallToAPI(isset($until) ? 'TempBanUser' : 'BanUser', array('UserID' => $uuid, 'BannedUntil' => $until));

			if(isset($result->Finished) === false){
				throw new UnexpectedValueException('Call to API was successful but required response properties were missing.');
			}else if(is_bool($result->Finished) === false){
				throw new UnexpectedValueException('Call to API was successful but required response property was of unexpected type.');
			}

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

			$result = $this->makeCallToAPI('UnBanUser', array('UserID' => $uuid));

			if(isset($result->Finished) === false){
				throw new UnexpectedValueException('Call to API was successful but required response properties were missing.');
			}else if(is_bool($result->Finished) === false){
				throw new UnexpectedValueException('Call to API was successful but required response property was of unexpected type.');
			}

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

			$result = $this->makeCallToAPI('FindUsers', array('Start', 'End', 'Query'));

			if(isset($result->Users) === false){
				throw new UnexpectedValueException('Call to API was successful but required response properties were missing.');
			}else if(is_array($result->Users) === false){
				throw new UnexpectedValueException('Call to API was successful but required response property was of unexpected type.');
			}

			$results = array();
			foreach($result->Users as $userdata){
				if(isset($userdata->PrincipalID, $userdata->UserName, $userdata->Created, $userdata->UserFlags) === false){
					throw new UnexpectedValueException('Call to API was successful but required response sub-properties were missing.');
				}
				$results = WebUI\SearchUser::r($userdata->PrincipalID, $userdata->UserName, $userdata->Created, $userdata->UserFlags);
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

			$result = $this->makeCallToAPI('GetAbuseReports', array('Start' => $start, 'Count' => $count, 'Active' => $active));

			if(isset($result->AbuseReports) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response properties were missing.');
			}else if(is_array($result->AbuseReports) === false){
				throw new UnexpectedValueException('Call to API was successful, but required response property was of unexpected type.');
			}

			$results = array();
			foreach($result->AbuseReports as $AR){
				if(isset($AR->Number, $AR->Details, $AR->Location, $AR->UserName, $AR->Summary, $AR->Active, $AR->AssignedTo, $AR->Category, $AR->Checked, $AR->Notes, $AR->ObjectName, $AR->ObjectPosition, $AR->ObjectUUID, $AR->RegionName, $AR->ReporterName, $AR->Screenshot) === false){
					throw new UnexpectedValueException('Call to API was successful, but required response sub-properties were missing.');
				}
				$results[] = WebUI\AbuseReport::r($AR->Number, $AR->Details, $AR->Location, $AR->UserName, $AR->Summary, $AR->Active, $AR->AssignedTo, $AR->Category, $AR->Checked, $AR->Notes, $AR->ObjectName, $AR->ObjectPosition, $AR->ObjectUUID, $AR->RegionName, $AR->ReporterName, $AR->Screenshot);
			}

			return new WebUI\AbuseReports($results);
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
			$result = $this->makeCallToAPI('SetWebLoginKey', array('PrincipalID'=>$for));
			if(isset($result->WebLoginKey) === false){
				throw new UnexpectedValueException('WebLoginKey value not present on API result, API call was made but not successful.');
			}else if(is_string($result->WebLoginKey) === false){
				throw new UnexpectedValueException('WebLoginKey value present on API result, but value was not a string as expected.');
			}else if(preg_match(self::regex_UUID, $result->WebLoginKey) !== 1){
				throw new UnexpectedValueException('WebLoginKey value present on API result, but value was not a valid UUID.');
			}
			return $result->WebLoginKey;
		}

//!	Get a list of regions in the AuroraSim install that match the specified flags.
/**
*	@param integer A bitfield corresponding to constants in Aurora::Framework::RegionFlags
*	@return array Currently returns an array, although may return a interface-specific Iterator in the future.
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*	@see Aurora::Addon::WebUI::fromEndPointResult()
*/
		public function GetRegions($flags){ // this doesn't work at the moment, there's a bug in the c# on the php5 branch of Aurora-WebUI
			if(is_integer($flags) === false){
				throw new InvalidArgumentException('RegionFlags argument should be supplied as integer.');
			}else if($flags < 0){
				throw new InvalidArgumentException('RegionFlags cannot be less than zero');
			}else if(RegionFlags::isValid($flags) === false){ // Aurora::Framework::RegionFlags::isValid() does do a check for integerness, but we want to throw a different exception message if it is an integer.
				throw new InvalidArgumentException('RegionFlags value is invalid, aborting call to API');
			}
			$result = $this->makeCallToAPI('GetRegions', array('RegionFlags'=>$flags));
			$response = array();
			foreach($result as $val){
				$response[] = WebUI\GridRegion::fromEndPointResult($val);
			}
			return $response;
		}
	}
}

//!	Code specific to the WebUI
namespace Aurora\Addon\WebUI{
	use InvalidArgumentException;

	use Countable;
	use Iterator;
	use IteratorAggregate;

	use Aurora\Services\Interfaces;
	use Aurora\Framework\RegionFlags;

//!	Implementation of Aurora::Services::Interfaces::GridRegion
	class GridRegion implements Interfaces\GridRegion{

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::RegionID()
		protected $RegionID;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionID
		public function RegionID(){
			return $this->RegionID;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::HttpPort()
		protected $HttpPort;
//!	@see Aurora::Addon::WebUI::GridRegion::$HttpPort
		public function HttpPort(){
			return $this->HttpPort;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::ServerURI()
		protected $ServerURI;
//!	@see Aurora::Addon::WebUI::GridRegion::$ServerURI
		public function ServerURI(){
			return $this->ServerURI;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::RegionName()
		protected $RegionName;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionName
		public function RegionName(){
			return $this->RegionName;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::RegionType()
		protected $RegionType;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionType
		public function RegionType(){
			return $this->RegionType;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionLocX()
		protected $RegionLocX;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionLocX
		public function RegionLocX(){
			return $this->RegionLocX;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionLocY()
		protected $RegionLocY;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionLocY
		public function RegionLocY(){
			return $this->RegionLocY;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionLocZ()
		protected $RegionLocZ;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionLocZ
		public function RegionLocZ(){
			return $this->RegionLocZ;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::EstateOwner()
		protected $EstateOwner;
//!	@see Aurora::Addon::WebUI::GridRegion::$EstateOwner
		public function EstateOwner(){
			return $this->EstateOwner;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionSizeX()
		protected $RegionSizeX;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionSizeX
		public function RegionSizeX(){
			return $this->RegionSizeX;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionSizeY()
		protected $RegionSizeY;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionSizeY
		public function RegionSizeY(){
			return $this->RegionSizeY;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionSizeZ()
		protected $RegionSizeZ;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionSizeZ
		public function RegionSizeZ(){
			return $this->RegionSizeZ;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::Flags()
		protected $Flags;
//!	@see Aurora::Addon::WebUI::GridRegion::$Flags
		public function Flags(){
			return $this->Flags;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::SessionID()
		protected $SessionID;
//!	@see Aurora::Addon::WebUI::GridRegion::$SessionID
		public function SessionID(){
			return $this->SessionID;
		}

//!	Converts integer-as-string to integer
		final protected static function stringMaybe2Integer(& $val){
			if(is_string($val) === true && ctype_digit($val) === true){
				$val = (integer)$val;
			}
		}

//!	We're making this a protected method because we're going to be using at least one public static method to deserialise some data for this class.
/**
*	@param integer $HttpPort
*	@param string $ServerURI
*	@param string $RegionName
*	@param string $RegionType
*	@param integer $RegionLocX
*	@param integer $RegionLocY
*	@param integer $RegionLocZ
*	@param string $EstateOwner
*	@param integer $RegionSizeX
*	@param integer $RegionSizeY
*	@param integer $RegionSizeZ
*	@param integer $Flags
*	@param string $SessionID
*/
		protected function __construct($RegionID, $HttpPort, $ServerURI, $RegionName, $RegionType, $RegionLocX, $RegionLocY, $RegionLocZ=0, $EstateOwner='00000000-0000-0000-0000-000000000000', $RegionSizeX=256, $RegionSizeY=256, $RegionSizeZ=256, $Flags=0, $SessionID='00000000-0000-0000-0000-000000000000'){
			self::stringMaybe2Integer($HttpPort);
			self::stringMaybe2Integer($RegionLocX);
			self::stringMaybe2Integer($RegionLocY);
			self::stringMaybe2Integer($RegionLocZ);
			self::stringMaybe2Integer($RegionSizeX);
			self::stringMaybe2Integer($RegionSizeY);
			self::stringMaybe2Integer($RegionSizeZ);
			self::stringMaybe2Integer($Flags);

			if(is_string($RegionID) === false){
				throw new InvalidArgumentException('RegionID should be a string');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $RegionID) !== 1){
				throw new InvalidArgumentException('RegionID was not a valid UUID');
			}else if(is_integer($HttpPort) === false){
				throw new InvalidArgumentException('HttpPort should be an integer');
			}else if($HttpPort < 0){
				throw new InvalidArgumentException('HttpPort should be greater than zero'); 
			}else if(is_string($ServerURI) === false){
				throw new InvalidArgumentException('ServerURI should be a string');
			}else if(strpos($ServerURI, 'http://') !== 0 && strpos($ServerURI, 'https://') !== 0){
				throw new InvalidArgumentException('ServerURI was not http or https');
			}else if(is_integer($RegionLocX) === false){
				throw new InvalidArgumentException('RegionLocX was not an integer');
			}else if(is_integer($RegionLocY) === false){
				throw new InvalidArgumentException('RegionLocY was not an integer');
			}else if(is_integer($RegionLocZ) === false){
				throw new InvalidArgumentException('RegionLocZ was not an integer');
			}else if(is_string($EstateOwner) === false){
				throw new InvalidArgumentException('EstateOwner was not a string');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $EstateOwner) !== 1){
				throw new InvalidArgumentException('EstateOwner was not a valid UUID');
			}else if(is_integer($RegionSizeX) === false){
				throw new InvalidArgumentException('RegionSizeX was not an integer');
			}else if(is_integer($RegionSizeY) === false){
				throw new InvalidArgumentException('RegionSizeY was not an integer');
			}else if(is_integer($RegionSizeZ) === false){
				throw new InvalidArgumentException('RegionSizeZ was not an integer');
			}else if(is_integer($Flags) === false){
				throw new InvalidArgumentException('Flags was not an integer');
			}else if(RegionFlags::isValid($Flags) === false){
				throw new InvalidArgumentException('Flags was not a valid RegionFlags bitfield');
			}else if(is_string($SessionID) === false){
				throw new InvalidArgumentException('SessionID was not a string');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $SessionID) !== 1){
				throw new InvalidArgumentException('SessionID was not a valid UUID');
			}

			$this->HttpPort    = $HttpPort;
			$this->ServerURI   = $ServerURI;
			$this->RegionName  = $RegionName;
			$this->RegionType  = $RegionType;
			$this->RegionLocX  = $RegionLocX;
			$this->RegionLocY  = $RegionLocY;
			$this->RegionLocZ  = $RegionLocZ;
			$this->EstateOwner = $EstateOwner;
			$this->RegionSizeX = $RegionSizeX;
			$this->RegionSizeY = $RegionSizeY;
			$this->RegionSizeZ = $RegionSizeZ;
			$this->Flags       = $Flags;
			$this->SessionID   = $SessionID;
		}

//!	For converting WebUI API end point results from json_decode()'d objects to instances of Aurora::Addon::WebUI::GridRegion
//!	@param object $result
//!	@return object instance of Aurora::Addon::WebUI::GridRegion
		public static function fromEndPointResult($result){
			if(is_object($result) === false){
				throw new InvalidArgumentException('result should be object');
			}else if(isset($result->uuid, $result->serverHttpPort, $result->serverURI, $result->regionName, $result->regionType, $result->locX, $result->locY, $result->locZ, $result->EstateOwner, $result->sizeX, $result->sizeY, $result->sizeZ, $result->Flags, $result->SessionID) === false){
				$missing = array();
				if(isset($result->uuid) === false){
					$missing[] = 'uuid';
				}
				if(isset($result->serverHttpPort) === false){
					$missing[] = 'serverHttpPort';
				}
				if(isset($result->serverURI) === false){
					$missing[] = 'serverURI';
				}
				if(isset($result->regionName) === false){
					$missing[] = 'regionName';
				}
				if(isset($result->regionType) === false){
					$missing[] = 'regionType';
				}
				if(isset($result->locX) === false){
					$missing[] = 'locX';
				}
				if(isset($result->locY) === false){
					$missing[] = 'locY';
				}
				if(isset($result->locZ) === false){
					$missing[] = 'locZ';
				}
				if(isset($result->EstateOwner) === false){
					$missing[] = 'EstateOwner';
				}
				if(isset($result->sizeX) === false){
					$missing[] = 'sizeX';
				}
				if(isset($result->sizeY) === false){
					$missing[] = 'sizeY';
				}
				if(isset($result->sizeZ) === false){
					$missing[] = 'sizeZ';
				}
				if(isset($result->Flags) === false){
					$missing[] = 'Flags';
				}
				if(isset($result->SessionID) === false){
					$missing[] = 'SessionID';
				}
				throw new InvalidArgumentException('Missing required properties: ' . implode(', ', $missing));
			}
			return new static($result->uuid, $result->serverHttpPort, $result->serverURI, $result->regionName, $result->regionType, $result->locX, $result->locY, $result->locZ, $result->EstateOwner, $result->sizeX, $result->sizeY, $result-> sizeZ, $result->Flags, $result->SessionID);
		}
	}

//! class for representing the online status result of an API query.
/**
*	Now the simplest approach would just be to return the object that from json_decode() in Aurora::Addon::WebUI::makeCallToAPI(), but stdClass doesn't prevent properties being removed or overwritten.
*/
	class OnlineStatus{
//!	Constructor used by Aurora::Addon::WebUI::OnlineStatus()
/**
*	We could- if we wanted to be really paranoid- examine the backtrace to make sure that the constructor is online called from within Aurora::Addon::WebUI::OnlineStatus(), but that would be a waste of resources.
*	@param boolean $Online TRUE means the grid is online, FALSE otherwise.
*	@param boolean $LoginEnabled TRUE means the grid has logins enabled, FALSE otherwise.
*	@see Aurora::Addon::WebUI::OnlineStatus::maybe2bool()
*	@see Aurora::Addon::WebUI::OnlineStatus::$Online
*	@see Aurora::Addon::WebUI::OnlineStatus::$LoginEnabled
*/
		public function __construct($Online, $LoginEnabled){
			self::maybe2bool($Online);
			self::maybe2bool($LoginEnabled);

			if(is_bool($Online) === false){
				throw new InvalidArgumentException('Online should be boolean');
			}else if(is_bool($LoginEnabled) === false){
				throw new InvalidArgumentException('LoginEnabled should be boolean');
			}

			$this->Online       = $Online;
			$this->LoginEnabled = $LoginEnabled;
		}

//!	boolean
//!	@see Aurora::Addon::WebUI::OnlineStatus::Online()
		protected $Online;
//!	@see Aurora::Addon::WebUI::OnlineStatus::$Online
//!	@return boolean TRUE if grid is online, FALSE otherwise.
		public function Online(){
			return $this->Online;
		}

//!	boolean
//!	@see Aurora::Addon::WebUI::OnlineStatus::LoginEnabled()
		protected $LoginEnabled;
//!	@return boolean TRUE if logins are enabled, FALSE otherwise.
//!	@see Aurora::Addon::WebUI::OnlineStatus::$LoginEnabled
		public function LoginEnabled(){
			return $this->LoginEnabled;
		}

//!	deduplication of code to convert arguments to Aurora::Addon::WebUI::OnlineStatus::__construct() to boolean.
//!	@param mixed $val passed by reference
		final protected static function maybe2bool(& $val){
			if(is_integer($val) === true){
				$val = ($val !== 0);
			}
		}
	}

//!	abstract implementation
	abstract class abstractUser implements Interfaces\User{

//!	protected constructor, should be hidden behind factory or registry methods. Assumes properties were already set.
		protected function __construct(){
			if(is_string($this->PrincipalID) === false){
				throw new InvalidArgumentException('User UUID must be a string.');
			}else if(preg_match(Aurora\Addon\WebUI::regex_UUID, $this->PrincipalID) === false){
				throw new InvalidArgumentException('User UUID was not a valid UUID.');
			}else if(is_string($this->FirstName) === false){
				throw new InvalidArgumentException('User first must be a string.');
			}else if(trim($this->FirstName) === ''){
				throw new InvalidArgumentException('User first name must not be an empty string.');
			}else if(is_string($this->LastName) === false){ // last names can be an empty string
				throw new InvalidArgumentException('User last name must be a string.');
			}
			$this->PrincipalID = strtolower($this->PrincipalID);
		}

//!	string UUID
//!	@see Aurora::Addon::WebUI::abstractUser::PrincipalID()
		protected $PrincipalID;
//!	@see Aurora::Addon::WebUI::abstractUser::$PrincipalID
		public function PrincipalID(){
			return $this->PrincipalID;
		}

//!	string First Name
//!	@see Aurora::Addon::WebUI::abstractUser::FirstName()
		protected $FirstName;
//!	@see Aurora::Addon::WebUI::abstractUser::$FirstName
		public function FirstName(){
			return $this->FirstName;
		}

//!	string Last Name
//!	@see Aurora::Addon::WebUI::abstractUser::LastName()
		protected $LastName;
//!	@see Aurora::Addon::WebUI::abstractUser::$LastName
		public function LastName(){
			return $this->LastName;
		}

//!	This is for child classes that won't have their own implementation
/**
*	@return string
*	@see Aurora::Addon::WebUI::abstractUser::FirstName()
*	@see Aurora::Addon::WebUI::abstractUser::LastName()
*/
		public function Name(){
			return trim($this->FirstName() . ' ' . $this->LastName()); // we use trim in case Aurora::Addon::WebUI::abstractUser::LastName() is an empty string.
		}
	}

//!	Lightweight generated user.
	class genUser extends abstractUser{
//!	Since this is a generated class, we need to override the parent constructor to set the properties, then call back to it.
/**
*	@param string $uuid Corresponds to Aurora::Services::Interfaces::User::PrincipalID()
*	@param string $first Corresponds to Aurora::Services::Interfaces::User::FirstName()
*	@param string $last Corresponds to Aurora::Services::Interfaces::User::LastName()
*/
		protected function __construct($uuid, $first, $last){
			$this->PrincipalID = $uuid;
			$this->FirstName   = $first;
			$this->LastName    = $last;
		}

//!	Since this is a generated class for non-unique entities, we're going to use a registry method.
/**
*	We're going to validate the UUID here, but leave the first/last name up to the class constructor, since we use the UUID as the registry key.
*	@param string $uuid Corresponds to Aurora::Services::Interfaces::User::PrincipalID()
*	@param string $first Corresponds to Aurora::Services::Interfaces::User::FirstName()
*	@param string $last Corresponds to Aurora::Services::Interfaces::User::LastName()
*	@return object instance of Aurora::Addon::WebUI::genUser
*/
		public static function r($uuid, $first=null, $last=null){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('User UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('User UUID was not a valid UUID.');
			}
			$uuid = strtolower($uuid);
			static $registry = array();
			if(isset($registry[$uuid]) === false){
				if(isset($first,$last) === false){
					throw new InvalidArgumentException('Cannot return an instance by UUID alone, user was never set.');
				}
				$registry[$uuid] = new static($uuid, $first, $last);
			}else if($registry[$uuid]->FirstName() !== $first || $registry[$uuid]->LastName() !== $last){ // assume a call was made to Aurora::Addon::WebUI::ChangeName()
				$registry[$uuid] = new static($uuid, $first, $last);
			}
			return $registry[$uuid];
		}
	}

	abstract class commonUser extends genUser{

//!	We need to add in some more properties and validation since we're extending another class.
/**
*	@param string $uuid UUID for the user
*	@param string $name the user's name.
*	@param string $email either a valid email address or an empty string.
*/
		protected function __construct($uuid, $name, $email){
			if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be a string.');
			}else if(trim($name) === ''){
				throw new InvalidArgumentException('Name cannot be an empty string.');
			}else if(is_string($email) === false){
				throw new InvalidArgumentException('Email address must be string.');
			}else if($email !== '' && is_email($email) === false){
				throw new InvalidArgumentException('Email address not valid.');
			}

			$firstName = explode(' ', $name);
			$lastName = array_pop($firstName);
			if($lastName === $name){
				$lastName = '';
				$firstName = $name;
			}else{
				$firstName = implode(' ', $firstName); // this is to future proof first names with multiple spaces.
			}

			$this->Email    = $email;
			$this->Name     = $name;

			parent::__construct($uuid, $firstName, $lastName);
		}

//!	string user name
//!	@see Aurora::Addon::WebUI::GridUserInfo::Name()
		protected $Name;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$Name
		public function Name(){
			return $this->Name;
		}

//!	string user email
//!	@see Aurora::Addon::WebUI::GridUserInfo::Email()
		protected $Email;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$Email
		public function Email(){
			return $this->Email;
		}
	}

//!	GridUserInfo class. Returned by Aurora::Addon::WebUI::GetGridUserInfo()
	class GridUserInfo extends commonUser{

//!	We need to add in some more properties and validation since we're extending another class.
/**
*	@param string $uuid UUID for the user
*	@param string $name the user's name.
*	@param string $homeUUID the UUID of the user's home region.
*	@param string $homeName the name of the user's home region.
*	@param string $onlineStatus TRUE if the user is currently online, FALSE otherwise.
*	@param string $email either a valid email address or an empty string.
*/
		protected function __construct($uuid, $name, $homeUUID, $homeName, $onlineStatus, $email){
			if(is_string($homeUUID) === false){
				throw new InvalidArgumentException('Home region UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $homeUUID) !== 1){
				throw new InvalidArgumentException('Home region UUID was not a valid UUID.');
			}else if(is_string($homeName) === false){
				throw new InvalidArgumentException('Home region name must be a string.');
			}else if($homeUUID !== '00000000-0000-0000-0000-000000000000' && trim($homeName) === ''){
				throw new InvalidArgumentException('Home region name cannot be an empty string.');
			}else if(is_bool($onlineStatus) === false){
				throw new InvalidArgumentException('Online status must be a boolean.');
			}

			$this->HomeUUID = $homeUUID;
			$this->HomeName = $homeName;
			$this->Online   = $onlineStatus;

			parent::__construct($uuid, $name, $email);
		}

//!	Since this is a generated class for non-unique entities, we're going to use a registry method.
/**
*	@param string $uuid UUID for the user
*	@param string $name the user's name.
*	@param string $homeUUID the UUID of the user's home region.
*	@param string $homeName the name of the user's home region.
*	@param string $onlineStatus TRUE if the user is currently online, FALSE otherwise.
*	@param string $email either a valid email address or an empty string.
*	@return object instance of Aurora::Addon::WebUI::GridUserInfo
*/
		public static function r($uuid, $name=null, $homeUUID=null, $homeName=null, $onlineStatus=null, $email=null){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}else if((isset($name) || isset($homeUUID) || isset($homeName) || isset($onlineStatus) || isset($email)) && isset($name, $homeUUID, $homeName, $onlineStatus, $email) === false){
				throw new InvalidArgumentException('If the grid info of the user has changed, all info must be specified.');
			}
			$uuid = strtolower($uuid);
			static $registry = array();
			if(isset($registry[$uuid]) === false){
				if(isset($name, $homeUUID, $homeName, $onlineStatus, $email) === false){
					throw new InvalidArgumentException('Cannot return grid info for user by UUID, grid info has not been set.');
				}
				$registry[$uuid] = new static($uuid, $name, $homeUUID, $homeName, $onlineStatus, $email);
			}else if(isset($name, $homeUUID, $homeName, $onlineStatus, $email) === true){
				$info = $registry[$uuid];
				if(
					$info->Name()         !== $name         ||
					$info->HomeUUID()     !== $homeUUID     ||
					$info->HomeName()     !== $homeName     ||
					$info->Online() !== $onlineStatus ||
					$info->Email()        !== $email
				){
					$registry[$uuid] = new static($uuid, $name, $homeUUID, $homeName, $onlineStatus, $email);
				}
			}
			return $registry[$uuid];
		}

//!	string user home region UUID
//!	@see Aurora::Addon::WebUI::GridUserInfo::HomeUUID()
		protected $HomeUUID;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$HomeUUID
		public function HomeUUID(){
			return $this->HomeUUID;
		}

//!	string user home region name
//!	@see Aurora::Addon::WebUI::GridUserInfo::HomeName()
		protected $HomeName;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$HomeName
		public function HomeName(){
			return $this->HomeName;
		}

//!	boolean TRUE if Online, FALSE otherwise
//!	@see Aurora::Addon::WebUI::GridUserInfo::Online()
		protected $Online;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$Online
		public function Online(){
			return $this->Online;
		}
	}

//!	UserProfile class. Returned by Aurora::Addon::WebUI::GetProfile()
	class UserProfile extends commonUser{

//!	We need to add in some more properties and validation since we're extending another class.
/**
*	@param string $uuid Account UUID
*	@param string $name Account name
*	@param string $email Account email address
*	@param integer $created unix timestamp of when the account was created
*	@param boolean $allowPublish TRUE if the profile shows in search, FALSE otherwise
*	@param boolean $maturePublish TRUE if the profile does not show in general search, FALSE otherwise
*	@param integer $wantToMask mask of wants
*	@param string $wantToText description of wants
*	@param integer $canDoMask mask of things the user can do
*	@param string $canDoText description of things the user can do
*	@param string $languages Languages the user understands
*	@param string $image Asset UUID of profile image
*	@param string $aboutText Descriptive "about" text of a user.
*	@param string $firstLifeImage Asset UUID of "first life" profile image
*	@param string $firstLifeAboutText Descriptive "first life" about text of a user.
*	@param string $webURL User's external (third-party) web page
*	@param string $displayName user's display name that can appear in supported viewers as an alternative to their account name.
*	@param string $partnerUUID UUID of the user's partner account
*	@param boolean $visible TRUE if the user's online status is visible to everyone, FALSE otherwise.
*	@param string $customType custom account type.
*	@param string $notes Stringified JSON data of account notes.
*/
		protected function __construct($uuid, $name='', $email='', $created=0, $allowPublish=false, $maturePublish=false, $wantToMask=0, $wantToText='', $canDoMask=0, $canDoText='', $languages='', $image='00000000-0000-0000-0000-000000000000', $aboutText='', $firstLifeImage='00000000-0000-0000-0000-000000000000', $firstLifeAboutText='', $webURL='', $displayName='', $partnerUUID='00000000-0000-0000-0000-000000000000', $visible=false, $customType='', $notes='', $RLName=null, $RLAddress=null, $RLZip=null, $RLCity=null, $RLCountry=null){
			if(is_string($created) === true && ctype_digit($created) === true){
				$created = (integer)$created;
			}
			if(is_string($wantToMask) === true && ctype_digit($wantToMask) === true){
				$wantToMask = (integer)$wantToMask;
			}
			if(is_string($canDoMask) === true && ctype_digit($canDoMask) === true){
				$canDoMask = (integer)$canDoMask;
			}

			if(is_integer($created) === false){
				throw new InvalidArgumentException('created must be an integer.');
			}else if(is_bool($allowPublish) === false){
				throw new InvalidArgumentException('allowPublish must be a boolean.');
			}else if(is_bool($maturePublish) === false){
				throw new InvalidArgumentException('maturePublish must be a boolean.');
			}else if(is_integer($wantToMask) === false){
				throw new InvalidArgumentException('wantToMask must be an integer.');
			}else if(is_string($wantToText) === false){
				throw new InvalidArgumentException('wantToText must be a string.');
			}else if(is_integer($canDoMask) === false){
				throw new InvalidArgumentException('canDoMask must be an integer.');
			}else if(is_string($canDoText) === false){
				throw new InvalidArgumentException('canDoText must be an integer.');
			}else if(is_string($languages) === false){
				throw new InvalidArgumentException('languages must be a string.');
			}else if(is_string($image) === false){
				throw new InvalidArgumentException('image asset UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $image) !== 1){
				throw new InvalidArgumentException('image asset UUID was not a valid UUID.');
			}else if(is_string($aboutText) === false){
				throw new InvalidArgumentException('about text must be a string.');
			}else if(is_string($firstLifeImage) === false){
				throw new InvalidArgumentException('first life image asset UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $firstLifeImage) !== 1){
				throw new InvalidArgumentException('first life image asset UUID was not a valid UUID.');
			}else if(is_string($firstLifeAboutText) === false){
				throw new InvalidArgumentException('first life about text must be a string.');
			}else if(is_string($webURL) === false){
				throw new InvalidArgumentException('third-party user web profile must be a string.');
			}else if(trim($webURL) !== '' && in_array(parse_url($webURL, \PHP_URL_SCHEME), array('http', 'https')) === false){
				throw new InvalidArgumentException('third-party user web profile must be either http or https url.');
			}else if(is_string($displayName) === false){
				throw new InvalidArgumentException('display name must be a string.');
			}else if(is_string($partnerUUID) === false){
				throw new InvalidArgumentException('partner account UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $partnerUUID) !== 1){
				throw new InvalidArgumentException('partner account UUID was not a valid UUID.');
			}else if(is_bool($visible) === false){
				throw new InvalidArgumentException('visible must be a boolean.');
			}else if(is_string($customType) === false){
				throw new InvalidArgumentException('custom type must be a string.');
			}else if(is_string($notes) === false){
				throw new InvalidArgumentException('notes must be a string.');
			}else if(json_decode($notes) === false){
				throw new InvalidArgumentException('notes must be a valid stringified JSON entity.');
			}
			
			$this->Created            = $created;
			$this->AllowPublish       = $allowPublish;
			$this->MaturePublish      = $maturePublish;
			$this->WantToMask         = $wantToMask;
			$this->WantToText         = trim($wantToText);
			$this->CanDoMask          = $canDoMask;
			$this->CanDoText          = trim($canDoText);
			$this->Languages          = trim($languages);
			$this->Image              = $image;
			$this->AboutText          = trim($aboutText);
			$this->FirstLifeImage     = $firstLifeImage;
			$this->FirstLifeAboutText = trim($firstLifeAboutText);
			$this->WebURL             = trim($webURL);
			$this->DisplayName        = trim($displayName);
			$this->PartnerUUID        = $partnerUUID;
			$this->Visible            = $visible;
			$this->CustomType         = trim($customType);
			$this->Notes              = $notes;

			if(isset($RLName, $RLAddress, $RLZip, $RLCity, $RLCountry) === true){
				$this->RLInfo = new RLInfo($RLName, $RLAddress, $RLZip, $RLCity, $RLCountry);
			}else if(isset($RLName) || isset($RLAddress) || isset($RLZip) || isset($RLCity) || isset($RLCountry)){
				throw new InvalidArgumentException('If RL information is being specified, all RL information needs to be specified.');
			}

			parent::__construct($uuid, $name, $email);
		}

		public static function r($uuid, $name=null, $email=null, $created=null, $allowPublish=null, $maturePublish=null, $wantToMask=null, $wantToText=null, $canDoMask=null, $canDoText=null, $languages=null, $image=null, $aboutText=null, $firstLifeImage=null, $firstLifeAboutText=null, $webURL=null, $displayName=null, $partnerUUID=null, $visible=null, $customType=null, $notes=null, $RLName=null, $RLAddress=null, $RLZip=null, $RLCity=null, $RLCountry=null){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}else if((
				(isset($name) || isset($email) || isset($created) || isset($allowPublish) || isset($maturePublish) || isset($wantToMask) || isset($wantToText) || isset($canDoMask) || isset($canDoText) || isset($languages) || isset($image) || isset($aboutText) || isset($firstLifeImage) || isset($firstLifeAboutText) || isset($webURL) || isset($displayName) || isset($partnerUUID) || isset($visible) || isset($customType) || isset($notes)) &&
				isset($name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes) === false) &&
				((isset($RLName) || isset($RLAddress) || isset($RLZip) || isset($RLCity) || isset($RLCountry)) && 
				isset($RLName, $RLAddress, $RLZip, $RLCity, $RLCountry) === false)
			){
				throw new InvalidArgumentException('If the profile info of the user has changed, all info must be specified.');
			}
			if(is_array($wantToMask) === true){
				if(count($wantToMask) > 32){
					throw new InvalidArgumentException('Cannot convert mask array to bitfield if array has greater than 32 entries');
				}
				$val = 0;
				foreach($wantToMask as $k=>$v){
					if($v != 0){
						$val |= 1 << $k;
					}
				}
				$wantToMask = $val;
			}
			if(is_array($canDoMask) === true){
				if(count($canDoMask) > 32){
					throw new InvalidArgumentException('Cannot convert mask array to bitfield if array has greater than 32 entries',1);
				}
				$val = 0;
				foreach($canDoMask as $k=>$v){
					if($v != 0){
						$val |= 1 << $k;
					}
				}
				$canDoMask = $val;
			}

			$uuid = strtolower($uuid);
			static $registry = array();
			if(isset($registry[$uuid]) === false){
				if(isset($name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes) === false){
					throw new InvalidArgumentException('Cannot return profile for user by UUID, profile data has not been set.');
				}
				$registry[$uuid] = new static($uuid, $name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes, $RLName, $RLAddress, $RLZip, $RLCity, $RLCountry);
			}else if(isset($uuid, $name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes) === true){
				$info = $registry[$uuid];
				if(
					$info->Name()               !== $name               ||
					$info->Email()              !== $email              ||
					$info->Created()            !== $created            ||
					$info->AllowPublish()       !== $allowPublish       ||
					$info->MaturePublish()      !== $maturePublish      ||
					$info->WantToMask()         !== $wantToMask         ||
					$info->WantToText()         !== $wantToText         ||
					$info->CanDoMask()          !== $canDoMask          ||
					$info->CanDoText()          !== $canDoText          ||
					$info->Languages()          !== $languages          ||
					$info->Image()              !== $image              ||
					$info->AboutText()          !== $aboutText          ||
					$info->FirstLifeImage()     !== $firstLifeImage     ||
					$info->FirstLifeAboutText() !== $firstLifeAboutText ||
					$info->WebURL()             !== $webURL             ||
					$info->DisplayName()        !== $displayName        ||
					$info->PartnerUUID()        !== $partnerUUID        ||
					$info->Visible()            !== $visible            ||
					$info->CustomType()         !== $customType         ||
					$info->Notes()              !== $notes              ||
					(string)$info->RLInfo()     !== trim(implode("\n", array($RLName, $RLAddress, $RLZip, $RLCity, $RLCountry)))
				){
					$registry[$uuid] = new static($uuid, $name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes, $RLName, $RLAddress, $RLZip, $RLCity, $RLCountry);
				}
			}
			return $registry[$uuid];
		}

//!	integer unix timestamp of when the account was created.
//!	@see Aurora::Addon::WebUI::UserProfile::Created()
		protected $Created;
//!	@see Aurora::Addon::WebUI::UserProfile::$Created
		public function Created(){
			return $this->Created;
		}

//!	boolean Show in search
//!	@see Aurora::Addon::WebUI::UserProfile::AllowPublish()
		protected $AllowPublish;
//!	@see Aurora::Addon::WebUI::UserProfile::$AllowPublish
		public function AllowPublish(){
			return $this->AllowPublish;
		}

//!	boolean Mature publishing
//!	@see Aurora::Addon::WebUI::UserProfile::MaturePublish()
		protected $MaturePublish;
//!	@see Aurora::Addon::WebUI::UserProfile::$MaturePublish
		public function MaturePublish(){
			return $this->MaturePublish;
		}

//!	boolean Mask of wants
//!	@see Aurora::Addon::WebUI::UserProfile::WantToMask()
		protected $WantToMask;
//!	@see Aurora::Addon::WebUI::UserProfile::$WantToMask
		public function WantToMask(){
			return $this->WantToMask;
		}

//!	string String of wants
//!	@see Aurora::Addon::WebUI::UserProfile::WantToText()
		protected $WantToText;
//!	@see Aurora::Addon::WebUI::UserProfile::$WantToText
		public function WantToText(){
			return $this->WantToText;
		}

//!	boolean Mask of things the user can do
//!	@see Aurora::Addon::WebUI::UserProfile::CanDoMask()
		protected $CanDoMask;
//!	@see Aurora::Addon::WebUI::UserProfile::$CanDoMask
		public function CanDoMask(){
			return $this->CanDoMask;
		}

//!	string String of things the user can do
//!	@see Aurora::Addon::WebUI::UserProfile::CanDoText()
		protected $CanDoText;
//!	@see Aurora::Addon::WebUI::UserProfile::$CanDoText
		public function CanDoText(){
			return $this->CanDoText;
		}

//!	string Languages the person understands.
//!	@see Aurora::Addon::WebUI::UserProfile::Languages()
		protected $Languages;
//!	@see Aurora::Addon::WebUI::UserProfile::$Languages
		public function Languages(){
			return $this->Languages;
		}

//!	string UUID for the asset used in the main section of the user's profile.
//!	@see Aurora::Addon::WebUI::UserProfile::Image()
		protected $Image;
//!	@see Aurora::Addon::WebUI::UserProfile::$Image
		public function Image(){
			return $this->Image;
		}

//!	string Descriptive "about" text of user.
//!	@see Aurora::Addon::WebUI::UserProfile::AboutText()
		protected $AboutText;
//!	@see Aurora::Addon::WebUI::UserProfile::$AboutText
		public function AboutText(){
			return $this->AboutText;
		}

//!	string UUID for the asset used in the "first life" section of the user's profile.
//!	@see Aurora::Addon::WebUI::UserProfile::FirstLifeImage()
		protected $FirstLifeImage;
//!	@see Aurora::Addon::WebUI::UserProfile::$FirstLifeImage
		public function FirstLifeImage(){
			return $this->FirstLifeImage;
		}

//!	string Descriptive "first life" text of user.
//!	@see Aurora::Addon::WebUI::UserProfile::FirstLifeAboutText()
		protected $FirstLifeAboutText;
//!	@see Aurora::Addon::WebUI::UserProfile::$FirstLifeAboutText
		public function FirstLifeAboutText(){
			return $this->FirstLifeAboutText;
		}

//!	string User's external (third-party) web page
//!	@see Aurora::Addon::WebUI::UserProfile::WebURL()
		protected $WebURL;
//!	@see Aurora::Addon::WebUI::UserProfile::$WebURL
		public function WebURL(){
			return $this->WebURL;
		}

//!	string user's display name that can appear in supported viewers as an alternative to their account name.
//!	@see Aurora::Addon::WebUI::UserProfile::DisplayName()
		protected $DisplayName;
//!	@see Aurora::Addon::WebUI::UserProfile::$DisplayName
		public function DisplayName(){
			return $this->DisplayName;
		}

//!	string UUID of the user's partner account
//!	@see Aurora::Addon::WebUI::UserProfile::PartnerUUID()
		protected $PartnerUUID;
//!	@see Aurora::Addon::WebUI::UserProfile::$PartnerUUID
		public function PartnerUUID(){
			return $this->PartnerUUID;
		}

//!	boolean Show online status
//!	@see Aurora::Addon::WebUI::UserProfile::Visible()
		protected $Visible;
//!	@see Aurora::Addon::WebUI::UserProfile::$Visible
		public function Visible(){
			return $this->Visible;
		}

//!	string custom type
//!	@see Aurora::Addon::WebUI::UserProfile::CustomType()
		protected $CustomType;
//!	@see Aurora::Addon::WebUI::UserProfile::$CustomType
		public function CustomType(){
			return $this->CustomType;
		}

//!	string profile notes.
//!	@see Aurora::Addon::WebUI::UserProfile::Notes()
		protected $Notes;
//!	@see Aurora::Addon::WebUI::UserProfile::$Notes
		public function Notes(){
			return $this->Notes;
		}

//!	mixed Either an instance of Aurora::Addon::WebUI::RLInfo or NULL.
//!	@see Aurora::Addon::WebUI::UserProfile::RLInfo()
		protected $RLInfo = null;
//!	@see Aurora::Addon::WebUI::UserProfile::$RLInfo
		public function RLInfo(){
			return $this->RLInfo;
		}
	}

//!	Encapsulating real-life/meatspace info in a class so that Aurora::Addon::WebUI::UserProfile::RLInfo() can simply return NULL to indicate the absence of such information.
	class RLInfo implements IteratorAggregate{
//!	public constructor
/**
*	Although it's likely this information will be unique, until it becomes used repeatedly inside a single workflow,
*	 we're not going to do a registry method for this class as it's currently only used inside Aurora::Addon::WebUI::UserProfile::__construct() which is hidden behind a registry method.
*	@param string $name meatspace name of account holder.
*	@param string $address postal address of user.
*	@param string $zip zip/postal code of user.
*	@param string $city meatspace city of user.
*	@param string $country meatspace country of user.
*/
		public function __construct($name, $address, $zip, $city, $country){
			if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be a string.');
			}else if(is_string($address) === false){
				throw new InvalidArgumentException('Address must be a string.');
			}else if(is_string($zip) === false){
				throw new InvalidArgumentException('Zip must be a string.');
			}else if(is_string($city) === false){
				throw new InvalidArgumentException('City must be as tring.');
			}else if(is_string($country) === false){
				throw new InvalidArgumentException('Country must be a string.');
			}

			$this->Name = trim($name);
			$this->Address = trim($address);
			$this->Zip = trim($zip);
			$this->City = trim($city);
			$this->Country = trim($country);
		}

//!	string meatspace name of account holder.
//!	@see Aurora::Addon::WebUI::RLInfo::Name()
		protected $Name;
//!	@see Aurora::Addon::WebUI::RLInfo::$Name
		public function Name(){
			return $this->Name;
		}

//!	string postal address of user.
//!	@see Aurora::Addon::WebUI::RLInfo::Address()
		protected $Address;
//!	@see Aurora::Addon::WebUI::RLInfo::$Address
		public function Address(){
			return $this->Address;
		}

//!	string zip/postal code of user.
//!	@see Aurora::Addon::WebUI::RLInfo::Zip()
		protected $Zip;
//!	@see Aurora::Addon::WebUI::RLInfo::$Zip
		public function Zip(){
			return $this->Zip;
		}

//!	string meatspace city of user
//!	@see Aurora::Addon::WebUI::RLInfo::City()
		protected $City;
//!	@see Aurora::Addon::WebUI::RLInfo::$City
		public function City(){
			return $this->City;
		}

//!	string meatspace country of user
//!	@see Aurora::Addon::WebUI::RLInfo::Country()
		protected $Country;
//!	@see Aurora::Addon::WebUI::RLInfo::$Country
		public function Country(){
			return $this->Country;
		}

//!	@return string
		public function __toString(){
			return trim(implode("\n", array(
				$this->Name(),
				$this->Address(),
				$this->Zip(),
				$this->City(),
				$this->Country()
			)));
		}

//!	@return object an instance of Aurora::Addon::WebUI::RLInfoIterator corresponding to this object
		public function getIterator(){
			return RLInfoIterator::r($this);
		}
	}

	abstract class abstractIterator implements Iterator, Countable{
//!	array holds the values of the iterator class
		protected $data = array();

		public function current(){
			return current($this->data);
		}

		public function key(){
			return key($this->data);
		}

		public function next(){
			next($this->data);
		}

		public function rewind(){
			reset($this->data);
		}

		public function valid(){
			return $this->key() !== null;
		}

		public function count(){
			return count($this->data);
		}
	}

	abstract class abstractUserIterator extends abstractIterator{

//!	public constructor
/**
*	Since Aurora::Addon::WebUI::abstractUserIterator does not implement methods for appending values, calling the constructor with no arguments is a shorthand means of indicating there are no abstract users available.
*	@param mixed $archives an array of Aurora::Addon::WebUI::abstractUser instances or NULL
*/
		public function __construct(array $archives=null){
			if(isset($archives) === true){
				foreach($archives as $v){
					if(($v instanceof abstractUser) === false){
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::abstractUser should be included in the array passed to Aurora::Addon::WebUI::abstractUserIterator::__construct()');
					}
				}
				reset($archives);
				$this->data = $archives;
			}
		}
	}

//!	This is Iterator here could be considered pure laziness.
	class RLInfoIterator extends abstractIterator{

//!	this gets hidden behind a registry method.
/**
*	@param object $RLInfo an instance of Aurora::Addon::WebUI::RLInfo
*/
		protected function __construct(RLInfo $RLInfo){
			$this->data = array(
				'RLName'    => $RLInfo->Name(),
				'RLAddress' => $RLInfo->Address(),
				'RLZip'     => $RLInfo->Zip(),
				'RLCity'    => $RLInfo->City(),
				'RLCountry' => $RLInfo->Country()
			);
		}

//!	Registry method.
/**
*	@param object $RLInfo an instance of Aurora::Addon::WebUI::RLInfo
*	@return object instance of Aurora::Addon::WebUI::RLInfoIterator
*/
		public static function r(RLInfo $RLInfo){
			static $registry = array();
			$hash = spl_object_hash($RLInfo);
			if(isset($registry[$hash]) === false){
				$registry[$hash] = new static($RLInfo);
			}
			return $registry[$hash];
		}
	}

//!	basic stub class for individual avatar archives.
	class basicAvatarArchive{

//!	We're not going to use a registry method here now (although we may in the future), so we're leaving the constructor as a public method.
/**
*	@param string $name Name of the archive
*	@param string $snapshot UUID of a texture that shows off this archive.
*/
		public function __construct($name, $snapshot){
			if(is_string($name) === true){
				$name = trim($name);
			}

			if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be a string.');
			}else if(is_string($snapshot) === false){
				throw new InvalidArgumentException('Snapshot must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $snapshot) !== 1){
				throw new InvalidArgumentException('Snapshot was not a valid UUID.');
			}

			$this->Name = $name;
			$this->Snapshot = $snapshot;
		}

//!	string Name of the archive
//!	@see Aurora::Addon::WebUI::basicAvatarArchive::Name()
		protected $Name;
//!	@see Aurora::Addon::WebUI::basicAvatarArchive::$Name
		public function Name(){
			return $this->Name;
		}

//!	string UUID of a texture that shows off this archive
//!	@see Aurora::Addon::WebUI::basicAvatarArchive::Snapshot()
		protected $Snapshot;
//!	@see Aurora::Addon::WebUI::basicAvatarArchive::$Snapshot
		public function Snapshot(){
			return $this->Snapshot;
		}
	}

//!	Iterator for instances of Aurora::Addon::WebUI::basicAvatarArchive
	class AvatarArchives extends abstractIterator{

//!	public constructor
/**
*	Since Aurora::Addon::WebUI::AvatarArchives does not implement methods for appending values, calling the constructor with no arguments is a shorthand means of indicating there are no avatar archives available.
*	@param mixed $archives an array of Aurora::Addon::WebUI::basicAvatarArchive instances or NULL
*/
		public function __construct(array $archives=null){
			if(isset($archives) === true){
				foreach($archives as $v){
					if(($v instanceof basicAvatarArchive) === false){
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::basicAvatarArchive should be included in the array passed to Aurora::Addon::WebUI::AvatarArchives::__construct()');
					}
				}
				reset($archives);
				$this->data = $archives;
			}
		}
	}

//!	SearchUser class. Included in result returned by Aurora::Addon::WebUI::FindUsers()
	class SearchUser extends abstractUser{

//!	We need to add in some more properties and validation since we're extending another class.
/**
*	@param string $uuid Account UUID
*	@param string $name Account name
*	@param integer $created unix timestamp of when the account was created
*	@param integer $userFlags bitfield of user flags
*/
		protected function __construct($uuid, $name, $created, $userFlags){
			if(is_integer($created) === false){
				throw new InvalidArgumentException('Created timestamp must be an integer.');
			}else if(is_integer($userFlags) === false){
				throw new InvalidArgumentException('User Flags must be an integer.');
			}

			$this->Created   = $created;
			$this->UserFlags = $userFlags;

			$firstName = explode(' ', $name);
			$lastName = array_pop($firstName);
			if($lastName === $name){
				$lastName = '';
				$firstName = $name;
			}else{
				$firstName = implode(' ', $firstName); // this is to future proof first names with multiple spaces.
			}

			parent::__construct($uuid, $firstName, $lastName);
		}

//!	Registry method.
/**
*	@param string $uuid Account UUID
*	@param string $name Account name
*	@param integer $created unix timestamp of when the account was created
*	@param integer $userFlags bitfield of user flags
*/
		public static function r($uuid, $name=null, $created=null, $userFlags=null){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}

			$uuid = strtolower($uuid);
			static $registry = array();
			$create = (isset($registry[$uuid]) === false);

			if($create === false && isset($name, $created, $userFlags) === true){
				$user = $registry[$uuid];
				$create = ($user->Name() !== $name || $user->Created() !== $created || $user->UserFlags !== $userFlags);
			}else if($created === true && isset($name, $created, $userFlags) === false){
				throw new InvalidArgumentException('Cannot create an instance of Aurora::Addon::WebUI::SearchUser if no information is specified.');
			}

			if($create){
				$registry[$uuid] = new static($uuid, $name, $created, $userFlags);
			}

			return $registry[$uuid];
		}

//!	integer unix timestamp of when the account was created.
//!	@see Aurora::Addon::WebUI::SearchUser::Created()
		protected $Created;
//!	@see Aurora::Addon::WebUI::SearchUser::$Created
		public function Created(){
			return $this->Created;
		}

//!	integer bitfield of user flags
//!	@see Aurora::Addon::WebUI::SearchUser::UserFlags()
		protected $UserFlags;
//!	@see Aurora::Addon::WebUI::SearchUser::$UserFlags
		public function UserFlags(){
			return $this->UserFlags;
		}
	}

//!	SearchUserResults iterator. Returned by Aurora::Addon::WebUI::FindUsers
	class SearchUserResults extends abstractUserIterator{

//!	public constructor
/**
*	Since Aurora::Addon::WebUI::SearchUserResults does not implement methods for appending values, calling the constructor with no arguments is a shorthand means of indicating there are no search users available.
*	@param mixed $archives an array of Aurora::Addon::WebUI::SearchUser instances or NULL
*/
		public function __construct(array $users=null){
			if(isset($users) === true){
				foreach($users as $v){
					if(($v instanceof SearchUser) === false){
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::SearchUser should be included in the array passed to Aurora::Addon::WebUI::SearchUserResults::__construct()');
					}
				}
				reset($users);
				$this->data = $users;
			}
		}
	}

//!	AbuseReport class. Included in result returned by Aurora::Addon::WebUI::GetAbuseReports()
	class AbuseReport{

//!	protected constructor, we hide it behind a registry method.
/**
*	@param integer $number looks like the primary key according to IAbuseReports.GetAbuseReport
*	@param string $details abuse report details
*	@param string $location abuse report location
*	@param string $username name of the account being reported
*	@param string $summary summary of abuse report
*	@param boolean $active whether or not the abuse report is open.
*	@param string $assignedTo name of admin the abuse report is assigned to.
*	@param string $category category of abuse
*	@param boolean $checked not entirely sure what this does.
*	@param string $notes notes on the abuse report.
*	@param string $objectName Name of object being reported (if applicable)
*	@param string $objectPosition Position of object being reported (if applicable)
*	@param string $objectUUID UUID of object being reported (if applicable)
*	@param string $regionName region the report originated from
*	@param string $reporterName account name of the user who filed the abuse report
*	@parama string $screenshot asset UUID of screenshot attached to the abuse report
*/
		protected function __construct($number, $details, $location, $userName, $summary, $active, $assignedTo, $category, $checked, $notes, $objectName, $objectPosition, $objectUUID, $regionName, $reporterName, $screenshot){
			if(is_integer($number) === false){
				throw new InvalidArgumentException('AR Number must be an integer.');
			}else if(is_string($details) === false){
				throw new InvalidArgumentException('AR Details must be a string.');
			}else if(is_string($location) === false){
				throw new InvalidArgumentException('AR Location must be a string.');
			}else if(is_string($username) === false){
				throw new InvalidArgumentException('AR accused name must be a string.');
			}else if(is_string($summary) === false){
				throw new InvalidArgumentException('AR summary must be a string.');
			}else if(is_bool($active) === false){
				throw new InvalidArgumentException('AR activity flag must be a boolean.');
			}else if(is_string($assignedTo) === false){
				throw new InvalidArgumentException('AR admin assignee name must be a string.');
			}else if(is_string($category) === false){
				throw new InvalidArgumentException('AR category must be a string.');
			}else if(is_bool($checked) === false){
				throw new InvalidArgumentException('AR checked flag must be a boolean.');
			}else if(is_string($notes) === false){
				throw new InvalidArgumentException('AR notes must be a string.');
			}else if(is_string($objectName) === false){
				throw new InvalidArgumentException('AR object name must be a string.');
			}else if(is_string($objectPosition) === false){
				throw new InvalidArgumentException('AR object position must be a string.');
			}else if(is_string($objectUUID) === false){
				throw new InvalidArgumentException('AR object UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $objectUUID) !== 1){
				throw new InvalidArgumentException('AR object UUID must be a valid UUID.');
			}else if(is_string($regionName) === false){
				throw new InvalidArgumentException('AR region of origin must be a string.');
			}else if(is_string($reporterName) === false){
				throw new InvalidArgumentException('AR reporter name must be as string.');
			}else if(is_string($screenshot) === false){
				throw new InvalidArgumentException('AR screenshot UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $screenshot) === false){
				throw new InvalidArgumentException('AR screenshot UUID must be a valid UUID.');
			}

			$this->Number         = $number;
			$this->Details        = trim($details);
			$this->Location       = trim($location);
			$this->UserName       = trim($userName);
			$this->Summary        = trim($summary);
			$this->Active         = $active;
			$this->AssignedTo     = trim($assignedTo);
			$this->Category       = trim($category);
			$this->Checked        = $checked;
			$this->Notes          = trim($notes);
			$this->ObjectName     = trim($objectName);
			$this->ObjectPosition = trim($objectPosition);
			$this->ObjectUUID     = $objectUUID;
			$this->RegionName     = trim($regionName);
			$this->ReporterName   = trim($reporterName);
			$this->Screenshot     = $screenshot;
		}

//!	registry method
/**
*	@param integer $number looks like the primary key according to IAbuseReports.GetAbuseReport
*	@param string $details abuse report details
*	@param string $location abuse report location
*	@param string $username name of the account being reported
*	@param string $summary summary of abuse report
*	@param boolean $active whether or not the abuse report is open.
*	@param string $assignedTo name of admin the abuse report is assigned to.
*	@param string $category category of abuse
*	@param boolean $checked not entirely sure what this does.
*	@param string $notes notes on the abuse report.
*	@param string $objectName Name of object being reported (if applicable)
*	@param string $objectPosition Position of object being reported (if applicable)
*	@param string $objectUUID UUID of object being reported (if applicable)
*	@param string $regionName region the report originated from
*	@param string $reporterName account name of the user who filed the abuse report
*	@parama string $screenshot asset UUID of screenshot attached to the abuse report
*/
		public static function r($number, $details=null, $location=null, $userName=null, $summary=null, $active=null, $assignedTo=null, $category=null, $checked=null, $notes=null, $objectName=null, $objectPosition=null, $objectUUID=null, $regionName=null, $reporterName=null, $screenshot=null){
			if(is_integer($number) === false){
				throw new InvalidArgumentException('AR number must be an integer.');
			}

			static $registry = array();
			$create = (isset($registry[$number]) === false);

			if($create === true && isset($details, $location, $userName, $summary, $active, $assignedTo, $category, $checked, $notes, $objectName, $objectPosition, $objectUUID, $regionName, $reporterName, $screenshot) === false){
				throw new InvalidArgumentException('Cannot return cached AbuseReport object as it hasn\'t been created.');
			}else if($create === false){
				$details        = trim($details);
				$location       = trim($location);
				$userName       = trim($userName);
				$summary        = trim($summary);
				$assignedTo     = trim($assignedTo);
				$category       = trim($category);
				$notes          = trim($notes);
				$objectName     = trim($objectName);
				$objectPosition = trim($objectPosition);
				$regionName     = trim($regionName);
				$reporterName   = trim($reporterName);

				$AR = $registry[$number];
				$create = (
					$AR->Details()        !== $details        ||
					$AR->Location()       !== $location       ||
					$AR->UserName()       !== $userName       ||
					$AR->Summary()        !== $summary        ||
					$AR->Active()         !== $active         ||
					$AR->AssignedTo()     !== $assignedTo     ||
					$AR->Category()       !== $category       ||
					$AR->Checked()        !== $checked        ||
					$AR->Notes()          !== $notes          ||
					$AR->ObjectName()     !== $objectName     ||
					$AR->ObjectPosition() !== $objectPosition ||
					$AR->ObjectUUID()     !== $objectUUID     ||
					$AR->RegionName()     !== $regionName     ||
					$AR->ReporterName()   !== $reporterName   ||
					$AR->Screenshot()     !== $screenshot
				);
			}

			if($create === true){
				$registry[$number] = new static($number, $details, $location, $userName, $summary, $active, $assignedTo, $category, $checked, $notes, $objectName, $objectPosition, $objectUUID, $regionName, $reporterName, $screenshot);
			}

			return $registry[$number];
		}

//!	integer looks like the primary key according to IAbuseReports.GetAbuseReport
//!	@see Aurora::Addon::WebUI::AbuseReport::Number()
		protected $Number;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Number
		public function Number(){
			return $this->Number;
		}

//!	string abuse report details
//!	@see Aurora::Addon::WebUI::AbuseReport::Details()
		protected $Details;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Details
		public function Details(){
			return $this->Details;
		}

//!	string abuse report location
//!	@see Aurora::Addon::WebUI::AbuseReport::Location()
		protected $Location;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Location
		public function Location(){
			return $this->Location;
		}

//!	string name of the account being reported
//!	@see Aurora::Addon::WebUI::AbuseReport::UserName()
		protected $UserName;
//!	@see Aurora::Addon::WebUI::AbuseReport::$UserName
		public function UserName(){
			return $this->UserName;
		}

//!	string summary of abuse report
//!	@see Aurora::Addon::WebUI::AbuseReport::Summary()
		protected $Summary;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Summary
		public function Summary(){
			return $this->Summary;
		}

//!	boolean whether or not the abuse report is open.
//!	@see Aurora::Addon::WebUI::AbuseReport::Active()
		protected $Active;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Active
		public function Active(){
			return $this->Active;
		}

//!	string name of admin the abuse report is assigned to.
//!	@see Aurora::Addon::WebUI::AbuseReport::AssignedTo()
		protected $AssignedTo;
//!	@see Aurora::Addon::WebUI::AbuseReport::$AssignedTo
		public function AssignedTo(){
			return $this->AssignedTo;
		}

//!	string category of abuse
//!	@see Aurora::Addon::WebUI::AbuseReport::Category()
		protected $Category;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Category
		public function Category(){
			return $this->Category;
		}

//!	boolean not entirely sure what this does.
//!	@see Aurora::Addon::WebUI::AbuseReport::Checked()
		protected $Checked;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Checked
		public function Checked(){
			return $this->Checked;
		}

//!	string notes on the abuse report.
//!	@see Aurora::Addon::WebUI::AbuseReport::Notes()
		protected $Notes;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Notes
		public function Notes(){
			return $this->Notes;
		}

//!	string Name of object being reported (if applicable)
//!	@see Aurora::Addon::WebUI::AbuseReport::ObjectName()
		protected $ObjectName;
//!	@see Aurora::Addon::WebUI::AbuseReport::$ObjectName
		public function ObjectName(){
			return $this->ObjectName;
		}

//!	string Position of object being reported (if applicable)
//!	@see Aurora::Addon::WebUI::AbuseReport::ObjectPosition()
		protected $ObjectPosition;
//!	@see Aurora::Addon::WebUI::AbuseReport::$ObjectPosition
		public function ObjectPosition(){
			return $this->ObjectPosition;
		}

//!	string UUID of object being reported (if applicable)
//!	@see Aurora::Addon::WebUI::AbuseReport::ObjectUUID()
		protected $ObjectUUID;
//!	@see Aurora::Addon::WebUI::AbuseReport::$ObjectUUID
		public function ObjectUUID(){
			return $this->ObjectUUID;
		}

//!	string region the report originated from
//!	@see Aurora::Addon::WebUI::AbuseReport::RegionName()
		protected $RegionName;
//!	@see Aurora::Addon::WebUI::AbuseReport::$RegionName
		public function RegionName(){
			return $this->RegionName;
		}

//!	string account name of the user who filed the abuse report
//!	@see Aurora::Addon::WebUI::AbuseReport::ReporterName()
		protected $ReporterName;
//!	@see Aurora::Addon::WebUI::AbuseReport::$ReporterName
		public function ReporterName(){
			return $this->ReporterName;
		}

//!	string asset UUID of screenshot attached to the abuse report
//!	@see Aurora::Addon::WebUI::AbuseReport::Screenshot()
		protected $Screenshot;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Screenshot
		public function Screenshot(){
			return $this->Screenshot;
		}
	}

//!	AbuseReports iterator. Returned by Aurora::Addon::WebUI::GetAbuseReports()
	class AbuseReports extends abstractIterator{

//!	public constructor
		public function __construct(array $ARs=null){
			if(isset($ARs) === true){
				foreach($ARs as $AR){
					if(($v instanceof AbuseReport) === false){
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::AbuseReport should be included in the array passed to Aurora::Addon::WebUI::AbuseReports::__construct()');
					}
				}
				reset($ARs);
				$this->data = $ARs;
			}
		}
	}
}
?>