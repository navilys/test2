<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");
// Execute events
require_once 'classes/model/Event.php';

//test d'un remboursement déjà effectué
$selectDemande = "SELECT NUM_DOSSIER FROM PMT_REMBOURSEMENT WHERE ID_DEMANDE = '".$_REQUEST['num_dossier']."' AND STATUT NOT IN(11,999,0)";
$dossierSelectDemande = executeQuery($selectDemande);
if (isset($dossierSelectDemande[1]['NUM_DOSSIER'])) {
    $idDemande = $dossierSelectDemande[1]['NUM_DOSSIER'];

    echo 'Une demande de remboursement existe pour ce dossier sous la référence '.$idDemande;
    die;
}
else {
    

    $caseInstance = new Cases ();
    $eventInstance = new Event();

    $data = $caseInstance->startCase($_REQUEST['task'], $_SESSION['USER_LOGGED']);
    $_SESSION['APPLICATION'] = $data['APPLICATION'];
    $_SESSION['INDEX'] = $data['INDEX'];
    $_SESSION['PROCESS'] = $data['PROCESS'];
    $_SESSION['TASK'] = $_REQUEST['task'];
    $_SESSION['STEP_POSITION'] = 0;

    $newFields = $caseInstance->loadCase($data['APPLICATION']);

    $actuelDatas = convergence_getAllAppData($_REQUEST['uid']);
    $selectAppNumber = "SELECT APP_NUMBER FROM APPLICATION WHERE APP_UID = '".$data['APPLICATION']."' ";
    $dataAppNumber = executeQuery($selectAppNumber);
    $numDossier = $dataAppNumber[1]['APP_NUMBER'];
    
    $newFields['APP_DATA']['NUM_DOSSIER'] = $numDossier;
    
    $newFields['APP_DATA']['ID_DEMANDE'] = $_REQUEST['num_dossier'];

    $newFields['APP_DATA']['DemandeID'] = $_REQUEST['uid'];
    $newFields['APP_DATA']['num_presta'] = $actuelDatas['numPresta'];
    $newFields['APP_DATA']['social'] = $actuelDatas['raisonSociale'];

    $query = 'SELECT * FROM PMT_PRESTATAIRE WHERE SIRET = '.$actuelDatas['idPresta'];
    $presta = executeQuery($query);

    $newFields['APP_DATA']['siret'] = $presta[1]['SIRET'];
    $newFields['APP_DATA']['certification'] = $presta[1]['NUM_CONVENTION'];
    $newFields['APP_DATA']['mail_presta'] = $presta[1]['MAIL'];
    $newFields['APP_DATA']['nomPresta'] = $presta[1]['NOM_CONTACT'];
    $newFields['APP_DATA']['prenomPresta'] = $presta[1]['PRENOM_CONTACT'];
    $newFields['APP_DATA']['adresse1Presta'] = $presta[1]['ADRESSE1'];
    $newFields['APP_DATA']['adresse2Presta'] = $presta[1]['ADRESSE2'];
    $newFields['APP_DATA']['adresse3Presta'] = $presta[1]['ADRESSE3'];
    $newFields['APP_DATA']['cpPresta'] = $presta[1]['CODEPOSTAL'];
    $newFields['APP_DATA']['villePresta'] = $presta[1]['VILLE'];

    $newFields['APP_DATA']['nom'] = $actuelDatas['nom'];
    $newFields['APP_DATA']['prenom'] = $actuelDatas['prenom'];
    $newFields['APP_DATA']['adresse'] = $actuelDatas['numVoie'].' '.$actuelDatas['typeVoie_label'].' '.$actuelDatas['NOMVOIE'].' '.$actuelDatas['CODEPOSTAL'].' '.$actuelDatas['VILLE'];

    if ($actuelDatas['typeVoie'] != 5)
            $newFields['APP_DATA']['adresse'] = $actuelDatas['numVoie'].' '.$actuelDatas['typeVoie_label'].' '.$actuelDatas['nomVoie']." - ".convergence_getCPVille($actuelDatas['codePostal']);
    else
            $newFields['APP_DATA']['adresse'] = $actuelDatas['numeVoie'].' '.$actuelDatas['autreVoie'].' '.$actuelDatas['nomVoie']." - ".convergence_getCPVille($actuelDatas['codePostal']);	


    if ($actuelDatas['thematique'] == 3)
        $newFields['APP_DATA']['surfaceIsolants'] = $actuelDatas['surfaceVentile'];
    else
        $newFields['APP_DATA']['surfaceIsolants'] = $actuelDatas['surfaceIsole'];

    $newFields['APP_DATA']['epaisseur'] = $actuelDatas['r'];

    if ($actuelDatas['vmcSimple']) {
    $newFields['APP_DATA']['typeVentilation'] = "VMC simple flux hygro B, Puissance ventilateur : ".$actuelDatas['vmcSimple'];
    }
    elseif ($actuelDatas['vmcDouble']) { 
    $newFields['APP_DATA']['typeVentilation'] = "VMC double flux, Efficacité de l'échangeur : ".$actuelDatas['vmcDouble'];
    }

    $newFields['APP_DATA']['montant_ht'] = $actuelDatas['coutEnsemble'];
    $newFields['APP_DATA']['montant_tva'] = $actuelDatas['tva'];
    $newFields['APP_DATA']['montant_ttc'] = $actuelDatas['total'];
    $newFields['APP_DATA']['STATUT'] = '1';


    $queryChq = 'SELECT * FROM PMT_CHEQUES WHERE NUM_DOSSIER = '.$_REQUEST['num_dossier']; 
    $chq = executeQuery($queryChq);

    $newFields['APP_DATA']['montant_chq'] = $chq[1]['VN_TITRE'];
    $newFields['APP_DATA']['CODE_OPER'] = $chq[1]['CODE_OPER'];
    $newFields['APP_DATA']['ETAT_TITRE'] = $chq[1]['ETAT_TITRE'];
    //$dateBegin = explode('.', $chq[1]['DEBUT_VALIDITE']);
    //$dateEnd = explode('.', $chq[1]['FIN_VALIDITE']);
    $newFields['APP_DATA']['dateEmission'] = str_replace('.', '-', $chq[1]['DEBUT_VALIDITE']);
    $newFields['APP_DATA']['dateFin'] = str_replace('.', '-',$chq[1]['FIN_VALIDITE']);

    $newFields['APP_DATA']['FLAG_ACTION'] = 'actionCreateCase';
    $newFields['APP_DATA']['FLAGTYPO3'] = 'Off';
    
    PMFSendVariables($data['APPLICATION'], $newFields['APP_DATA']);		    
    $caseInstance->updateCase($data['APPLICATION'], $newFields);
    //$newFields = $caseInstance->loadCase($data['APPLICATION']);
    //if(isset($idDemande)){

    //    echo 'Déjà rmb';
    //}else{
        $eventInstance->createAppEvents($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK']);
    //}
    // Redirect to cases steps
    $nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
    G::header('Location: ../../cases/' . $nextStep['PAGE']);
    //G::header('Location: ../../cases/open?APP_UID=' . $_SESSION['APPLICATION'].'&DEL_INDEX='.$_SESSION['INDEX']);
}
?>
