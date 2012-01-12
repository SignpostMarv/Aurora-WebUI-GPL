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
/**
*	We also deviate from the class spec, using a single DateTime object instead of a date string + date integer, also using a single cover charge method
*/
	interface EventData{

//!	@return integer Event ID
		public function eventID();

//!	@return string Creator UUID
		public function creator();

//!	@return string Event Subject
		public function name();

//!	@return string Event Category
		public function category();

//!	@return string Event description
		public function description();

//!	@return object instance of DateTime indicating when event started
		public function date();

//!	@return integer number of minutes the events lasts
		public function duration();

//!	@return integer cover charge
		public function cover();

//!	@return string Name of the region that the event is held in.
		public function simName();

//!	@return object instance of OpenMetaverse::Vector3 indicating the grid coordinates for the event
		public function globalPos();

//!	@return integer Event Flags bitfield
		public function eventFlags();

//!	@return integer Content Rating of event
		public function maturity();
	}
}
?>