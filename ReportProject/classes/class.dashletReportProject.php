<?php
require_once ("classes/interfaces/dashletInterface.php");

class dashletReportProject implements DashletInterface
{
  const version = '1.0';

  private $role;
  private $note;

  public static function getAdditionalFields($className)
  {
    $additionalFields = array();
    $cnn = Propel::getConnection("rbac");
    $stmt = $cnn->createStatement();

    $arrayRole = array();

    $sql = "SELECT ROL_CODE
            FROM   ROLES
            WHERE  ROL_SYSTEM = '00000000000000000000000000000002' AND ROL_STATUS = 1
            ORDER BY ROL_CODE ASC";
    $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    while ($rsSQL->next())
    {
      $row = $rsSQL->getRow();
      $arrayRole[] = array($row["ROL_CODE"], $row["ROL_CODE"]);
    }

    ///////
    $storeRole = new stdclass();
    $storeRole->xtype = "arraystore";
    $storeRole->idIndex = 0;
    $storeRole->fields = array("value", "text");
    $storeRole->data = $arrayRole;

    ///////
    $cboRole = new stdclass();
    $cboRole->xtype = "combo";
    $cboRole->name = "DAS_ROLE";

    $cboRole->valueField = "value";
    $cboRole->displayField = "text";
    $cboRole->value = $arrayRole[0][0];
    $cboRole->store = $storeRole;

    $cboRole->triggerAction = "all";
    $cboRole->mode = "local";
    $cboRole->editable = false;

    $cboRole->width = 320;
    $cboRole->fieldLabel = "Role";
    $additionalFields[] = $cboRole;

    ///////
    $txtNote = new stdclass();
    $txtNote->xtype = "textfield";
    $txtNote->name = "DAS_NOTE";
    $txtNote->fieldLabel = "Note";
    $txtNote->width = 320;
    $txtNote->value = null;
    $additionalFields[] = $txtNote;

    ///////
    return ($additionalFields);
  }

  public static function getXTemplate($className)
  {
    return "<iframe src=\"{" . "page" . "}?DAS_INS_UID={" . "id" . "}\" width=\"{" . "width" . "}\" height=\"207\" frameborder=\"0\"></iframe>";
  }

  public function setup($config)
  {
    $this->role = $config["DAS_ROLE"];
    $this->note = $config["DAS_NOTE"];
  }

  public function render($width = 300)
  {
    $cnn = Propel::getConnection("workflow");
    $stmt = $cnn->createStatement();
    $this->renderFiltre($stmt);
  }
  
  public static function getStatutArray($stmt)
  {
    $arrayStatut = array();
    $sqlStatutLabel = "SELECT TITLE FROM PMT_STATUT WHERE UID <> '999' ORDER BY UID";
    $rsSQL = $stmt->executeQuery($sqlStatutLabel, ResultSet::FETCHMODE_ASSOC);
    while($rsSQL->next())
    {
        $row = $rsSQL->getRow();
        $arrayStatut[] = $row["TITLE"];
    }
    $arrayStatut[] = 'Total';
    return $arrayStatut;
  }

  public function getTypeChequierArray($stmt, $filtering = true)
  {
    $sqlFilteringWhere = '';
    if($filtering && isset($_POST['filtre']) && $_POST['filtre'] != 'NONE')
    {
        $sqlFilteringWhere = " WHERE LABEL = '".$_POST['filtre']."' ";
    }
    $arrayTypeCheq = array();
    $sqlTypeChequierLabel = "SELECT LABEL FROM PMT_TYPE_CHEQUIER ".$sqlFilteringWhere." ORDER BY 1";
    $rsSQL = $stmt->executeQuery($sqlTypeChequierLabel, ResultSet::FETCHMODE_ASSOC);
    while($rsSQL->next())
    {
        $row = $rsSQL->getRow();
        $arrayTypeCheq[] = $row["LABEL"];
    }
    return $arrayTypeCheq;
  }
  
  public function getVilleArray($stmt, $filtering = true)
  {
    $sqlFilteringWhere = '';
    if($filtering && isset($_POST['filtre']) && $_POST['filtre'] != 'NONE')
    {
        $sqlFilteringWhere = " AND LABEL = '".$_POST['filtre']."' ";
    }
    $arrayVille = array();
    $sqlVilleLabel = "SELECT DISTINCT VILLE FROM PMT_DEMANDES as D INNER JOIN PMT_TYPE_CHEQUIER as C ON (D.CODE_CHEQUIER = C.CODE_CD) WHERE STATUT <> '0' and STATUT <> '999' ".$sqlFilteringWhere." ORDER BY 1";
    $rsSQL = $stmt->executeQuery($sqlVilleLabel, ResultSet::FETCHMODE_ASSOC);
    while($rsSQL->next())
    {
        $row = $rsSQL->getRow();
        $arrayVille[] = $row["VILLE"];
    }
    return $arrayVille;
  }
  
  public function getDeptArray($stmt, $filtering = true)
  {
    $sqlFilteringWhere = '';
    if($filtering && isset($_POST['filtre']) && $_POST['filtre'] != 'NONE')
    {
        $sqlFilteringWhere = " AND LABEL = '".$_POST['filtre']."' ";
    }
    $arrayDept = array();
    $sqlDeptLabel = "SELECT DISTINCT COALESCE(CONCAT('Dép. ', SUBSTRING(CP, 1, 2)), 'Non renseigné') AS DEPT FROM PMT_DEMANDES as D INNER JOIN PMT_TYPE_CHEQUIER as C ON (D.CODE_CHEQUIER = C.CODE_CD) WHERE STATUT <> '0' and STATUT <> '999' ".$sqlFilteringWhere." ORDER BY 1";
    $rsSQL = $stmt->executeQuery($sqlDeptLabel, ResultSet::FETCHMODE_ASSOC);
    while($rsSQL->next())
    {
        $row = $rsSQL->getRow();
        $arrayDept[] = $row["DEPT"];
    }
    return $arrayDept;
  }
  
