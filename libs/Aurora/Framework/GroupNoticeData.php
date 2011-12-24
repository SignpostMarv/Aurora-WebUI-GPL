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
	interface GroupNoticeData{

//!	@return string GroupID that the notice belongs to.
		public function GroupID();

//!	@return string NoticeID unique identifier for the notice.
		public function NoticeID();

//!	@return integer unix timestamp indicating when the group notice was created.
		public function Timestamp();

//!	@return string Name of user that created the group notice.
		public function FromName();

//!	@return string Subject of group notice.
		public function Subject();

//!	@return boolean TRUE of the group notice has an attachment, FALSE otherwise.
		public function HasAttachment();

//!	@return integer asset type
		public function AssetType();

//!	@return string attachment ID
		public function ItemID();

//!	@return string name of attachment
		public function ItemName();
	}
}