<?php
class DocumentFixture extends CakeTestFixture {
      var $name = 'Document';
      var $import = 'CaracoleDocuments.Document';

      var $records = array(
            array('id' => 'testId', 'path' => 'testPath'),
            array('id' => 'testIdTwo', 'path' => 'testPathTwo')
      );
 }
 ?>