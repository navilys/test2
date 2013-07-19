<?php

require_once 'classes/interfaces/dashletInterface.php';

class dashletPmTables implements DashletInterface {

  const version = '1.1';

  private $addTabUid;
  private $fields;

  public static function getAdditionalFields($className) {
    $additionalFields = array();

    G::LoadClass('pmTable');
    $additionalTables = AdditionalTables::getAll(0, 1000000);

    $additionalTablesStore = new stdclass();
    $additionalTablesStore->xtype = 'arraystore';
    $additionalTablesStore->fields = array('id', 'value');
    if (!empty($additionalTables['rows']) && is_array($additionalTables['rows'])) {
      foreach ($additionalTables['rows'] as $row) {
        $additionalTablesStore->data[] = array($row['ADD_TAB_UID'], $row['ADD_TAB_NAME']);
      }
    }

    $listeners = new stdclass();
    $listeners->select = "
    function (combo, record, index) {
      dashletInstance.storeAdditionalFields.baseParams = {'DAS_INS_UID': Ext.getCmp('hiddenDasInsUID').getValue(), 'ADD_TAB_UID': Ext.getCmp('ADD_TAB_UID').getValue()};
      dashletInstance.storeAdditionalFields.reload();
    }
    ";

    $additionalTables = new stdclass();
    $additionalTables->xtype = 'combo';
    $additionalTables->name = 'ADD_TAB_UID';
    $additionalTables->id = 'ADD_TAB_UID';
    $additionalTables->fieldLabel = 'PM Table';
    $additionalTables->editable = false;
    $additionalTables->width = 320;
    $additionalTables->store = $additionalTablesStore;
    $additionalTables->mode = 'local';
    $additionalTables->triggerAction = 'all';
    $additionalTables->valueField = 'id';
    $additionalTables->displayField = 'value';
    $additionalTables->allowBlank = false;
    $additionalTables->listeners = $listeners;
    $additionalTables->_afterRender = "
    dashletInstance.storeAdditionalFields = new Ext.data.Store({
      proxy: new Ext.data.HttpProxy({
        url:    '../advancedDashboards/pmTablesAjax',
        method: 'POST'
      }),
      reader: new Ext.data.JsonReader({
        fields: [{name: 'FLD_NAME',  type: 'string'}, {name: 'FLD_DESCRIPTION', type: 'string'}, {name: 'checked', type: 'boolean'}]
      }),
      autoLoad: " . (isset($_REQUEST['DAS_INS_UID']) ? 'true' : 'false') . ",
      listeners: {
        beforeload: function (store) {
          if (Ext.getCmp('ADD_TAB_UID').getValue() != '') {
            dashletInstance.storeAdditionalFields.baseParams = {'DAS_INS_UID': Ext.getCmp('hiddenDasInsUID').getValue(), 'ADD_TAB_UID': Ext.getCmp('ADD_TAB_UID').getValue()};
          }
        },
        load: function(store, record, option) {
          for (var i = 0; i < Ext.getCmp('FIELDS').columns; i++) {
            if (Ext.getCmp('FIELDS').panel.getComponent(i).items.length > 0) {
              Ext.getCmp('FIELDS').panel.getComponent(i).items.each(
                function (element) {
                  element.destroy();
              });
            }
          }
          dashletInstance.storeAdditionalFields.each(function(item, index, totalItems) {
            var newItem = new Ext.form.Checkbox({
                         xtype: 'checkbox',
                         name: 'FIELDS[' + item.get('FLD_NAME') + ']',
                         boxLabel: item.get('FLD_NAME'),
                         inputValue: item.get('FLD_NAME'),
                         checked: item.get('checked')
                       });
            Ext.getCmp('FIELDS').items[index] = Ext.getCmp('FIELDS').panel.getComponent(0).add(newItem);
            Ext.getCmp('FIELDS').panel.getComponent(0).doLayout();
          });
        }
      }
    });
    ";
    $additionalFields[] = $additionalTables;

    $checkboxes = array();
    $listeners = new stdclass();
    if (!isset($_REQUEST['DAS_INS_UID'])) {
      $dummyCheckBox = new stdclass();
      $dummyCheckBox->boxLabel = 'No items to show, please select a PM Table';
      $dummyCheckBox->name = '';
      $dummyCheckBox->disabled = true;
      $checkboxes[] = $dummyCheckBox;
    }
    else {
      $dashletInstance = new DashletInstance();
      $dashletInstanceData = $dashletInstance->load($_REQUEST['DAS_INS_UID']);
      if (is_array($dashletInstanceData['FIELDS']) && count($dashletInstanceData['FIELDS']) > 0) {
        foreach ($dashletInstanceData['FIELDS'] as $fieldName => $checked) {
          $checkBox = new stdclass();
          $checkBox->name = 'FIELDS[' . $fieldName . ']';
          $checkBox->id = 'FIELDS[' . $fieldName . ']';
          $checkBox->boxLabel = $fieldName;
          $checkBox->inputValue = $fieldName;
          $checkBox->checked = $checked == 'true';
          $checkboxes[] = $checkBox;
        }
      }
      else {
        $dummyCheckBox = new stdclass();
        $dummyCheckBox->boxLabel = 'No items to show, please select another PM Table';
        $dummyCheckBox->name = '';
        $dummyCheckBox->disabled = true;
        $checkboxes[] = $dummyCheckBox;
      }
    }

    $additionalTablesFields = new stdclass();
    $additionalTablesFields->xtype = 'checkboxgroup';
    $additionalTablesFields->name = 'FIELDS';
    $additionalTablesFields->id = 'FIELDS';
    $additionalTablesFields->fieldLabel = 'Fields';
    $additionalTablesFields->columns = 1;
    $additionalTablesFields->items = $checkboxes;
    $additionalFields[] = $additionalTablesFields;

    return $additionalFields;
  }

