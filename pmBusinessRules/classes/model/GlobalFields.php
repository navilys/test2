<?php

require_once 'classes/model/om/BaseGlobalFields.php';

/**
 * Skeleton subclass for representing a row from the 'GLOBAL_FIELDS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class GlobalFields extends BaseGlobalFields {

    public function getAll()
    {
        $criteria = new Criteria( 'workflow' );

        $criteria->addSelectColumn(GlobalFieldsPeer::GF_NAME);
        $criteria->addSelectColumn(GlobalFieldsPeer::GF_VALUE);
        $criteria->addSelectColumn(GlobalFieldsPeer::GF_TYPE);
        $criteria->addSelectColumn(GlobalFieldsPeer::GF_QUERY);
        $criteria->addSelectColumn(GlobalFieldsPeer::DBS_UID);

        //execute the query
        $dataset = GlobalFieldsPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rows = array();
        $types = self::getTypes();

        while ($dataset->next()) {
            $row = $dataset->getRow();
            $row['GF_TYPE_LABEL'] = array_key_exists($row['GF_TYPE'], $types)
                ? $types[$row['GF_TYPE']] : $row['GF_TYPE'];
            $rows[] = $row;
        }

        return $rows;
    }

    public static function getTypes()
    {
        return array(
            'string'  => 'String', // labels can be translated here!
            'integer' => 'Integer',
            'query'   => 'Query'
        );
    }
}

// GlobalFields
