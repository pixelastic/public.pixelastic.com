<?php
class MetadataFixture extends CakeTestFixture {
      var $name = 'Metadata';
      var $import = 'CaracoleDocuments.Metadata';

      var $records = array(
            array('id' => 1, 'document_id' => 'testId', 'name' => 'author', 'value' => 'myself'),
            array('id' => 2, 'document_id' => 'testIdTwo', 'name' => 'width', 'value' => 800),
            array('id' => 3, 'document_id' => 'testIdTwo', 'name' => 'height', 'value' => 600)
      );
 }
 ?>