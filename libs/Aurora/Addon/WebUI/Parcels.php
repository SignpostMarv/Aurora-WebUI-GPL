<?php
//!	@file libs/Aurora/Addon/WebUI/Parcels.php
//!	@brief Parcel-related WebUI code
//!	@author SignpostMarv


namespace Aurora\Addon\WebUI{

	use Aurora\Addon\WebUI;
	use Aurora\Framework;
	use OpenMetaverse\ParcelCategory;
	use OpenMetaverse\ParcelStatus;
	use OpenMetaverse\Vector3;

//!	Implementation of Aurora::Framework::LandData
	class LandData implements Framework\LandData{

//!	@see Aurora::Addon::WebUI::LandData::RegionID()
		protected $RegionID;
//!	@see Aurora::Addon::WebUI::LandData::$RegionID
		public function RegionID(){
			return $this->RegionID;
		}

//!	@see Aurora::Addon::WebUI::LandData::GlobalID()
		protected $GlobalID;
//!	@see Aurora::Addon::WebUI::LandData::$GlobalID
		public function GlobalID(){
			return $this->GlobalID;
		}

//!	@see Aurora::Addon::WebUI::LandData::LocalID()
		protected $LocalID;
//!	@see Aurora::Addon::WebUI::LandData::$LocalID
		public function LocalID(){
			return $this->LocalID;
		}

//!	@see Aurora::Addon::WebUI::LandData::SalePrice()
		protected $SalePrice;
//!	@see Aurora::Addon::WebUI::LandData::$SalePrice
		public function SalePrice(){
			return $this->SalePrice;
		}

//!	@see Aurora::Addon::WebUI::LandData::UserLocation()
		protected $UserLocation;
//!	@see Aurora::Addon::WebUI::LandData::$UserLocation
		public function UserLocation(){
			return $this->UserLocation;
		}

//!	@see Aurora::Addon::WebUI::LandData::UserLookAt()
		protected $UserLookAt;
//!	@see Aurora::Addon::WebUI::LandData::$UserLookAt
		public function UserLookAt(){
			return $this->UserLookAt;
		}

//!	@see Aurora::Addon::WebUI::LandData::Name()
		protected $Name;
//!	@see Aurora::Addon::WebUI::LandData::$Name
		public function Name(){
			return $this->Name;
		}

//!	@see Aurora::Addon::WebUI::LandData::Description()
		protected $Description;
//!	@see Aurora::Addon::WebUI::LandData::$Description
		public function Description(){
			return $this->Description;
		}

//!	@see Aurora::Addon::WebUI::LandData::Flags()
		protected $Flags;
//!	@see Aurora::Addon::WebUI::LandData::$Flags
		public function Flags(){
			return $this->Flags;
		}

//!	@see Aurora::Addon::WebUI::LandData::Dwell()
		protected $Dwell;
//!	@see Aurora::Addon::WebUI::LandData::$Dwell
		public function Dwell(){
			return $this->Dwell;
		}

//!	@see Aurora::Addon::WebUI::LandData::InfoUUID()
		protected $InfoUUID;
//!	@see Aurora::Addon::WebUI::LandData::$InfoUUID
		public function InfoUUID(){
			return $this->InfoUUID;
		}

//!	@see Aurora::Addon::WebUI::LandData::AuctionID()
		protected $AuctionID;
//!	@see Aurora::Addon::WebUI::LandData::$AuctionID
		public function AuctionID(){
			return $this->AuctionID;
		}

//!	@see Aurora::Addon::WebUI::LandData::Area()
		protected $Area;
//!	@see Aurora::Addon::WebUI::LandData::$Area
		public function Area(){
			return $this->Area;
		}

//!	@see Aurora::Addon::WebUI::LandData::Maturity()
		protected $Maturity;
//!	@see Aurora::Addon::WebUI::LandData::$Maturity
		public function Maturity(){
			return $this->Maturity;
		}

//!	@see Aurora::Addon::WebUI::LandData::OwnerID()
		protected $OwnerID;
//!	@see Aurora::Addon::WebUI::LandData::$OwnerID
		public function OwnerID(){
			return $this->OwnerID;
		}

//!	@see Aurora::Addon::WebUI::LandData::GroupID()
		protected $GroupID;
//!	@see Aurora::Addon::WebUI::LandData::$GroupID
		public function GroupID(){
			return $this->GroupID;
		}

//!	@see Aurora::Addon::WebUI::LandData::IsGroupOwned()
		protected $IsGroupOwned;
//!	@see Aurora::Addon::WebUI::LandData::$IsGroupOwned
		public function IsGroupOwned(){
			return $this->IsGroupOwned;
		}

//!	@see Aurora::Addon::WebUI::LandData::SnapshotID()
		protected $SnapshotID;
//!	@see Aurora::Addon::WebUI::LandData::$SnapshotID
		public function SnapshotID(){
			return $this->SnapshotID;
		}

//!	@see Aurora::Addon::WebUI::LandData::MediaDescription()
		protected $MediaDescription;
//!	@see Aurora::Addon::WebUI::LandData::$MediaDescription
		public function MediaDescription(){
			return $this->MediaDescription;
		}

//!	@see Aurora::Addon::WebUI::LandData::MediaWidth()
		protected $MediaWidth;
//!	@see Aurora::Addon::WebUI::LandData::$MediaWidth
		public function MediaWidth(){
			return $this->MediaWidth;
		}

//!	@see Aurora::Addon::WebUI::LandData::MediaHeight()
		protected $MediaHeight;
//!	@see Aurora::Addon::WebUI::LandData::$MediaHeight
		public function MediaHeight(){
			return $this->MediaHeight;
		}

//!	@see Aurora::Addon::WebUI::LandData::MediaLoop()
		protected $MediaLoop;
//!	@see Aurora::Addon::WebUI::LandData::$MediaLoop
		public function MediaLoop(){
			return $this->MediaLoop;
		}

//!	@see Aurora::Addon::WebUI::LandData::MediaType()
		protected $MediaType;
//!	@see Aurora::Addon::WebUI::LandData::$MediaType
		public function MediaType(){
			return $this->MediaType;
		}

//!	@see Aurora::Addon::WebUI::LandData::ObscureMedia()
		protected $ObscureMedia;
//!	@see Aurora::Addon::WebUI::LandData::$ObscureMedia
		public function ObscureMedia(){
			return $this->ObscureMedia;
		}

//!	@see Aurora::Addon::WebUI::LandData::ObscureMusic()
		protected $ObscureMusic;
//!	@see Aurora::Addon::WebUI::LandData::$ObscureMusic
		public function ObscureMusic(){
			return $this->ObscureMusic;
		}

//!	@see Aurora::Addon::WebUI::LandData::MediaLoopSet()
		protected $MediaLoopSet;
//!	@see Aurora::Addon::WebUI::LandData::$MediaLoopSet
		public function MediaLoopSet(){
			return $this->MediaLoopSet;
		}

//!	@see Aurora::Addon::WebUI::LandData::MediaAutoScale()
		protected $MediaAutoScale;
//!	@see Aurora::Addon::WebUI::LandData::$MediaAutoScale
		public function MediaAutoScale(){
			return $this->MediaAutoScale;
		}

//!	@see Aurora::Addon::WebUI::LandData::MediaURL()
		protected $MediaURL;
//!	@see Aurora::Addon::WebUI::LandData::$MediaURL
		public function MediaURL(){
			return $this->MediaURL;
		}

//!	@see Aurora::Addon::WebUI::LandData::MusicURL()
		protected $MusicURL;
//!	@see Aurora::Addon::WebUI::LandData::$MusicURL
		public function MusicURL(){
			return $this->MusicURL;
		}

//!	@see Aurora::Addon::WebUI::LandData::Bitmap()
		protected $Bitmap;
//!	@see Aurora::Addon::WebUI::LandData::$Bitmap
		public function Bitmap(){
			return $this->Bitmap;
		}

//!	@see Aurora::Addon::WebUI::LandData::Category()
		protected $Category;
//!	@see Aurora::Addon::WebUI::LandData::$Category
		public function Category(){
			return $this->Category;
		}

//!	@see Aurora::Addon::WebUI::LandData::FirstParty()
		protected $FirstParty;
//!	@see Aurora::Addon::WebUI::LandData::$FirstParty
		public function FirstParty(){
			return $this->FirstParty;
		}

//!	@see Aurora::Addon::WebUI::LandData::ClaimDate()
		protected $ClaimDate;
//!	@see Aurora::Addon::WebUI::LandData::$ClaimDate
		public function ClaimDate(){
			return $this->ClaimDate;
		}

//!	@see Aurora::Addon::WebUI::LandData::ClaimPrice()
		protected $ClaimPrice;
//!	@see Aurora::Addon::WebUI::LandData::$ClaimPrice
		public function ClaimPrice(){
			return $this->ClaimPrice;
		}

//!	@see Aurora::Addon::WebUI::LandData::LandingType()
		protected $LandingType;
//!	@see Aurora::Addon::WebUI::LandData::$LandingType
		public function LandingType(){
			return $this->LandingType;
		}

//!	@see Aurora::Addon::WebUI::LandData::PassHours()
		protected $PassHours;
//!	@see Aurora::Addon::WebUI::LandData::$PassHours
		public function PassHours(){
			return $this->PassHours;
		}

//!	@see Aurora::Addon::WebUI::LandData::PassPrice()
		protected $PassPrice;
//!	@see Aurora::Addon::WebUI::LandData::$PassPrice
		public function PassPrice(){
			return $this->PassPrice;
		}

//!	@see Aurora::Addon::WebUI::LandData::AuthBuyerID()
		protected $AuthBuyerID;
//!	@see Aurora::Addon::WebUI::LandData::$AuthBuyerID
		public function AuthBuyerID(){
			return $this->AuthBuyerID;
		}

//!	@see Aurora::Addon::WebUI::LandData::OtherCleanTime()
		protected $OtherCleanTime;
//!	@see Aurora::Addon::WebUI::LandData::$OtherCleanTime
		public function OtherCleanTime(){
			return $this->OtherCleanTime;
		}

//!	@see Aurora::Addon::WebUI::LandData::RegionHandle()
		protected $RegionHandle;
//!	@see Aurora::Addon::WebUI::LandData::$RegionHandle
		public function RegionHandle(){
			return $this->RegionHandle;
		}

//!	@see Aurora::Addon::WebUI::LandData::isPrivate()
		protected $isPrivate;
//!	@see Aurora::Addon::WebUI::LandData::$isPrivate
		public function isPrivate(){
			return $this->isPrivate;
		}

//!	@see Aurora::Addon::WebUI::LandData::GenericData()
		protected $GenericData;
//!	@see Aurora::Addon::WebUI::LandData::$GenericData
		public function GenericData(){
			return $this->GenericData;
		}

//!	Constructor is protected because we hide it behind a registry method
/**
*	@param string $RegionID Region UUID.
*	@param string $GlobalID Global UUID
*	@param integer $LocalID Local ID
*	@param integer $SalePrice Sale price
*	@param object $UserLocation instance of OpenMetaverse::Vector3 User teleport location
*	@param object $UserLookAt instance of OpenMetaverse::Vector3 indicating where the user should look at on arrival.
*	@param string $Name Parcel name
*	@param string $Description Parcel description
*	@param integer $Flags Parcel Flags bitfield
*	@param integer $Dwell Parcel Dwell
*	@param string $InfoUUID Info UUID
*	@param integer $AuctionID Auction ID
*	@param integer $Area Area of parcel in square meters
*	@param integer $Maturity Maturity
*	@param string $OwnerID Owner UUID
*	@param string $GroupID Group UUID
*	@param boolean $IsGroupOwned TRUE if Aurora::Framework::GroupID() is not 00000000-0000-0000-0000-000000000000, FALSE otherwise
*	@param string $SnapshotID Snapshot asset texture UUID
*	@param string $MediaDescription Media Description
*	@param integer $MediaWidth Media Width
*	@param integer $MediaHeight Media Height
*	@param boolean $MediaLoop Media Loop flag
*	@param string $MediaType Media type
*	@param boolean $ObscureMedia flag to obscure media url
*	@param boolean $ObscureMusic flag to obscure music url
*	@param float $MediaLoopSet Media Loop time
*	@param integer $MediaAutoScale Media auto-sclae flag (why is this not a boolean ?)
*	@param string $MediaURL Media URL
*	@param string $MusicURL Music URL
*	@param string $Bitmap Bitmap WebUI will get this as a space-separated list of hexadecimal digits, rather than the raw bitmap
*	@param integer $Category Parcel Category
*	@param boolean $FirstParty TRUE if parcel is ran by grid operator, FALSE otherwise
*	@param integer $ClaimDate Unix timestamp indicating when parcel was claimed
*	@param integer $ClaimPrice Claim price
*	@param integer $LandingType Landing Type
*	@param float $PassHours How long an access pass lasts for in hours
*	@param integer $PassPrice How much the access pass costs
*	@param string $AuthBuyerID user UUID of authorised buyer.
*	@param integer $OtherCleanTime Other Clean Time
*	@param string $RegionHandle Region Handle - in the c#, this is a 64bit unsigned integer. since we can't guarantee availability of 64bit integers (never mined the lack of unsigned integers in PHP), WebUI will get this as a string.
*	@param boolean $isPrivate TRUE if parcel is Private, FALSE otherwise. we name the method isPrivate() instead of Private() because Private is a reserved word.
*	@param array $GenericData Generic Data
*/
		protected function __construct(
			$RegionID,
			$GlobalID,
			$LocalID,
			$SalePrice,
			Vector3 $UserLocation,
			Vector3 $UserLookAt,
			$Name,
			$Description,
			$Flags,
			$Dwell,
			$InfoUUID,
			$AuctionID,
			$Area,
			$Maturity,
			$OwnerID,
			$GroupID,
			$IsGroupOwned,
			$SnapshotID,
			$MediaDescription,
			$MediaWidth,
			$MediaHeight,
			$MediaLoop,
			$MediaType,
			$ObscureMedia,
			$ObscureMusic,
			$MediaLoopSet,
			$MediaAutoScale,
			$MediaURL,
			$MusicURL,
			$Bitmap,
			$Category,
			$FirstParty,
			$ClaimDate,
			$ClaimPrice,
			$LandingType,
			$PassHours,
			$PassPrice,
			$AuthBuyerID,
			$OtherCleanTime,
			$RegionHandle,
			$isPrivate,
			\stdClass $GenericData){

			if(is_string($LocalID) === true && ctype_digit($LocalID) === true){
				$LocalID = (integer)$LocalID;
			}
			if(is_string($SalePrice) === true && ctype_digit($SalePrice) === true){
				$SalePrice = (integer)$SalePrice;
			}
			if(is_string($Name) === true){
				$Name = trim($Name);
			}
			if(is_string($Description) === true){
				$Description = trim($Description);
			}
			if(is_string($Flags) === true && ctype_digit($Flags) === true){
				$Flags = (integer)$Flags;
			}
			if(is_string($Area) === true && ctype_digit($Area) === true){
				$Area = (integer)$Area;
			}
			if(is_string($Maturity) === true && ctype_digit($Maturity) === true){
				$Maturity = (integer)$Maturity;
			}
			if(is_string($IsGroupOwned) === true && ctype_digit($IsGroupOwned) === true){
				$IsGroupOwned = (boolean)$IsGroupOwned;
			}
			if(is_string($MediaDescription) === true){
				$MediaDescription = trim($MediaDescription);
			}
			if(is_string($MediaWidth) === true && ctype_digit($MediaWidth) === true){
				$MediaWidth = (integer)$MediaWidth;
			}
			if(is_string($MediaHeight) === true && ctype_digit($MediaHeight) === true){
				$MediaHeight = (integer)$MediaHeight;
			}
			if(is_string($MediaLoop) === true && ctype_digit($MediaLoop) === true){
				$MediaLoop = (boolean)$MediaLoop;
			}
			if(is_string($MediaType) === true){
				$MediaType = trim($MediaType);
			}
			if(is_string($ObscureMedia) === true && ctype_digit($ObscureMedia) === true){
				$ObscureMedia = (boolean)$ObscureMedia;
			}
			if(is_string($ObscureMusic) === true && ctype_digit($ObscureMusic) === true){
				$ObscureMusic = (boolean)$ObscureMusic;
			}
			if(is_string($MediaLoopSet) === true && (ctype_digit($MediaLoopSet) === true || preg_match('/^\d*\.\d+$/', $MediaLoopSet) == 1)){
				$MediaLoopSet = (float)$MediaLoopSet;
			}
			if(is_string($MediaAutoScale) === true && ctype_digit($MediaAutoScale) === true){
				$MediaAutoScale = (integer)$MediaAutoScale;
			}
			if(is_string($MediaURL) === true){
				$MediaURL = trim($MediaURL);
			}
			if(is_string($MusicURL) === true){
				$MusicURL = trim($MusicURL);
			}
			if(is_string($Bitmap) === true){
				$Bitmap = trim($Bitmap);
			}
			if(is_string($Category) === true && ctype_digit($Category) === true){
				$Category = (integer)$Category;
			}
			if(is_string($ClaimDate) === true && ctype_digit($ClaimDate) === true){
				$ClaimDate = (integer)$ClaimDate;
			}
			if(is_string($ClaimPrice) === true && ctype_digit($ClaimPrice) === true){
				$ClaimPrice = (integer)$ClaimPrice;
			}
			if(is_string($LandingType) === true && ctype_digit($LandingType) === true){
				$LandingType = (integer)$LandingType;
			}
			if(is_string($PassHours) === true && (ctype_digit($PassHours) === true || preg_match('/^\d*\.\d+$/', $PassHours) == 1)){
				$PassHours = (float)$PassHours;
			}
			if(is_string($PassPrice) === true && ctype_digit($PassPrice) === true){
				$PassPrice = (integer)$PassPrice;
			}
			if(is_string($OtherCleanTime) === true && ctype_digit($OtherCleanTime) === true){
				$OtherCleanTime = (integer)$OtherCleanTime;
			}
			if(is_string($isPrivate) === true && ctype_digit($isPrivate) === true){
				$isPrivate = (boolean)$isPrivate;
			}

			if(is_string($RegionID) === false){
				throw new InvalidArgumentException('RegionID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $RegionID) != 1){
				throw new InvalidArgumentException('RegionID must be a valid UUID.');
			}else if(is_string($GlobalID) === false){
				throw new InvalidArgumentException('GlobalID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $GlobalID) != 1){
				throw new InvalidArgumentException('GlobalID must be a valid UUID.');
			}else if(is_integer($LocalID) === false){
				throw new InvalidArgumentException('LocalID must be specified as integer.');
			}else if(is_integer($SalePrice) === false){
				throw new InvalidArgumentException('SalePrice must be specified as integer.');
			}else if(is_string($Name) === false){
				throw new InvalidArgumentException('Parcel Name must be specified as string.');
			}else if($Name === ''){
				throw new InvalidArgumentException('Parcel Name must be non-empty string.');
			}else if(is_string($Description) === false){
				throw new InvalidArgumentException('Parcel Description must be specified as string.');
			}else if(is_integer($Flags) === false){
				throw new InvalidArgumentException('Parcel Flags must be specified as integer.');
			}else if(is_integer($Dwell) === false){
				throw new InvalidArgumentException('Dwell must be specified as integer.');
			}else if(is_string($InfoUUID) === false){
				throw new InvalidArgumentException('Info UUID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $InfoUUID) != 1){
				throw new InvalidArgumentException('Info UUID must be valid UUID.');
			}else if(is_integer($AuctionID) === false){
				throw new InvalidArgumentException('AuctionID must be specified as integer.');
			}else if(is_integer($Area) === false){
				throw new InvalidArgumentException('Area must be specified as integer.');
			}else if($Area < 0){
				throw new InvalidArgumentException('Area must be greater than zero.');
			}else if(is_integer($Maturity) === false){
				throw new InvalidArgumentException('Maturity must be specified as integer.');
			}else if(is_string($OwnerID) === false){
				throw new InvalidArgumentException('OwnerID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $OwnerID) != 1){
				throw new InvalidArgumentException('OwnerID must be valid UUID.');
			}else if(is_string($GroupID) === false){
				throw new InvalidArgumentException('GroupID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $GroupID) != 1){
				throw new InvalidArgumentException('GroupID must be a valid UUID.');
			}else if(is_bool($IsGroupOwned) === false){
				throw new InvalidArgumentException('IsGroupOwned must be specified as boolean.');
			}else if(is_string($SnapshotID) === false){
				throw new InvalidArgumentException('SnapshotID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $SnapshotID) != 1){
				throw new InvalidArgumentException('SnapshotID must be a valid UUID.');
			}else if(is_string($MediaDescription) === false){
				throw new InvalidArgumentException('MediaDescription must be specified as string.');
			}else if(is_integer($MediaWidth) === false){
				throw new InvalidArgumentException('MediaWidth must be specified as integer.');
			}else if($MediaWidth < 0){
				throw new InvalidArgumentException('MediaWidth must be greater than zero.');
			}else if(is_integer($MediaHeight) === false){
				throw new InvalidArgumentException('MediaHeight must be specified as integer.');
			}else if($MediaHeight < 0){
				throw new InvalidArgumentException('MediaHeight must be greater than zero.');
			}else if(is_bool($MediaLoop) === false){
				throw new InvalidArgumentException('MediaLoop must be specified as boolean.');
			}else if(is_string($MediaType) === false){
				throw new InvalidArgumentException('MediaType must be specified as string.');
			}else if($MediaType === ''){
				throw new InvalidArgumentException('MediaType must be non-empty string.');
			}else if(is_bool($ObscureMedia) === false){
				throw new InvalidArgumentException('ObscureMedia must be specified as boolean.');
			}else if(is_bool($ObscureMusic) === false){
				throw new InvalidArgumentException('ObscureMusic must be specified as boolean.');
			}else if(is_float($MediaLoopSet) === false){
				throw new InvalidArgumentException('MediaLoopSet must be specified as float.');
			}else if(is_integer($MediaAutoScale) === false){
				throw new InvalidArgumentException('MediaAutoScale must be specified as integer.');
			}else if(is_string($MediaURL) === false){
				throw new InvalidArgumentException('MediaURL must be specified as string.');
			}else if($MediaURL !== '' && parse_url($MediaURL) === false){
				throw new InvalidArgumentException('MediaURL was malformed.');
			}else if(is_string($MusicURL) === false){
				throw new InvalidArgumentException('MusicURL must be specified as string.');
			}else if($MusicURL !== '' && parse_url($MusicURL) === false){
				throw new InvalidArgumentException('MusicURL was malformed.');
			}else if(is_string($Bitmap) === false){
				throw new InvalidArgumentException('Bitmap must be specified as string.');
			}else if(ctype_xdigit(str_replace(array(' ',"\n"),'', $Bitmap)) === false){
				throw new InvalidArgumentException('Bitmap must be space-separated list of hexadecimal digits.');
			}else if(is_integer($Category) === false){
				throw new InvalidArgumentException('Category must be specified as integer.');
			}else if(is_integer($ClaimDate) === false){
				throw new InvalidArgumentException('ClaimDate must be specified as integer.');
			}else if(is_integer($ClaimPrice) === false){
				throw new InvalidArgumentException('ClaimPrice must be specified as integer.');
			}else if(is_integer($LandingType) === false){
				throw new InvalidArgumentException('LandingType must be specified as integer.');
			}else if(is_float($PassHours) === false){
				throw new InvalidArgumentException('PassHours must be specified as float.');
			}else if(is_integer($PassPrice) === false){
				throw new InvalidArgumentException('PassPrice must be specified as integer.');
			}else if(is_string($AuthBuyerID) === false){
				throw new InvalidArgumentException('AuthBuyerID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $AuthBuyerID) != 1){
				throw new InvalidArgumentException('AuthBuyerID must be valid UUID.');
			}else if(is_integer($OtherCleanTime) === false){
				throw new InvalidArgumentException('OtherCleanTime must be specified as integer.');
			}else if(is_string($RegionHandle) === false){
				throw new InvalidArgumentException('RegionHandle must be specified as string.');
			}else if(ctype_digit($RegionHandle) === false){
				throw new InvalidArgumentException('RegionHandle must be integer-as-string.');
			}else if(is_bool($isPrivate) === false){
				throw new InvalidArgumentException('isPrivate must be specified as boolean.');
			}

			$this->RegionID             = $RegionID;
			$this->GlobalID             = $GlobalID;
			$this->LocalID              = $LocalID;
			$this->SalePrice            = $SalePrice;
			$this->UserLocation         = $UserLocation;
			$this->UserLookAt           = $UserLookAt;
			$this->Name                 = $Name;
			$this->Description          = $Description;
			$this->Flags                = $Flags;
			$this->Dwell                = $Dwell;
			$this->InfoUUID             = $InfoUUID;
			$this->AuctionID            = $AuctionID;
			$this->Area                 = $Area;
			$this->Maturity             = $Maturity;
			$this->OwnerID              = $OwnerID;
			$this->GroupID              = $GroupID;
			$this->IsGroupOwned         = $IsGroupOwned;
			$this->SnapshotID           = $SnapshotID;
			$this->MediaDescription     = $MediaDescription;
			$this->MediaWidth           = $MediaWidth;
			$this->MediaHeight          = $MediaHeight;
			$this->MediaLoop            = $MediaLoop;
			$this->MediaType            = $MediaType;
			$this->ObscureMedia         = $ObscureMedia;
			$this->ObscureMusic         = $ObscureMusic;
			$this->MediaLoopSet         = $MediaLoopSet;
			$this->MediaAutoScale       = $MediaAutoScale;
			$this->MediaURL             = $MediaURL;
			$this->MusicURL             = $MusicURL;
			$this->Bitmap               = $Bitmap;
			$this->Category             = $Category;
			$this->FirstParty           = $FirstParty;
			$this->ClaimDate            = $ClaimDate;
			$this->ClaimPrice           = $ClaimPrice;
			$this->LandingType          = $LandingType;
			$this->PassHours            = $PassHours;
			$this->PassPrice            = $PassPrice;
			$this->AuthBuyerID          = $AuthBuyerID;
			$this->OtherCleanTime       = $OtherCleanTime;
			$this->RegionHandle         = $RegionHandle;
			$this->isPrivate            = $isPrivate;
			$this->GenericData          = $GenericData;
		}

		public static function r(
			$InfoUUID,
			$RegionID=null,
			$GlobalID=null,
			$LocalID=null,
			$SalePrice=null,
			Vector3 $UserLocation=null,
			Vector3 $UserLookAt=null,
			$Name=null,
			$Description=null,
			$Flags=null,
			$Dwell=null,
			$AuctionID=null,
			$Area=null,
			$Maturity=null,
			$OwnerID=null,
			$GroupID=null,
			$IsGroupOwned=null,
			$SnapshotID=null,
			$MediaDescription=null,
			$MediaWidth=null,
			$MediaHeight=null,
			$MediaLoop=null,
			$MediaType=null,
			$ObscureMedia=null,
			$ObscureMusic=null,
			$MediaLoopSet=null,
			$MediaAutoScale=null,
			$MediaURL=null,
			$MusicURL=null,
			$Bitmap=null,
			$Category=null,
			$FirstParty=null,
			$ClaimDate=null,
			$ClaimPrice=null,
			$LandingType=null,
			$PassHours=null,
			$PassPrice=null,
			$AuthBuyerID=null,
			$OtherCleanTime=null,
			$RegionHandle=null,
			$isPrivate=null,
			$GenericData=null){

			static $registry = array();

			if(is_string($InfoUUID) === false){
				throw new InvalidArgumentException('InfoUUID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $InfoUUID) === false){
				throw new InvalidArgumentException('InfoUUID must be valid UUID.');
			}

			$InfoUUID = strtolower($InfoUUID);
			$create = isset($registry[$InfoUUID]) === false;

			if($create === false){
				$parcel = $registry[$InfoUUID];
				$create = (
					$parcel->RegionID()                  != $RegionID ||
					$parcel->GlobalID()                  != $GlobalID ||
					$parcel->LocalID()                   != $LocalID ||
					$parcel->SalePrice()                 != $SalePrice ||
					(string)$parcel->UserLocation()      != (string)$UserLocation ||
					(string)$parcel->UserLookAt()        != (string)$UserLookAt ||
					$parcel->Name()                      != $Name ||
					$parcel->Description()               != $Description ||
					$parcel->Flags()                     != $Flags ||
					$parcel->Dwell()                     != $Dwell ||
					$parcel->AuctionID()                 != $AuctionID ||
					$parcel->Area()                      != $Area ||
					$parcel->Maturity()                  != $Maturity ||
					$parcel->OwnerID()                   != $OwnerID ||
					$parcel->GroupID()                   != $GroupID ||
					$parcel->IsGroupOwned()              != $IsGroupOwned ||
					$parcel->SnapshotID()                != $SnapshotID ||
					$parcel->MediaDescription()          != $MediaDescription ||
					$parcel->MediaWidth()                != $MediaWidth ||
					$parcel->MediaHeight()               != $MediaHeight ||
					$parcel->MediaLoop()                 != $MediaLoop ||
					$parcel->MediaType()                 != $MediaType ||
					$parcel->ObscureMedia()              != $ObscureMedia ||
					$parcel->ObscureMusic()              != $ObscureMusic ||
					$parcel->MediaLoopSet()              != $MediaLoopSet ||
					$parcel->MediaAutoScale()            != $MediaAutoScale ||
					$parcel->MediaURL()                  != $MediaURL ||
					$parcel->MusicURL()                  != $MusicURL ||
					$parcel->Bitmap()                    != $Bitmap ||
					$parcel->Category()                  != $Category ||
					$parcel->FirstParty()                != $FirstParty ||
					$parcel->ClaimDate()                 != $ClaimDate ||
					$parcel->ClaimPrice()                != $ClaimPrice ||
					$parcel->LandingType()               != $LandingType ||
					$parcel->PassHours()                 != $PassHours ||
					$parcel->PassPrice()                 != $PassPrice ||
					$parcel->AuthBuyerID()               != $AuthBuyerID ||
					$parcel->OtherCleanTime()            != $OtherCleanTime ||
					$parcel->RegionHandle()              != $RegionHandle ||
					$parcel->isPrivate()                 != $isPrivate ||
					print_r($parcel->GenericData(),true) != print_r($GenericData,true)
				);
			}

			if($create === true && isset(
				$RegionID,
				$GlobalID,
				$LocalID,
				$SalePrice,
				$UserLocation,
				$UserLookAt,
				$Name,
				$Description,
				$Flags,
				$Dwell,
				$InfoUUID,
				$AuctionID,
				$Area,
				$Maturity,
				$OwnerID,
				$GroupID,
				$IsGroupOwned,
				$SnapshotID,
				$MediaDescription,
				$MediaWidth,
				$MediaHeight,
				$MediaLoop,
				$MediaType,
				$ObscureMedia,
				$ObscureMusic,
				$MediaLoopSet,
				$MediaAutoScale,
				$MediaURL,
				$MusicURL,
				$Bitmap,
				$Category,
				$FirstParty,
				$ClaimDate,
				$ClaimPrice,
				$LandingType,
				$PassHours,
				$PassPrice,
				$AuthBuyerID,
				$OtherCleanTime,
				$RegionHandle,
				$isPrivate,
				$GenericData) === false){
				throw new InvalidArgumentException('Cannot create object unless all arguments are specified.');
			}

			if($create === true){
				$registry[$InfoUUID] = new static(
					$RegionID,
					$GlobalID,
					$LocalID,
					$SalePrice,
					$UserLocation,
					$UserLookAt,
					$Name,
					$Description,
					$Flags,
					$Dwell,
					$InfoUUID,
					$AuctionID,
					$Area,
					$Maturity,
					$OwnerID,
					$GroupID,
					$IsGroupOwned,
					$SnapshotID,
					$MediaDescription,
					$MediaWidth,
					$MediaHeight,
					$MediaLoop,
					$MediaType,
					$ObscureMedia,
					$ObscureMusic,
					$MediaLoopSet,
					$MediaAutoScale,
					$MediaURL,
					$MusicURL,
					$Bitmap,
					$Category,
					$FirstParty,
					$ClaimDate,
					$ClaimPrice,
					$LandingType,
					$PassHours,
					$PassPrice,
					$AuthBuyerID,
					$OtherCleanTime,
					$RegionHandle,
					$isPrivate,
					$GenericData
				);
			}

			return $registry[$InfoUUID];
		}
	}

//!	Abstract iterator for instances of Aurora::Addon::WebUI::LandData
	abstract class abstractSeekableLandDataIterator extends abstractSeekableIterator{

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
/**
*	@param object $WebUI instanceof Aurora::Addon::WebUI
*	@param integer $start start point
*	@param integer $total total number of LandData results according to child-class filters
*	@param array $parcels array of Aurora::Addon::WebUI::LandData objects
*/
		protected function __construct(WebUI $WebUI, $start=0, $total=0, array $parcels=null){
			parent::__construct($WebUI, $start, $total);
			if(isset($parcels) === true){
				$i = $start;
				foreach($parcels as $parcel){
					if($parcel instanceof LandData){
						$this->data[$i++] = $parcel;
					}else{
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::LandData should be passed to Aurora::Addon::WebUI::abstractSeekableLandDataIterator::__construct()');
					}
				}
			}
		}
	}

	abstract class abstractSeekableLandDataIteratorByRegion extends abstractSeekableLandDataIterator{

//!	object instance of Aurora::Addon::WebUI::GridRegion
		protected $region;

//!	string region scopeID
		protected $scopeID;

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
/**
*	@param object $WebUI instanceof Aurora::Addon::WebUI
*	@param integer $start start point
*	@param integer $total total number of LandData results according to child-class filters
*	@param object $region instance of Aurora::Addon::WebUI::GridRegion
*	@param string $scopeID Region ScopeID
*	@param array $parcels array of Aurora::Addon::WebUI::LandData objects
*/
		protected function __construct(WebUI $WebUI, $start=0, $total=0, GridRegion $region, $scopeID='00000000-0000-0000-0000-000000000000', array $parcels=null){
			if(is_string($scopeID) === false){
				throw new InvalidArgumentException('ScopeID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $scopeID) != 1){
				throw new InvalidArgumentException('ScopeID must be valid UUID.');
			}

			parent::__construct($WebUI, $start, $total, $parcels);
			$this->region  = $region;
			$this->scopeID = $scopeID;
		}
	}

//!	Iterator for geting instances of Aurora::Addon::WebUI::LandData by parcel owner and region
	class GetParcelsByRegion extends abstractSeekableLandDataIteratorByRegion{

//!	string parcel owner UUID
		protected $owner;

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
/**
*	@param object $WebUI instanceof Aurora::Addon::WebUI
*	@param integer $start start point
*	@param integer $total total number of LandData results according to child-class filters
*	@param object $region instance of Aurora::Addon::WebUI::GridRegion
*	@param string $owner Parcel Owner UUID
*	@param string $scopeID Region ScopeID
*	@param array $parcels array of Aurora::Addon::WebUI::LandData objects
*/
		protected function __construct(WebUI $WebUI, $start=0, $total=0, GridRegion $region, $owner='00000000-0000-0000-0000-000000000000', $scopeID='00000000-0000-0000-0000-000000000000', array $parcels=null){
			if(is_string($owner) === false){
				throw new InvalidArgumentException('OwnerID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $owner) != 1){
				throw new InvalidArgumentException('OwnerID must be valid UUID.');
			}else if(is_string($scopeID) === false){
				throw new InvalidArgumentException('ScopeID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $scopeID) != 1){
				throw new InvalidArgumentException('ScopeID must be valid UUID.');
			}

			parent::__construct($WebUI, $start, $total, $region, $scopeID, $parcels);
			$this->owner = $owner;
		}

//! This is a registry method for a class that implements the SeekableIterator class, so we can save ourselves some API calls if we've already fetched some parcels.
/**
*	@param object $WebUI instanceof Aurora::Addon::WebUI
*	@param integer $start start point
*	@param integer $total total number of LandData results according to child-class filters
*	@param object $region instance of Aurora::Addon::WebUI::GridRegion
*	@param string $owner Parcel Owner UUID
*	@param string $scopeID Region ScopeID
*	@param array $parcels array of Aurora::Addon::WebUI::LandData objects
*	@return object instance of Aurora::Addon::WebUI::GetParcelsByRegion
*/
		public static function r(WebUI $WebUI, $start=0, $total=0, GridRegion $region, $owner='00000000-0000-0000-0000-000000000000', $scopeID='00000000-0000-0000-0000-000000000000', array $parcels=null){
			if(is_string($owner) === false){
				throw new InvalidArgumentException('OwnerID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $owner) != 1){
				throw new InvalidArgumentException('OwnerID must be valid UUID.');
			}else if(is_string($scopeID) === false){
				throw new InvalidArgumentException('ScopeID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $scopeID) != 1){
				throw new InvalidArgumentException('ScopeID must be valid UUID.');
			}

			static $registry = array();
			$hash1 = spl_object_hash($WebUI);
			$hash2 = $region->RegionID();
			$owner = strtolower($owner);
			$scopeID = strtolower($scopeID);

			if(isset($registry[$hash1]) === false){
				$registry[$hash1] = array();
			}
			if(isset($registry[$hash1][$hash2]) === false){
				$registry[$hash1][$hash2] = array();
			}
			if(isset($registry[$hash1][$hash2][$scopeID]) === false){
				$registry[$hash1][$hash2][$scopeID] = array();
			}

			$create = (isset($registry[$hash1][$hash2][$scopeID][$owner]) === false || $registry[$hash1][$hash2][$scopeID][$owner]->count() !== $total);

			if($create === true){
				$registry[$hash1][$hash2][$scopeID][$owner] = new static($WebUI, $start, $total, $region, $owner, $scopeID, $parcels);
			}

			$registry[$hash1][$hash2][$scopeID][$owner]->seek($start);

			return $registry[$hash1][$hash2][$scopeID][$owner];
		}

//!	To avoid slowdowns due to an excessive amount of curl calls, we populate Aurora::Addon::WebUI::GetParcelsByRegion::$data in batches of 10
/**
*	@return mixed either NULL or an instance of Aurora::Addon::WebUI::LandData
*/
		public function current(){
			if($this->valid() === false){
				return null;
			}else if(isset($this->data[$this->key()]) === false){
				$start   = $this->key();
				$results = $this->WebUI->GetParcelsByRegion($start, 10, $this->region, $this->owner, $this->scopeID);
				foreach($results as $group){
					$this->data[$start++] = $group;
				}
			}
			return $this->data[$this->key()];
		}
	}

//!	Iterator for geting instances of Aurora::Addon::WebUI::LandData by parcel name and region
	class GetParcelsWithNameByRegion extends abstractSeekableLandDataIteratorByRegion{

//!	string parcel name
		protected $name;

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
/**
*	@param object $WebUI instanceof Aurora::Addon::WebUI
*	@param integer $start start point
*	@param integer $total total number of LandData results according to child-class filters
*	@param object $region instance of Aurora::Addon::WebUI::GridRegion
*	@param string $owner Parcel Owner UUID
*	@param string $scopeID Region ScopeID
*	@param array $parcels array of Aurora::Addon::WebUI::LandData objects
*/
		protected function __construct(WebUI $WebUI, $start=0, $total=0, $name='', GridRegion $region, $scopeID='00000000-0000-0000-0000-000000000000', array $parcels=null){
			if(is_string($name) === true){
				$name = trim($name);
			}

			if(is_string($name) === false){
				throw new InvalidArgumentException('Parcel name must be specified as string.');
			}else if($name === ''){
				throw new InvalidArgumentException('Parcel name cannot be empty string.');
			}else if(is_string($scopeID) === false){
				throw new InvalidArgumentException('ScopeID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $scopeID) != 1){
				throw new InvalidArgumentException('ScopeID must be valid UUID.');
			}

			parent::__construct($WebUI, $start, $total, $region, $scopeID, $parcels);
			$this->name = $name;
		}

//! This is a registry method for a class that implements the SeekableIterator class, so we can save ourselves some API calls if we've already fetched some parcels.
/**
*	@param object $WebUI instanceof Aurora::Addon::WebUI
*	@param integer $start start point
*	@param integer $total total number of LandData results according to child-class filters
*	@param string $name Parcel name
*	@param object $region instance of Aurora::Addon::WebUI::GridRegion
*	@param string $scopeID Region ScopeID
*	@param array $parcels array of Aurora::Addon::WebUI::LandData objects
*	@return object instance of Aurora::Addon::WebUI::GetParcelsByRegion
*/
		public static function r(WebUI $WebUI, $start=0, $total=0, $name='', GridRegion $region, $scopeID='00000000-0000-0000-0000-000000000000', array $parcels=null){
			if(is_string($name) === true){
				$name = trim($name);
			}

			if(is_string($name) === false){
				throw new InvalidArgumentException('Parcel name must be specified as string.');
			}else if($name === ''){
				throw new InvalidArgumentException('Parcel name cannot be empty string.');
			}else if(is_string($scopeID) === false){
				throw new InvalidArgumentException('ScopeID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $scopeID) != 1){
				throw new InvalidArgumentException('ScopeID must be valid UUID.');
			}

			static $registry = array();
			$hash1 = spl_object_hash($WebUI);
			$hash2 = $region->RegionID();
			$scopeID = strtolower($scopeID);

			if(isset($registry[$hash1]) === false){
				$registry[$hash1] = array();
			}
			if(isset($registry[$hash1][$hash2]) === false){
				$registry[$hash1][$hash2] = array();
			}
			if(isset($registry[$hash1][$hash2][$scopeID]) === false){
				$registry[$hash1][$hash2][$scopeID] = array();
			}

			$create = (isset($registry[$hash1][$hash2][$scopeID][$name]) === false || $registry[$hash1][$hash2][$scopeID][$name]->count() !== $total);

			if($create === true){
				$registry[$hash1][$hash2][$scopeID][$name] = new static($WebUI, $start, $total, $name, $region, $scopeID, $parcels);
			}

			$registry[$hash1][$hash2][$scopeID][$name]->seek($start);

			return $registry[$hash1][$hash2][$scopeID][$name];
		}

//!	To avoid slowdowns due to an excessive amount of curl calls, we populate Aurora::Addon::WebUI::GetParcelsWithNameByRegion::$data in batches of 10
/**
*	@return mixed either NULL or an instance of Aurora::Addon::WebUI::LandData
*/
		public function current(){
			if($this->valid() === false){
				return null;
			}else if(isset($this->data[$this->key()]) === false){
				$start   = $this->key();
				$results = $this->WebUI->GetParcelsWithNameByRegion($start, 10, $this->name, $this->region, $this->scopeID, true);
				foreach($results as $group){
					$this->data[$start++] = $group;
				}
			}
			return $this->data[$this->key()];
		}
	}
}
?>
