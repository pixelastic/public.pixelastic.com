<?php
class DraftablePageFixture extends CakeTestFixture {
    var $name = 'Page';

    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'name' => array('type' => 'string', 'length' => 255, 'null' => false),
        'is_draft' => array('type' => 'integer', 'default' => '0', 'null' => false),
    );

    var $records = array(
        array('id' => 1, 'name' => 'Published', 'is_draft' => 0),
        array('id' => 2, 'name' => 'Work in progress', 'is_draft' => 1)
    );

 }