  public static function getXTemplate($className) {
    return "<iframe src=\"{page}?DAS_INS_UID={id}\" width=\"{width}\" height=\"207\" frameborder=\"0\"></iframe>";
  }

  public function setup($config) {
    $this->addTabUid = $config['ADD_TAB_UID'];
    $this->fields = $config['FIELDS'];
  }

  public function render ($width = 300) {
    require_once ("classes/model/AdditionalTables.php");

    $criteria = new Criteria("workflow");

    $listTitle = null;

    ///////
    //SELECT
    $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_NAME);
    $criteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_DESCRIPTION);
    //FROM
    //WHERE
    $criteria->add(AdditionalTablesPeer::ADD_TAB_UID, $this->addTabUid);

    //query
    $rsSQLADDTAB = AdditionalTablesPeer::doSelectRS($criteria);
    $rsSQLADDTAB->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    if (AdditionalTablesPeer::doCount($criteria) > 0) {
      while ($rsSQLADDTAB->next()) {
        $row = $rsSQLADDTAB->getRow();

        $listTitle = ($row["ADD_TAB_DESCRIPTION"] != '' ? $row["ADD_TAB_DESCRIPTION"] : $row["ADD_TAB_NAME"]);
      }
    }

    ///////
    $columns = array();
    foreach ($this->fields as $fieldName => $checked) {
      if ($checked == "true") {
        $columns[] = $fieldName;
      }
    }

    G::LoadClass("pmTable");
    $data = AdditionalTables::getAllData($this->addTabUid);

    $pmTablesDataList = new PmTablesDataList($listTitle);
    $pmTablesDataList->columns = $columns;
    $pmTablesDataList->rows = $data["rows"];
    $pmTablesDataList->printTemplate();
  }
}

class PmTablesDataList extends Smarty {

  public $columns = array();
  public $rows = array();

  private $smarty;

  public function __construct($listTitle = null) {
    $this->smarty = new Smarty();
    $this->smarty->compile_dir  = PATH_SMARTY_C;
    $this->smarty->cache_dir    = PATH_SMARTY_CACHE;
    $this->smarty->config_dir   = PATH_THIRDPARTY . "smarty/configs";
    $this->smarty->caching      = false;
    $this->smarty->templateFile = PATH_PLUGINS . "advancedDashboards/templates/list.html";
    $this->smarty->assign("listTitle", $listTitle);
  }

  public function renderTemplate() {
    $this->smarty->assign("columns", $this->columns);
    $this->smarty->assign("rows", $this->rows);
    return $this->smarty->fetch($this->smarty->templateFile);
  }

  public function printTemplate() {
    die($this->renderTemplate());
  }

}
?>