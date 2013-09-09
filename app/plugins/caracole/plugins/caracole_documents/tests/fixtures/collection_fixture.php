<?php
// This is a fixture for a dummy model that will have Documents attached in a HABTM way
class CollectionFixture extends CakeTestFixture {
    var $name = 'Collection';


    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'name' => array('type' => 'string', 'length' => 255, 'null' => false)
    );

    var $records = array(
        array('id' => 1, 'name' => 'Collection'),
    );

 }