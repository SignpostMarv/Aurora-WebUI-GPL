<?php
/**
*	This file is based on c# code from the Aurora-Sim project.
*	As such, the original header text is included.
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
	interface EstateSettings{

//!	@return integer Estate ID
		public function EstateID();

//!	@return string Name of estate
		public function EstateName();

//!	@return boolean TRUE if abuse reports should be emailed to the estate owner, FALSE otherwise
		public function AbuseEmailToEstateOwner();

//!	@return boolean TRUE if anonymous users should be denid access, FALSE otherwise
		public function DenyAnonymous();

//!	@return boolean TRUE if teleporting out of the estate resets the home location, FALSE otherwise
		public function ResetHomeOnTeleport();

//!	@return boolean TRUE if the sun should be fixed within the estate, FALSE otherwise
		public function FixedSun();

//!	@return boolean TRUE if non-transacted users should be denied?
		public function DenyTransacted();

//!	@return boolean TRUE to block dwell calculations ?
		public function BlockDwell();

//!	@return boolean TRUE if unverified users should be denied ?
		public function DenyIdentified();

//!	@return boolean TRUE to allow voice within the estate, FALSE otherwise
		public function AllowVoice();

//!	@return boolean TRUE to use global time, FALSE otherwise
		public function UseGlobalTime();

//!	@return integer grid currency price per meter
		public function PricePerMeter();

//!	@return boolean TRUE if land within estate is tax-free, FALSE otherwise
		public function TaxFree();

//!	@return boolean TRUE to enable direct teleport within the estate, FALSE otherwise
		public function AllowDirectTeleport();

//!	@return mixed NULL or redirect grid position x-axis integer
		public function RedirectGridX();

//!	@return mixed NULL or redirect grid position y-axis integer
		public function RedirectGridY();

//!	@return integer Parent Estate ID
		public function ParentEstateID();

//!	@return float Sun Position
		public function SunPosition();

//!	@return boolean ??
		public function EstateSkipScripts();

//!	@return float ??
		public function BillableFactor();

//!	@return boolean TRUE if access to the land within the estate is implicit, FALSE otherwise
		public function PublicAccess();

//!	@return string abuse report email address
		public function AbuseEmail();

//!	@return string Estate owner UUID
		public function EstateOwner();

//!	@return boolean TRUE if underage users are denied access
		public function DenyMinors();

//!	@return boolean TRUE to enable landmarks within the estate, FALSE otherwise
		public function AllowLandmark();

//!	@return boolean TRUE if changes can be made to parcels, FALSE otherwise
		public function AllowParcelChanges();

//!	@return boolean TRUE if a user can set their home location within the estate, FALSE otherwise.
		public function AllowSetHome();

//!	@return array Array of banned user UUIDs
		public function EstateBans();

//!	@return array Array of estate manager user UUIDs
		public function EstateManagers();

//!	@return array Array of UUIDs for groups that have explicit access to the estate
		public function EstateGroups();

//!	@return array Array of UUIDs for users that have explicit access to the estate
		public function EstateAccess();
	}
}
?>