<?php
class PageFixture extends CakeTestFixture {
      var $name = 'Page';
      var $import = 'CaracolePages.Page';

      var $records = array(
            array('id' => 1, 'name' => 'About us', 'text' => 'Lorem Ipsum', 'slug' => 'about')
      );
 }
 ?>