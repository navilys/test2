<?php
interface strategyReplication{
	public $aErrors;
	private $aWarnings;
	private $aParameters;
	abstract function validateReplication();
	abstract function replicate();
	abstract function loadParameters();
}
