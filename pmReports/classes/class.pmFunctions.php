<?php
/**
 * class.pmReports.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// pmReports PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}