  public function initArray($arrayStatut, $arrayRepartition)
  {
    $arrayFiltre = array();
    for($i = 1, $sizeStatut = count($arrayStatut) ; $i <= $sizeStatut; $i++)
    {
      for($j = 0, $sizeType = count($arrayRepartition) ; $j < $sizeType ; $j++)
      {
          $arrayFiltre[$j][0] = $arrayRepartition[$j];
          $arrayFiltre[$j][$i] = 0;
      }
      $arrayFiltre[count($arrayRepartition)][0] = 'Total';
      $arrayFiltre[count($arrayRepartition)][$i] = 0;
    }
    return $arrayFiltre;
  }

  public function renderFiltre($stmt)
  {
    $arrayResult = array();
    $arrayRepartition = array();
    $arrayStatut = $this->getStatutArray($stmt);
    $arrayTypeCheq = $this->getTypeChequierArray($stmt);
    if(isset($_POST['repartition']))
    {
        switch($_POST['repartition'])
        {
            case 'statut' : $arrayResult = $this->renderRepartition(null, $arrayStatut, array(''), $stmt);
                            break;
            case 'ville' : $arrayRepartition = $this->getVilleArray($stmt);
                           $arrayResult = $this->renderRepartition("D.VILLE", $arrayStatut, $arrayRepartition, $stmt);
                           break;
            case 'departement' : $arrayRepartition = $this->getDeptArray($stmt);
                                 $arrayResult = $this->renderRepartition("DISTINCT COALESCE(CONCAT('Dép. ', SUBSTRING(CP, 1, 2)), 'Non renseigné')", $arrayStatut, $arrayRepartition, $stmt);
                                 break;
            case 'typecheq' : $arrayResult = $this->renderRepartition("C.LABEL", $arrayStatut, $arrayTypeCheq, $stmt);
                              break;
            case 'theme' : $arrayResult = $this->renderRepartition("C.LABEL", $arrayStatut, $arrayTypeCheq, $stmt);
                           break;
        }
    }
    else
    {
        $arrayResult = $this->renderRepartition(null, $arrayStatut, array(''), $stmt);
    }
    $dashletView = new dashletReportProjectView($arrayResult, $this->note, $arrayStatut, $this->getTypeChequierArray($stmt, false));
    $dashletView->templatePrint();
  }
  
  public function renderRepartition($repartitionFieldName, $arrayStatut, $arrayRepartition, $stmt)
  {
    if(isset($repartitionFieldName))
    {
        $repartitionFieldName.= " as REPARTITION, ";
    }
    $arrayResult = $this->initArray($arrayStatut, $arrayRepartition);
    $sqlWhereCheq = '';
    if(isset($_POST['filtre']) && $_POST['filtre'] != 'NONE')
    {
        $sqlWhereCheq = " AND C.LABEL = '".$_POST['filtre']."' "; 
    }
    $sql = "SELECT ".$repartitionFieldName." S.TITLE as STATUS, COUNT(*) as QUANTITY
            FROM PMT_DEMANDES as D INNER JOIN PMT_TYPE_CHEQUIER as C ON (D.CODE_CHEQUIER = C.CODE_CD)
            INNER JOIN PMT_STATUT as S on(D.STATUT = S.UID)
            WHERE STATUT <> '0' and STATUT <> '999' ".$sqlWhereCheq."
            GROUP BY 1, STATUS ORDER BY 1";
    $rsSQL = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
    while ($rsSQL->next())
    {
        $row = $rsSQL->getRow();
        $keyStatut = array_search($row["STATUS"], $arrayStatut);
        if(isset($repartitionFieldName))
        {
            $keyRepartition = array_search($row["REPARTITION"], $arrayRepartition);
        }
        else
        {
            $keyRepartition = 0;
        }
        $arrayResult[$keyRepartition][$keyStatut+1] = $row["QUANTITY"];
        $arrayResult[count($arrayResult)-1][$keyStatut+1] += $row["QUANTITY"];
        $arrayResult[$keyRepartition][count($arrayResult[$keyRepartition])-1] += $row["QUANTITY"];
        $arrayResult[count($arrayResult)-1][count($arrayResult[$keyRepartition])-1] += $row["QUANTITY"];
    }
    return $arrayResult;
  }
}

class dashletReportProjectView extends Smarty
{
  private $smarty;
  private $user;
  private $note;
  private $statut;

  public function __construct($u, $n, $s, $t)
  {
    $this->user = $u;
    $this->note = $n;
    $this->statut = $s;
    $this->types = $t;

    $this->smarty = new Smarty();
    $this->smarty->compile_dir  = PATH_SMARTY_C;
    $this->smarty->cache_dir    = PATH_SMARTY_CACHE;
    $this->smarty->config_dir   = PATH_THIRDPARTY . "smarty/configs";
    $this->smarty->caching      = false;
    $this->smarty->templateFile = PATH_PLUGINS . "ReportProject" . PATH_SEP . "views" . PATH_SEP . "dashletReportProject.html";
  }

  public function templateRender()
  {
    $this->smarty->assign("user", $this->user);
    $this->smarty->assign("note", $this->note);
    $this->smarty->assign("statut", $this->statut);
    $this->smarty->assign("typesCheq", $this->types);

    return ($this->smarty->fetch($this->smarty->templateFile));
  }

  public function templatePrint()
  {
    echo $this->templateRender();
    exit(0);
  }
}
?>
