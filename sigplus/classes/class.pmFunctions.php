<?php
/**
 * class.sigplus.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// sigplus PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function sigplus_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function sigplus_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}
