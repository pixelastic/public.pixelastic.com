<?php
class ImageFixture extends CakeTestFixture {
      var $name = 'Image';
      var $import = 'CaracoleDocuments.Image';

      var $records = array(
            array('id' => 'source1', 'parent_id' => 0, 'path' => '__TESTS__/source1.jpg'),
            array('id' => 'version1', 'parent_id' => 'source1', 'path' => '__TESTS__/version1.jpg')
      );


 }
 ?>