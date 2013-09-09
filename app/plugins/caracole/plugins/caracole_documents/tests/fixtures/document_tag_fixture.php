<?php
// This is a fixture for a dummy model that will NOT have a document attached
class DocumentTagFixture extends CakeTestFixture {
    var $name = 'Tag';


    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'name' => array('type' => 'string', 'length' => 255, 'null' => false)
    );

    var $records = array(
        array('id' => 1, 'name' => 'Tag1'),
    );

 }
