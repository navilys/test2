<?php
/**
 * class.limousinProject.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// limousinProject PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function limousinProject_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function limousinProject_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}
