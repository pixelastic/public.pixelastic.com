<?php
// This is a fixture for the binding table between Collection and Documents
class CollectionsDocumentFixture extends CakeTestFixture {
    var $name = 'CollectionsDocument';


    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'collection_id' => array('type' => 'integer'),
        'document_id' => array('type' => 'string', 'length' => 36),


    );

    var $records = array(
        array('id' => 1, 'collection_id' => 1, 'document_id' => 'testId'),
        array('id' => 2, 'collection_id' => 1, 'document_id' => 'testIdTwo'),
    );

 }