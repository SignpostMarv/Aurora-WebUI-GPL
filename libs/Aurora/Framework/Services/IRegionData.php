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

	use ReflectionClass;

	class RegionFlags{
//!	Used for new Rez. Random if multiple defined
		const DefaultRegion  = 1   ;

//!	Regions we redirect to when the destination is down
		const FallbackRegion = 2   ;

//!	Set when a region comes online, unset when it unregisters and DeleteOnUnregister is false
		const RegionOnline   = 4   ;

//!	Region unavailable for direct logins (by name)
		const NoDirectLogin  = 8   ;

//!	Don't remove on unregister
		const Persistent     = 16  ;

//!	Don't allow registration
		const LockedOut      = 32  ;

//!	Don't allow moving this region
		const NoMove         = 64  ;

//!	This is an inactive reservation
		const Reservation    = 128 ;

//!	Require authentication
		const Authenticate   = 256 ;

//!	Record represents a HG link
		const Hyperlink      = 512 ;

//!	Hides the sim except for those on the access list
		const Hidden         = 1024;

//!	Safe to login agents to
		const Safe           = 2048;

//!	Starting region that you can only go to once
		const Prelude        = 4096;

//!	Region is not in this grid
		const Foreign        = 8192;

		public static function isValid($flags){
			if(is_integer($flags) === true && $flags >= 0){
				static $sum;
				if(isset($sum) === false){
					$reflection = new ReflectionClass(get_called_class());
					$sum = array_sum($reflection->getConstants());
				}
				return ($flags <= $sum);
			}
			return false;
		}
	}
}
?>