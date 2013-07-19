<?php
require_once 'classes/model/Process.php';

$globalFields = new GlobalFields();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$ruleSet = array();
$fields = array();
$globals = $globalFields->getAll();

if (! empty($id)) {
    $ruleSet = RuleSetPeer::retrieveByPK($id)->toArray();
    $ruleSet['RstStruct'] = json_decode(base64_decode($ruleSet['RstStruct']));
    $ruleSet['ProTitle'] = ProcessPeer::retrieveByPK($ruleSet['ProUid'])->getProTitle();
}

// $constants = G::getSystemConstants();
// foreach ($constants as $text => $value) {
//     $fields[] = array('value' => $text, 'text' => '[GLOBAL] ' . $text);
// }

// Getting process dynafields.
$processFields = getDynafields($ruleSet['ProUid']);

foreach ($processFields as $processField) {
    $fields[] = array('value' => $processField['FIELD_NAME'], 'text' => $processField['FIELD_NAME']);
}

foreach ($globals as $global) {
    $fields[] = array('value' => 'global@' . $global['GF_NAME'], 'text' => '[Global] ' . $global['GF_NAME']);
}

include "templates/editor.phtml";


function getDynafields($proUid)
{
    require_once 'classes/model/Dynaform.php';
    $fields = array();
    $fieldsNames = array();
    $type = 'xmlform';

    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->addSelectColumn( DynaformPeer::DYN_FILENAME );
    $oCriteria->add( DynaformPeer::PRO_UID, $proUid );
    $oCriteria->add( DynaformPeer::DYN_TYPE, $type );

    $oDataset = DynaformPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();

    $excludeFieldsList = array (
        'title','subtitle','link','file','button','reset','submit','listbox',
        'checkgroup','grid','javascript', ''
    );

    $labelFieldsTypeList = array ('dropdown','radiogroup');
    G::loadSystem( 'dynaformhandler' );
    $index = 0;

    while ($aRow = $oDataset->getRow()) {
        if (file_exists( PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml' )) {
            $dynaformHandler = new dynaformHandler( PATH_DYNAFORM . $aRow['DYN_FILENAME'] . '.xml' );
            $nodeFieldsList = $dynaformHandler->getFields();

            foreach ($nodeFieldsList as $node) {
                $arrayNode = $dynaformHandler->getArray( $node );
                $fieldName = $arrayNode['__nodeName__'];
                $fieldType = isset($arrayNode['type']) ? $arrayNode['type']: '';
                $fieldValidate = ( isset($arrayNode['validate'])) ? $arrayNode['validate'] : '';

                if (! in_array( $fieldType, $excludeFieldsList ) && ! in_array( $fieldName, $fieldsNames ) ) {
                    $fields[] = array (
                        'FIELD_UID' => $fieldName . '-' . $fieldType,
                        'FIELD_NAME' => $fieldName,
                        'FIELD_VALIDATE'=>$fieldValidate,
                    );
                    $fieldsNames[] = $fieldName;

                    if (in_array( $fieldType, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                        $fields[] = array (
                            'FIELD_UID' => $fieldName . '_label' . '-' . $fieldType,
                            'FIELD_NAME' => $fieldName . '_label',
                            'FIELD_VALIDATE'=>$fieldValidate,
                        );
                        $fieldsNames[] = $fieldName;
                    }
                }
            }
        }
        $oDataset->next();
    }

    sort($fields);

    return $fields;
}


