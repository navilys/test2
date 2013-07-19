<?php

if (!defined('PATH_PM_BUSINESS_RULES')) {
    define('PATH_PM_BUSINESS_RULES', PATH_CORE . 'plugins' . PATH_SEP . 'pmBusinessRules' . PATH_SEP );
}

$functionExec = '';

if (isset($_REQUEST['functionExecute'])) {
    $functionExec = $_REQUEST['functionExecute'];
    unset($_REQUEST['functionExecute']);
}

switch ($functionExec) {
    //TO DELETE SOON
    //
    // case 'processList':
    //     $process = new Process();
    //     $aProcess = $process->getAll();
    //     echo G::json_encode(array('data' => $aProcess, 'total' => count($aProcess)));
    //     break;
    // case 'listExcelFiles':
    //     $data = array();
    //     $pathExcelFiles = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'xls' . PATH_SEP;
    //     G::verifyPath($pathExcelFiles , true);

    //     if ($handle = opendir( $pathExcelFiles )) {
    //         while (false !== ($file = readdir( $handle ))) {
    //             // list of content
    //             if ($file != '.' && $file != '..') {
    //                 $dataFile = pathinfo($file);

    //                 if (isset($dataFile['extension']) && ($dataFile['extension'] == 'xls' || $dataFile['extension'] == 'xlsx')) {
    //                     $data[] = array(    'NAME_FILE' => $dataFile['filename'] . '.' . $dataFile['extension'],
    //                                         'DATE_FILE' => date("Y-m-d H:i:s", filectime($pathExcelFiles . $file)),
    //                                         'SIZE_FILE' => round((filesize($pathExcelFiles . $file)/1024), 2) . ' Kb');
    //                 }
    //             }
    //         }
    //     }

    //     closedir($handle);
    //     echo G::json_encode(array('data' => $data, 'total' => count($data)));
    //     break;
    // case 'listPmrlFiles':
    //     $data = array();
    //     $pathPrmlFiles = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'pmrl' . PATH_SEP;
    //     G::verifyPath($pathPrmlFiles , true);

    //     $data = array();
    //     if ($handle = opendir( $pathPrmlFiles )) {
    //         while (false !== ($file = readdir( $handle ))) {
    //             // list of content
    //             if ($file != '.' && $file != '..') {
    //                 $dataFile = pathinfo($file);

    //                 if (isset($dataFile['extension']) && $dataFile['extension'] == 'pmrl') {
    //                     $data[] = array(    'NAME_FILE' => $dataFile['filename'] . '.' . $dataFile['extension'],
    //                                         'DATE_FILE' => date("Y-m-d H:i:s", filectime($pathPrmlFiles . $file)),
    //                                         'SIZE_FILE' => round((filesize($pathPrmlFiles . $file)/1024), 2) . ' Kb');
    //                 }

    //             }
    //         }
    //     }
    //     closedir($handle);
    //     echo G::json_encode(array('data' => $data, 'total' => count($data)));
    //     break;
    // case 'showPmrlFile':
    //     $content = '';
    //     if (isset($_POST['NAME_FILE'])) {
    //         $pathFile = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'pmrl' . PATH_SEP . $_POST['NAME_FILE'];
    //         if (file_exists($pathFile)) {
    //             $content = file_get_contents($pathFile);
    //         }
    //     }
    //     echo '<textarea id="fieldTextarea" onkeyup="savePmrlFile.enable(); return false;" style="border-style: solid; border-width: 0; padding: 0; height: 100%; width: 100%;">'.$content.'</textarea>';
    //     break;
    // case 'uploadExcelFile':
    //     require_once (PATH_PM_BUSINESS_RULES . 'classes' . PATH_SEP . 'class.RuleBuilder.php');

    //     $ruleBuilder = new RuleBuilder();
    //     $ruleBuilder->processExcelFile();

    //     echo G::json_encode(array('success' => true));
    //     break;
    // case 'deleteExcelFile':
    //     $pathFile = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'xls' . PATH_SEP . $_POST['NAME_FILE'];
    //     G::rm_dir( $pathFile );
    //     echo G::json_encode(array('success' => true));
    //     break;
    // case 'generateFilesPmrl':
    //     require_once (PATH_PM_BUSINESS_RULES . 'classes' . PATH_SEP . 'class.RuleBuilder.php');
    //     $nameExcel = $_POST['NAME_FILE'];
    //     $ruleBuilder = new RuleBuilder($nameExcel);
    //     $ruleBuilder->processExcelFile();
    //     echo G::json_encode(array('success' => true));
    //     break;
    // case 'uploadPmrlFile':
    //     $fileTemp = $_FILES['form']['tmp_name']['FILENAME'];
    //     $fileName = $_FILES['form']['name']['FILENAME'];
    //     $filePath = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'pmrl' . PATH_SEP;
    //     G::uploadFile($fileTemp, $filePath, $fileName, '0777');
    //     chmod( $filePath . $fileName, '0777' );
    //     echo G::json_encode(array('success' => true));
    //     break;
    // case 'deletePmrlFile':
    //     $pathFile = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'pmrl' . PATH_SEP . $_POST['NAME_FILE'];
    //     G::rm_dir( $pathFile );
    //     echo G::json_encode(array('success' => true));
    //     break;
    // case 'editFilePmrl':
    //     if (isset($_POST['NAME_FILE'])) {
    //         $pathFile = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . 'pmrl' . PATH_SEP . $_POST['NAME_FILE'];
    //         if (file_exists($pathFile)) {
    //             file_put_contents($pathFile, $_POST['CONTENT']);
    //         }
    //     }
    //     echo G::json_encode(array('success' => true));
    //     break;

    case 'saveRule':
        $result = array();
        try {
            if (empty($_POST['RST_UID'])) {
                $br = new RuleSet();
                $br->setRstUid(G::generateUniqueID());
                $br->setRstCreateDate(date('Y-m-d H:i:s'));
                $br->setRstUpdateDate(date('Y-m-d H:i:s'));
            } else {
                $br = RuleSetPeer::retrieveByPK($_POST['RST_UID']);
                $br->setRstUpdateDate(date('Y-m-d H:i:s'));
            }

            if (! empty($_POST['data']) && ! is_null($_POST['data'])) {
                $data = json_decode($_POST['data']);
                $source = base64_encode(convertJsonToPmrl($data));
                $br->setRstName($data->name);
                $br->setRstType($data->type);
                $br->setRstSource($source);
                $br->setRstCheckSum(md5($source));
                $br->setRstStruct(base64_encode($_POST['data']));
            } else {
                unset($_POST['RST_UID']);
                $br->fromArray($_POST, BasePeer::TYPE_FIELDNAME);
            }

            $br->save();

            $result['success'] = true;
            $result['RST_UID'] = $br->getRstUid();
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }
        echo G::json_encode($result);
        break;
    case 'getRuleSetSource';
        $id = $_GET['id'];
        $br = RuleSetPeer::retrieveByPK($id);
        echo base64_decode($br->getRstSource());
        break;
    case 'saveSource';
        unset($_POST['functionExecute']);
        $result = array();
        $br = RuleSetPeer::retrieveByPK($_POST['id']);
        $br->setRstSource($_POST['data']);
        $br->setRstUpdateDate(date('Y-m-d H:i:s'));
        try {
            $br->save();
            $result['success'] = true;
            $result['message'] = "Rule Set saved successfully.";
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }
        echo G::json_encode($result);
        break;
    case 'deleteRule':
        $result = array();

        try {
            $br = RuleSetPeer::retrieveByPK($_POST['id']);
            $br->setRstUpdateDate(date('Y-m-d H:i:s'));
            $br->setRstDeleted(true); // just logical deletion
            $br->save();

            $result['success'] = true;
        } catch (Exception $e) {
            $result['success'] = true;
            $result['message'] = $e->getMessage();
        }
        echo G::json_encode($result);
        break;
    case 'getGlobals':
        $globalFields = new GlobalFields();
        $data = $globalFields->getAll();
        foreach ($data as $i => $record) {
            $disabled = $record['GF_TYPE'] == 'query' ? '' : ' disabled';
            $options = '<button class="btn btn-mini x-g-edit" title="Edit"><i class="icon-pencil"></i></button> '
                     . '<button class="btn btn-mini x-g-dbquery" data-id="'.$record['GF_NAME'].'" title="Set DB Query"'.$disabled.'><i class="icon-random"></i></button> '
                     . '<button class="btn btn-mini x-g-delete" data-id="'.$record['GF_NAME'].'" '
                     . 'title="Delete"><i class="icon-trash"></i></button>';

            $data[$i]['_options'] = $options;
        }
        $response = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo G::json_encode($response);
        break;
    case 'deleteGlobals':
        try {
            if (! isset($_POST['GF_NAME'])) {
                throw new Exception("Bad Request: Param 'GF_NAME' is missing");
            }

            $globalFields = GlobalFieldsPeer::retrieveByPK($_POST['GF_NAME']);
            $globalFields->delete();

            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->message;
        }
        echo G::json_encode($response);
        break;
    case 'loadGlobal':
        $globalField = GlobalFieldsPeer::retrieveByPK($_GET['GF_NAME']);
        $data = $globalField->toArray(BasePeer::TYPE_FIELDNAME);
        echo G::json_encode($data);
        break;
    case 'saveGlobals':
        $globalField = GlobalFieldsPeer::retrieveByPK($_POST['GF_NAME']);
        $message = 'Global Variable updated successfully!';

        if (is_null($globalField)) {
            $globalField = new GlobalFields();
            $message = 'New Record saved';
        }

        try {
            $globalField->fromArray($_POST, BasePeer::TYPE_FIELDNAME);
            $globalField->save();

            $response['success'] = true;
            $response['message'] = $message;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->message;
        }
        echo G::json_encode($response);
        break;
    case 'loadDbCnn':
        $dbSource = new DbSource();
        $data = $dbSource->load($_GET['DBS_UID'], 0);
        $data['DBS_PORT'] = $data['DBS_PORT'] ? $data['DBS_PORT'] : '';
        echo G::json_encode($data);
        break;
    case 'getDdCnn':
        require_once "classes/model/DbSource.php";
        $dbSource = new DbSource();
        $criteria = $dbSource->getCriteriaDBSList('0');
        $dataset = RuleSetPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rows = array();
        while ($dataset->next()) {
            $row = $dataset->getRow();

            $options = '<button class="btn btn-mini x-dbcnn-edit" title="Edit" onclick="editDbConnection(\''.$row['DBS_UID'].'\')"><i class="icon-pencil"></i></button> '
                     . '<button class="btn btn-mini x-dbcnn-delete" title="Delete" onclick="deleteDbConnection(\''.$row['DBS_UID'].'\')"><i class="icon-trash"></i></button>';
            $row['_options'] = $options;
            $rows[] = $row;
        }

        $response = array(
            "sEcho" => 1,
            "iTotalRecords" => count($rows),
            "iTotalDisplayRecords" => count($rows),
            "aaData" => $rows
        );

        echo G::json_encode($response);
        break;
    case 'testDbCnn';
        // trim all vars
        foreach ($_POST as $key => $value) {
            $_POST[$key] = trim($value);
        }

        $result = array();
        $type   = $_POST['DBS_TYPE'];
        $server = $_POST['DBS_SERVER'];
        $dbName = $_POST['DBS_DATABASE_NAME'];
        $user   = $_POST['DBS_USERNAME'];
        $passwd = $_POST['DBS_PASSWORD'];
        $port   = $_POST['DBS_PORT'];

        G::LoadClass('net');
        $net = new Net($server);

        try {
            if ($net->getErrno() != 0) {
                throw new Exception("Error: Can't connect to server: $server");
            }

            // try to login on server
            $net->loginDbServer($user, $passwd);
            $net->setDataBase($dbName,$port);

            if ($net->errno == 0) {
                $response = $net->tryConnectServer( $type );

                if ($response->status != 'SUCCESS') {
                    throw new Exception("Error: " . $net->error);
                }

                $response = $net->tryOpenDataBase($type);

                if ($response->status != 'SUCCESS') {
                    throw new Exception("Error: " . $net->error);
                }
            } else {
                throw new Exception("Error: " . $net->error);
            }

            $result['success'] = true;
            $result['message'] = "Test passed successfully";
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        echo G::json_encode($result);
        break;
    case 'saveDbCnn':
        require_once "classes/model/DbSource.php";
        $result = array();
        $isEdit = false;

        if (isset($_POST['DBS_UID']) && ! empty($_POST['DBS_UID'])) {
            $dbSource = DbSourcePeer::retrieveByPK($_POST['DBS_UID'], 0);
            $isEdit = true;
        } else {
            unset($_POST['DBS_UID']);
            $dbSource = new DbSource();
            $dbSource->setDbsUid(G::generateUniqueID());
        }
        try {
            $dbSource->fromArray($_POST, BasePeer::TYPE_FIELDNAME);
            $dbSource->save();

            $result['success'] = true;
            $result['message'] = $isEdit
                ? "Data Base connection updated successfully!"
                : "New Data Base connection created successfully!";
        } catch(Exception $e) {
            $result['message'] = $e->getMessage();
            $result['success'] = false;
        }
        echo G::json_encode($result);
        break;
    case 'deleteDbCnn':
        require_once "classes/model/DbSource.php";
        $result = array();

        try {
            $dbSource = DbSourcePeer::retrieveByPK($_POST['DBS_UID'], 0);
            $dbSource->delete();
            $result['success'] = true;
            $result['message'] = "Data Base connection deleted successfully!";
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        echo G::json_encode($result);
        break;
    case 'exportPmrl':
        unset($_REQUEST['functionExecute']);
        $br = RuleSetPeer::retrieveByPK($_REQUEST['id']);

        $row = array();
        $row['RST_UID']         = $br->getRstUid();
        $row['RST_NAME']        = $br->getRstName();
        $row['RST_DESCRIPTION'] = $br->getRstDescription();
        $row['RST_TYPE']        = $br->getRstType();
        $row['RST_STRUCT']      = $br->getRstStruct();
        $row['RST_SOURCE']      = $br->getRstSource();
        $row['RST_CREATE_DATE'] = $br->getRstCreateDate();
        $row['RST_UPDATE_DATE'] = $br->getRstUpdateDate();
        $row['RST_CHECKSUM']    = $br->getRstChecksum();
        $row['RST_DELETED']     = $br->getRstDeleted();
        $row['PRO_UID']         = $br->getProUid();

        $filename = preg_replace("/[^a-zA-Z0-9]/", "", trim($br->getRstName())) . '.pmrl';

        //get the ProcessMaker version
        if (! defined( 'PM_VERSION' )) {
            if (file_exists( PATH_METHODS . 'login/version-pmos.php' )) {
                include (PATH_METHODS . 'login/version-pmos.php');
            } else {
                $version = 'Development Version';
                define( 'PM_VERSION', $version );
            }
        }

        //build the response, the data and metadata
        $res = new StdClass();
        $res->metadata = array(
            'version' => 1,
            'php' => phpversion(),
            'os' => PHP_OS,
            'pm' => PM_VERSION);
        $res->data = $row;
        header('Content-Type: application/pm');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        print G::json_encode($res);
        die();
    default:
        echo G::json_encode(array('success' => false, "message"=>"undefined method".$functionExec));
        break;
}

function convertJsonToPmrl($data)
{
    $trimmedName = trim($data->name);
    $output = "ruleset \"{$trimmedName}\" {$data->type}\n";

    foreach ($data->ruleset as $rkey => $ruleset) {
        $ruleIndex = $rkey + 1;
        $output .= "rule \"{$trimmedName}_{$ruleIndex}\"\n";

        //processing conditions
        $output .= "    if\n";
        $conditionText = '';
        if (count($ruleset->conditions) > 0 ) {
            foreach ($ruleset->conditions as $cdkey => $condition) {
                $conditionText .= ($conditionText == '') ? '': "\n        AND \n";
                $operator       = translateField($condition->condition);
                if (in_array($operator, array('within','not within') ) ) {
                    $conditionText .= "        " . ($operator == 'within' ? '' : '!');
                    $conditionText .= "in_array( @@{$condition->variable_name} , ";
                    $serieValues = trim (evaluateExpressionArray($condition->value));
                    if (substr($serieValues,0,1) == '"' ||  substr($serieValues,0,1) == "'" ) {
                        $serieValues = substr($serieValues, 1);
                    }
                    if (substr($serieValues,-1) == '"' ||  substr($serieValues,-1) == "'" ) {
                        $serieValues = substr($serieValues, 0, strlen($serieValues) -1);
                    }
                    $values = explode (",",  $serieValues );
                    $valuesOutput = '';
                    foreach ($values as $val) {
                        $valuesOutput[] = is_numeric($val) ? $val : "'" . trim($val) . "'";
                    }
                    $values = implode (", ", $valuesOutput);
                    $conditionText .= "array($values) ) ";

                } else {
                    $conditionText .= "        @@{$condition->variable_name} ";
                    $conditionText .= $operator; //this is the operator
                    $conditionText .= ' ' . evaluateExpressionArray($condition->value);
                }
            }

        } else {
            $conditionText = "        true ";
        }
        $output .= $conditionText;

        $output .= "\n    then\n";
        $returnText = '';
        foreach ($ruleset->conclusions as $clkey => $conclusion) {
            if ($conclusion->conclusion_type == 'return') {
                $returnText .= "        return " . evaluateExpressionArray($conclusion->value) .  ";\n";
            }
            if ($conclusion->conclusion_type == 'variable') {
                $conclusionMode = 'set';
                foreach ($data->columns->conclusions as $col) {
                    $conditionText .= $col->variable_name . ' = ' .$conclusion->conclusion_value. ' ' ;
                    if ($col->variable_name == $conclusion->conclusion_value) {
                        if (isset($col->conclusion_mode)) {
                            $conclusionMode = $col->conclusion_mode;
                        }
                    }
                }
                    //$conditionText .= print_r($data->columns->conclusions,1);
                if ($conclusionMode == 'array') {
                    $output .= "        @@{$conclusion->conclusion_value}[] = ";
                    $output .= evaluateExpressionArray($conclusion->value) .  ";\n";
                }
                if ($conclusionMode == 'concat') {
                    $output .= "        @@{$conclusion->conclusion_value} .= ";
                    $output .= evaluateExpressionArray($conclusion->value) .  ";\n";
                }
                if ($conclusionMode == 'set') {
                    $output .= "        @@{$conclusion->conclusion_value} = ";
                    $output .= evaluateExpressionArray($conclusion->value) .  ";\n";
                }
            }
        }
        $output .= $returnText;
        $output .= "end\n\n";
    }

    //$output .= print_r($data,1);
    return $output;
}

function evaluateExpressionArray($expressionArray = array())
{
    $expression = '';
    //by default return the empty string
    if (empty($expressionArray)) {
        return "''";
    }

    //if there is an expression we have to parse each element of this expression
    foreach ($expressionArray as $element) {
        switch ($element->type){
            case 'EVALUATION':
            case 'LOGIC':
                if (empty($expression)) {
                    $expression .= "{$element->value} ";
                } else {
                    $expression .= " {$element->value} ";
                }
            break;
            case 'CONST':
                $expression .= "'{$element->value}'";
            break;
            case 'VAR':
                $expression .= "{::{$element->value}::}";
            break;
            case 'STRING':
                //$expression .= "'" . $element->value . "'";
                $expression .= "\"" . $element->value . "\"";
            break;
            case 'INT':
                $expression .= " " . intval($element->value) . " ";
            break;
            default:
                $expression .= print_r($element,1) .'xx'. $element->value;
        }
    }
    return $expression;
}

function translateField($field)
{
    switch ($field){
        case 'base_module':
            return 'rst_module';
            break;
        case 'name':
            return 'rst_name';
            break;
        case 'id':
            return 'rst_uid';
            break;
        case 'type':
            return 'rst_type';
            break;
        case 'DT':
            return 'single';
            break;
        case 'DTM':
            return 'multiple';
            break;
        case '=':
            return '==';
            break;
        default:
            return $field;
            break;
    }
}
