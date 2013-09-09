<?php
class ImageMetadataFixture extends CakeTestFixture {
      var $name = 'Metadata';
      var $import = 'CaracoleDocuments.Metadata';

      var $records = array(
            array('id' => 1, 'document_id' => 'source1', 'name' => 'ext', 'value' => 'jpg'),
            array('id' => 2, 'document_id' => 'source1', 'name' => 'mimetype', 'value' => 'image/jpg'),
            array('id' => 3, 'document_id' => 'source1', 'name' => 'filename', 'value' => 'source1'),
            array('id' => 4, 'document_id' => 'source1', 'name' => 'filesize', 'value' => '415232'),
            array('id' => 5, 'document_id' => 'source1', 'name' => 'width', 'value' => '800'),
            array('id' => 6, 'document_id' => 'source1', 'name' => 'height', 'value' => '600'),
                  // Version 1 150x100
                  array('id' => 7, 'document_id' => 'version1', 'name' => 'ext', 'value' => 'jpg'),
                  array('id' => 8, 'document_id' => 'version1', 'name' => 'mimetype', 'value' => 'image/jpg'),
                  array('id' => 9, 'document_id' => 'version1', 'name' => 'filename', 'value' => 'version1'),
                  array('id' => 10, 'document_id' => 'version1', 'name' => 'filesize', 'value' => '12818'),
                  array('id' => 11, 'document_id' => 'version1', 'name' => 'width', 'value' => '150'),
                  array('id' => 12, 'document_id' => 'version1', 'name' => 'height', 'value' => '100'),

      );


 }
 ?>