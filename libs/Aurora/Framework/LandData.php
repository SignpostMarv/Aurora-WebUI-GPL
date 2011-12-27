<?php
/**
*	This file is based on c# code from the Aurora-Sim project.
*	As such, the original header text is included.
*	Although the location of the c# file in the Aurora-Sim project is within the OpenSim.Framework project, this file is placed under the equivalent of Aurora.Framework for simplicity.
*/

/*
 * Copyright (c) Contributors, http://aurora-sim.org/, http://opensimulator.org/
 * See Aurora-CONTRIBUTORS.TXT for a full list of copyright holders.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Aurora-Sim Project nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE DEVELOPERS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Aurora\Framework{

//!	We're deviating from the c# here, we're using methods instead of public properties.
	interface LandData{

//!	@return string Region UUID.
		public function RegionID();

//!	@return string Global UUID
		public function GlobalID();

//!	@return integer Local ID
		public function LocalID();

//!	@return integer Sale price
		public function SalePrice();

//!	@return object instance of OpenMetaverse::Vector3 User teleport location
		public function UserLocation();

//!	@return object instance of OpenMetaverse::Vector3 indicating where the user should look at on arrival.
		public function UserLookAt();

//!	@return string Parcel name
		public function Name();

//!	@return string Parcel description
		public function Description();

//!	@return integer Parcel Flags bitfield
		public function Flags();

//!	@return integer Parcel Dwell
		public function Dwell();

//!	@return string Info UUID
		public function InfoUUID();

//!	@return integer Auction ID
		public function AuctionID();

//!	@return integer Area of parcel in square meters
		public function Area();

//!	@return integer Maturity
		public function Maturity();

//!	@return string Owner UUID
		public function OwnerID();

//!	@return string Group UUID
		public function GroupID();

//!	@return boolean TRUE if Aurora::Framework::GroupID() is not 00000000-0000-0000-0000-000000000000, FALSE otherwise
		public function IsGroupOwned();

//!	@return string Snapshot asset texture UUID
		public function SnapshotID();

//!	@return string Media Description
		public function MediaDescription();

//!	@return integer Media Width
		public function MediaWidth();

//!	@return integer Media Height
		public function MediaHeight();

//!	@return boolean Media Loop flag
		public function MediaLoop();

//!	@return string Media type
		public function MediaType();

//!	@return boolean flag to obscure media url
		public function ObscureMedia();

//!	@return boolean flag to obscure music url
		public function ObscureMusic();

//!	@return float Media Loop time
		public function MediaLoopSet();

//!	@return integer Media auto-sclae flag (why is this not a boolean ?)
		public function MediaAutoScale();

//!	@return string Media URL
		public function MediaURL();

//!	@return string Music URL
		public function MusicURL();

//!	@return string Bitmap WebUI will get this as a space-separated list of hexadecimal digits, rather than the raw bitmap
		public function Bitmap();

//!	@return integer Parcel Category
		public function Category();

//!	@return integer Unix timestamp indicating when parcel was claimed
		public function ClaimDate();

//!	@return integer Claim price
		public function ClaimPrice();

//!	@return integer Landing Type
		public function LandingType();

//!	@return float How long an access pass lasts for in hours
		public function PassHours();

//!	@return integer How much the access pass costs
		public function PassPrice();

//!	@return string user UUID of authorised buyer.
		public function AuthBuyerID();

//!	@return integer Other Clean Time
		public function OtherCleanTime();

//!	@return string Region Handle - in the c#, this is a 64bit unsigned integer. since we can't guarantee availability of 64bit integers (never mined the lack of unsigned integers in PHP), WebUI will get this as a string.
		public function RegionHandle();

//!	@return boolean TRUE if parcel is Private, FALSE otherwise. we name the method isPrivate() instead of Private() because Private is a reserved word.
		public function isPrivate();

//!	@return array Generic Data
		public function GenericData();
	}
}
