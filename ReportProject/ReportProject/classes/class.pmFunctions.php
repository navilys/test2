<?php
/**
 * class.ReportProject.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// ReportProject PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function ReportProject_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function ReportProject_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}
