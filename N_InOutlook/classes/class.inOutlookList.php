<?php
class InOutlookList extends Smarty {

  public $columns = array();
  public $rows = array();
  public $hide = array();
  public $customJS = '';

  private $smarty;

  public function __construct($listTitle = '') {
    $this->smarty = new Smarty();
    $this->smarty->compile_dir  = PATH_SMARTY_C;
    $this->smarty->cache_dir    = PATH_SMARTY_CACHE;
    $this->smarty->config_dir   = PATH_THIRDPARTY . 'smarty/configs';
    $this->smarty->caching      = false;
    $this->smarty->templateFile = PATH_PLUGINS . 'N_InOutlook/templates/list.html';
    $this->smarty->assign('listTitle', $listTitle);
  }

  public function renderTemplate() {
    $this->smarty->assign('columns', $this->columns);
    $this->smarty->assign('rows', $this->rows);
    $this->smarty->assign('hide', $this->hide);
    $this->smarty->assign('customJS', $this->customJS);
    return $this->smarty->fetch($this->smarty->templateFile);
  }

  public function printTemplate() {
    die($this->renderTemplate());
  }

}
?>