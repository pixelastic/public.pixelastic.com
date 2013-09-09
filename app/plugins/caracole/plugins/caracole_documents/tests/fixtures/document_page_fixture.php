<?php
// This is a fixture for a dummy model that will have a document attached
class DocumentPageFixture extends CakeTestFixture {
    var $name = 'Page';


    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'name' => array('type' => 'string', 'length' => 255, 'null' => false),
        'document_file' => array('type' => 'string', 'length' => 36),
    );

    var $records = array(
        array('id' => 1, 'name' => 'Empty page', 'document_file' => 0),
        array('id' => 2, 'name' => 'Page with document', 'document_file' => 'testId'),
        array('id' => 3, 'name' => 'Page with another document', 'document_file' => 'testIdTwo'),
    );

 }
