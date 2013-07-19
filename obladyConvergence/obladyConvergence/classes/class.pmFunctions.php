<?php
/**
 * class.obladyConvergence.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// obladyConvergence PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function obladyConvergence_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function obladyConvergence_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}
