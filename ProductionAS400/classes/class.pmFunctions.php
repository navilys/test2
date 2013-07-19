<?php
/**
 * class.ProductionAS400.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// ProductionAS400 PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function ProductionAS400_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function ProductionAS400_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}
