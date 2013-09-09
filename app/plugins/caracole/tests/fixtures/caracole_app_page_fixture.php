<?php
// This model is used as a dummy model to test the CaracoleApp main model
class CaracoleAppPageFixture extends CakeTestFixture {
    var $name = 'Page';

    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'name' => array('type' => 'string', 'length' => 255, 'null' => false),
    );

    var $records = array(
        array('id' => 1, 'name' => 'About'),
        array('id' => 2, 'name' => 'Bibliography')
    );

 }
