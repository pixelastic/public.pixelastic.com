<?php
class SluggablePageFixture extends CakeTestFixture {
    var $name = 'Page';

    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'name' => array('type' => 'string', 'length' => 255, 'null' => false),
        'slug' => array('type' => 'string',  'length' => 255, 'null' => false),
    );

    var $records = array(
        array('id' => 1, 'name' => 'Test', 'slug' => 'test'),
    );

 }
