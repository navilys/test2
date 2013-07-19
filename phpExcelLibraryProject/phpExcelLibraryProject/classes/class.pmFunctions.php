<?php
/**
 * class.phpExcelLibraryProject.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// phpExcelLibraryProject PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function phpExcelLibraryProject_getMyCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function phpExcelLibraryProject_getMyCurrentTime()
{
	return G::CurDate('H:i:s');
}
