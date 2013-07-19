<?php

require_once 'classes/model/om/BaseRuleSet.php';


/**
 * Skeleton subclass for representing a row from the 'RULE_SET' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class RuleSet extends BaseRuleSet {

    public function getAll()
    {
        $criteria = new Criteria( 'workflow' );

        $criteria->addSelectColumn(RuleSetPeer::RST_UID);
        $criteria->addSelectColumn(RuleSetPeer::RST_NAME);
        $criteria->addSelectColumn(RuleSetPeer::RST_DESCRIPTION);
        $criteria->addSelectColumn(RuleSetPeer::RST_TYPE);
        $criteria->addSelectColumn(RuleSetPeer::RST_STRUCT);
        $criteria->addSelectColumn(RuleSetPeer::RST_SOURCE);
        $criteria->addSelectColumn(RuleSetPeer::RST_CHECKSUM);
        $criteria->addSelectColumn(RuleSetPeer::RST_CREATE_DATE);
        $criteria->addSelectColumn(RuleSetPeer::RST_UPDATE_DATE);
        $criteria->addSelectColumn(RuleSetPeer::RST_CHECKSUM);

        $criteria->add(RuleSetPeer::RST_DELETED, '0');
        $criteria->addAscendingOrderByColumn(RuleSetPeer::RST_NAME);

        $dataset = RuleSetPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rows = array();

        while ($dataset->next()) {
            $rows[] = $dataset->getRow();
        }

        return $rows;
    }
} // RuleSet
