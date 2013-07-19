<?php

class RuleReader {
    public $extensionGlobal = 'G@';
    public $appDataVar = array();
    public $globalVar = array();

    public function __construct ($appData = array(), $global = array())
    {
        $this->appDataVar = $appData;
        $this->globalVar = $global;
    }

    public function parseRule($rstSource, $appData = array(), $global = array())
    {
        $message = '';
        $log = '';
        $rulesFired = array();
        $res = '';
        if (count($appData) == 0) {
            $appData = $this->appDataVar;
        }
        if (count($global) == 0) {
            $global = $this->globalVar;
        }

        $isMultiHit = false;

        $lines  = explode("\n", $rstSource);
        $iLines = 0;

        $ruleInfo = array();
        $rawRule = '';
        $ruleName = '';
        $success = false;
        $successRule = "";
        $successAction = "";
        $successReturn = '';
        $isMultiHit = false;
        while ($iLines < count($lines) && !$success) {
            $line = $lines[$iLines++];
            //print $line . "\n";

            if (preg_match('/^ruleset "{1,1}([^"]*)"{0,1} {0,1}(.*)/',$line,$matches)) {
                //print "start of rule : '" . $matches[1] . "'\n";
                $rulesetName = trim($matches[1]);
                $testHit = strtolower(trim($matches[2]));
                $isMultiHit = ($testHit == 'single') ? false : true;
                continue;
            }

            //if (preg_match('/^rule "{0,1}([^"]*)"{0,1}\s+(.*)$/',$line,$matches)) {
            if (preg_match('/^rule "{1,1}([^"]*)"{0,1} {0,1}(.*)/',$line,$matches)) {
                //print "start of rule : '" . $matches[1] . "'\n";
                $ruleName = trim($matches[1]);
                //$testHit = strtolower(trim($matches[2]));
                //$isMultiHit = ($testHit == 'multi-hit') ? true : false;
                continue;
            }
            if (preg_match('/^ {0,8}if {0,8}/',$line,$matches)) {
                $line = $lines[$iLines++];
                $condition = '';
                $reachThen = (preg_match('/^ {0,8}then {0,8}/',$line, $matches));
                while  ($iLines < count($lines) && !$reachThen) {
                    $condition .= trim($line) . ' ';
                    $line = $lines[$iLines++];
                    $reachThen = (preg_match('/^ {0,8}then {0,8}/',$line, $matches));
                }

                $line = $lines[$iLines++];
                $actions = '';
                $reachEnd = (preg_match('/^ {0,8}end {0,8}/',$line, $matches));
                $returnSentence = '';
                $actionSentences = '';
                while  ($iLines < count($lines) && !$reachEnd) {
                    $reachReturn = (preg_match('/^ {0,8}return {0,8}(.*);/',$line, $matches));
                    if ($reachReturn) {
                        $returnSentence .= trim($line);
                    } else {
                        $actionSentences .= trim($line);
                    }
                    $line = $lines[$iLines++];
                    $reachEnd = (preg_match('/^ {0,8}end {0,8}/',$line, $matches));
                }
                $actions = $actionSentences;

                //print "condition of '$ruleName' is '$condition' \n";
                $toEval = $condition;

                foreach ($appData as $key => $value) {
                    $value = trim($value,'"');
                    $toEval = str_replace('@@'.$key, '"'.$value.'"', $toEval);
                }

                foreach ($global as $key => $value) {
                    $toEval = str_replace($this->extensionGlobal.$key, $value, $toEval);
                }

                if (strpos($toEval, '<') || strpos($toEval, '>')) {
                    $toEval = str_replace('"', '', $toEval);
                }

                $toEval = str_ireplace(' and ', ' && ', $toEval);
                $toEval = str_ireplace(' or ', ' || ', $toEval);
                $result = 0;

                eval ( "\$result = $toEval;");

                if ($result) {
                    $success       = true;
                    $successRule   .= ' ' . $ruleName;
                    $successAction .= ' ' . $actions;
                    //remove the return and ; from sentence
                    $successReturn = str_replace('return ', '',$returnSentence);
                    $successReturn = trim(str_replace(';', '',$successReturn));

                    $log .= "$ruleName evaluated to true\n";
                    $rulesFired[$ruleName] = 'true';
                } else {
                    $log .= "$ruleName evaluated to false\n";
                    $rulesFired[$ruleName] = 'false';
                }

                if ($isMultiHit) {
                    $success = false;
                    $change = trim($successAction);
                    $executes = explode(';', $change);
                    foreach ($executes as $value) {
                        $newValue = trim($value);
                        $parts = explode('=', $newValue);
                        $partName = trim($parts[0]);
                        $res = strpos($partName, '@@');
                        if ($res !== false) {
                            $key = substr($partName, 2);
                            $parts[1] = trim($parts[1]);
                            eval('$appData["'.$key.'"] = $parts[1];');
                        }
                        $res = strpos($partName, $this->extensionGlobal);
                        if ($res !== false) {
                            $key = substr($partName, 2);
                            $parts[1] = trim($parts[1]);
                            eval('$global["'.$key.'"] = $parts[1];');
                        }
                    }
                }
                continue;
            }

            if (preg_match('/^ {0,8}then {0,8}/',$line,$matches)) {
                //print_r($matches);
                continue;
            }

            if (trim($line) != '') {
                $message = 'error in line: ' . $line;
                print $message;
            }

        }

        //when is singlehit and no Success condition executed
        if (!$success && !$isMultiHit) {
            $message = "all rules fired, but all rules returns false!\n";
            print $message;
        }
        //when is singlehit and Success condition found
        if ($success && !$isMultiHit) {
            $message = "rule $successRule executed\n";
        }
        if ($success || $isMultiHit) {
            $successAction = trim ($successAction);
            if (strlen($successAction) > 0) {
                $success = true;
            }
        }

        $r = new StdClass();
        $r->success       = $success;
        $r->successAction = $successAction;
        $r->returnValue   = $successReturn ;
        $r->log           = $log;
        $r->message       = $message;
        $r->rulesFired    = $rulesFired;
        return $r;
    }
}
