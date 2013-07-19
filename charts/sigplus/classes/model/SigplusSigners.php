<?php

require_once 'classes/model/om/BaseSigplusSigners.php';


/**
 * Skeleton subclass for representing a row from the 'SIGPLUS_SIGNERS' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class SigplusSigners extends BaseSigplusSigners {

  function countAppOutputDocument ( $appUid, $docUid ) {
    $c = new Criteria('workflow');
    $c->clearSelectColumns();
    $c->addSelectColumn('COUNT(*)');
    $c->add ( AppDocumentPeer::APP_UID, $appUid);
    $c->add ( AppDocumentPeer::DOC_UID, $docUid);

    $rs = AppDocumentPeer::doSelectRS($c);
    $rs->next();
    $row = $rs->getRow();
    //print "appUid ->".$appUid. "<br/>docUid->".$docUid;
    //print_r($row);
    return $row[0];
  }
  
} // SigplusSigners
