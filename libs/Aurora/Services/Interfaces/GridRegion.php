<?php
/**
*	This file is based on c# code from the Aurora-Sim project.
*	As such, the original header text is included.
*	Although the location of the c# file in the Aurora-Sim project is within the OpenSim.Services project, this file is placed under the equivalent of Aurora.Services for simplicity.
*/

/*
 * Copyright (c) Contributors, http://aurora-sim.org/, http://opensimulator.org/
 * See CONTRIBUTORS.TXT for a full list of copyright holders.
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

//!	Mimicking the layout of code in Aurora Sim here.
namespace Aurora\Services\Interfaces{
//!	Now in Aurora-Sim, this is a class not an interface. But since it's early days we're going to play it safe and keep our options open.
	interface GridRegion{
//!	The UUID of the region
//!	@return string
		public function RegionID();

//!	The port by which http communication occurs with the region.
//!	@return integer this would be an unsigned integer if PHP had such a thing.
		public function HttpPort();

//!	URI for the host region server.
//!	@return string
		public function ServerURI();

//!	Name of the region.
//!	@return string
		public function RegionName();

//!	Region type
//!	@return string
		public function RegionType();

//!	X-axis location of region within the grid.
//!	@return integer
		public function RegionLocX();

//!	Y-axis location of region within the grid.
//!	@return integer
		public function RegionLocY();

//!	Z-axis location of region within the grid.
//!	@return integer
		public function RegionLocZ();

//!	UUID for Estate owner
//!	@return string We don't have a UUID type yet.
		public function EstateOwner();

//!	Distance across region West to East.
//!	@return integer
		public function RegionSizeX();

//!	Distance across region North to South.
//!	@return integer
		public function RegionSizeY();

//!	Height of region.
//!	@return integer
		public function RegionSizeZ();

//!	Bitfield of region flags.
//!	@return integer
		public function Flags();

//!	No idea what this does.
//!	@return string a UUID.
		public function SessionID();
	}
}
?>