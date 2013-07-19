<?php
G::LoadClass('pmTable');
$additionalTables = new AdditionalTables();
$additionalTableData = $additionalTables->load($_REQUEST['ADD_TAB_UID'], true);
if ($_REQUEST['DAS_INS_UID'] != '') {
  G::LoadClass('pmDashlet');
  $dashletInstance = new DashletInstance();
  $dashletInstanceData = $dashletInstance->load($_REQUEST['DAS_INS_UID']);
}
$fields = array();
foreach ($additionalTableData['FIELDS'] as $additionalTableField) {
  $field = new stdclass();
  $field->FLD_NAME = $additionalTableField['FLD_NAME'];
  $field->FLD_DESCRIPTION = $additionalTableField['FLD_DESCRIPTION'];
  $field->checked = isset($dashletInstanceData['FIELDS'][$additionalTableField['FLD_NAME']]) ? $dashletInstanceData['FIELDS'][$additionalTableField['FLD_NAME']] : true;
  $fields[] = $field;
}
die(G::json_encode($fields));