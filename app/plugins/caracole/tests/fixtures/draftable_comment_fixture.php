<?php
class DraftableCommentFixture extends CakeTestFixture {
    var $name = 'Comment';

    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'page_id' => array('type' => 'integer'),
        'text' => array('type' => 'text'),
        'is_draft' => array('type' => 'integer', 'default' => '0', 'null' => false),
    );

    var $records = array(
        array('id' => 1, 'page_id' => 1, 'text' => 'Ok comment for ok page 1', 'is_draft' => 0),
        array('id' => 2, 'page_id' => 1, 'text' => 'Draft comment for ok page 1', 'is_draft' => 1),
        array('id' => 3, 'page_id' => 2, 'text' => 'Ok comment for drafted page 2', 'is_draft' => 0),
        array('id' => 4, 'page_id' => 2, 'text' => 'Draft comment drafted page 1', 'is_draft' => 1),
    );

 }
