<?php
App::import('Libs', 'CaracoleDocuments.CaracoleImage');
class CaracoleImageTestCase extends CakeTestCase {
	function startTest() {
		$this->landscape = array('width' => 800, 'height' => 600);
		$this->portrait = array('width' => 600, 'height' => 800);

		$this->data = array(
			'id' => 'uuid',
			'ext' => 'png',
			'mimetype' => 'image/png',
			'filename' => 'foo',
			'filesize' => 3,
			'path' => 'foo/bar/baz/uuid.png',
			'width' => '800',
			'height' => '600',
			'Version' => array(
				array(
					'id' => 'version1',
					'ext' => 'jpg',
					'mimetype' => 'image/jpg',
					'filename' => 'foo',
					'filesize' => 3,
					'path' => 'foo/bar/baz/uuid.png',
					'width' => '150',
					'height' => '113',
					'resize' => 'relative'
				)
			)
		);
	}

	// Getting square resize dimensions with only one side
	function testGetSquareResizeDimensionsWithOnlyOneSide() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 150, 'resize' => 'square'));
		$this->assertEqual($result['width'], 150);
		$this->assertEqual($result['height'], 150);
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('height' => 150, 'resize' => 'square'));
		$this->assertEqual($result['width'], 150);
		$this->assertEqual($result['height'], 150);
	}

	// Landscape in 150x150 gives 150x113
	function testResizeRelativeLandscape150x150() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 150, 'height' => 150));
		$this->assertEqual($result['width'], 150);
		$this->assertEqual($result['height'], 113);
	}

	// Portrait in 150x150
	function testResizeRelativePortrait150x150() {
		$result = CaracoleImage::getResizeDimensions($this->portrait, array('width' => 150, 'height' => 150));
		$this->assertEqual($result['width'], 113);
		$this->assertEqual($result['height'], 150);
	}

	// Landscape in 150x300
	function testResizeRelativeLandscape150x300() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 150, 'height' => 300));
		$this->assertEqual($result['width'], 150);
		$this->assertEqual($result['height'], 113);
	}

	// Landscape in 300x150
	function testResizeRelativeLandscape300x150() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 300, 'height' => 150));
		$this->assertEqual($result['width'], 200);
		$this->assertEqual($result['height'], 150);
	}

	// Portrait in 150x300
	function testResizeRelativePortrait150x300() {
		$result = CaracoleImage::getResizeDimensions($this->portrait, array('width' => 150, 'height' => 300));
		$this->assertEqual($result['width'], 150);
		$this->assertEqual($result['height'], 200);
	}

	// Portrait in 300x150
	function testResizeRelativePortrait300x150() {
		$result = CaracoleImage::getResizeDimensions($this->portrait, array('width' => 300, 'height' => 150));
		$this->assertEqual($result['width'], 113);
		$this->assertEqual($result['height'], 150);
	}

	// Landscape in 1000x1000
	function testResizeRelativeLandscape1000x1000() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 1000, 'height' => 1000));
		$this->assertEqual($result['width'], 800);
		$this->assertEqual($result['height'], 600);
	}

	// Landscape in 1000x400
	function testResizeRelativeLandscape1000x400() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 1000, 'height' => 400));
		$this->assertEqual($result['width'], 533);
		$this->assertEqual($result['height'], 400);
	}

	// Landscape in 400x
	function testResizeRelativeLandscape400x() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 400));
		$this->assertEqual($result['width'], 400);
		$this->assertEqual($result['height'], 300);
	}

	// Landscape in x400
	function testResizeRelativeLandscapex400() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('height' => 400));
		$this->assertEqual($result['width'], 533);
		$this->assertEqual($result['height'], 400);
	}

	// Landscape in 0.2x0.5
	function testResizeRelativeLandscape02x05() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 0.2, 'height' => 0.5));
		$this->assertEqual($result['width'], 160);
		$this->assertEqual($result['height'], 120);
	}

	// Landscape in 0.5x0.2
	function testResizeRelativeLandscape05x02() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 0.5, 'height' => 0.2));
		$this->assertEqual($result['width'], 160);
		$this->assertEqual($result['height'], 120);
	}

	// Portrait in 0.2x0.5
	function testResizeRelativePortrait02x05() {
		$result = CaracoleImage::getResizeDimensions($this->portrait, array('width' => 0.2, 'height' => 0.5));
		$this->assertEqual($result['width'], 120);
		$this->assertEqual($result['height'], 160);
	}

	// Portrait in 0.5x0.2
	function testResizeRelativePortrait05x02() {
		$result = CaracoleImage::getResizeDimensions($this->portrait, array('width' => 0.5, 'height' => 0.2));
		$this->assertEqual($result['width'], 120);
		$this->assertEqual($result['height'], 160);
	}

	// Url without options links to original file
	function testGetDefaultUrl() {
		$result = CaracoleImage::url($this->data);
		$this->assertEqual($result, '/foo/bar/baz/uuid/foo.png');
	}

	// Setting resize sizes equals to original size are discarded
	function testGetResizeUrlSameSize() {
		$result = CaracoleImage::url($this->data, array('width' => 800, 'height' => 600));
		$this->assertEqual($result, '/foo/bar/baz/uuid/foo.png');
	}

	// Setting a special filename to download
	function testUrlWithFilename() {
		$result = CaracoleImage::url($this->data, array('filename' => 'blabla'));
		$this->assertEqual($result, '/foo/bar/baz/uuid/blabla.png');
	}

	// Getting an encoded url to resize an image
	function testGetEncodedResizeUrl() {
		$result = CaracoleImage::url($this->data, array('width' => 400, 'height' => 300));
		$this->assertEqual($result['plugin'], 'caracole_documents');
		$this->assertEqual($result['controller'], 'images');
		$this->assertEqual($result['action'], 'process');
		$processData = unserialize(base64_decode(str_replace('_', '/', $result['processData'])));
		$this->assertEqual($processData['width'], 400);
		$this->assertEqual($processData['height'], 300);
	}

	// Finding an existing version only if two filters match
	function testGetExistingVersionWithMultipleFilters() {
		$result = CaracoleImage::getExistingVersion($this->data, array('width' => 150, 'height' => 113));
		$this->assertEqual($result['id'], 'version1');
	}

	// Do not find an existing version if only one filter matches
	function testGetExistingVersionWithMultipleFiltersButOnlyOneMatch() {
		$result = CaracoleImage::getExistingVersion($this->data, array('width' => 150, 'height' => 150));
		$this->assertFalse($result);
	}



	// Getting square resize dimensions
	function testGetSquareResizeDimensions() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 150, 'height' => 150, 'resize' => 'square'));
		$this->assertEqual($result['width'], 150);
		$this->assertEqual($result['height'], 150);
	}

	// Getting square resize dimensions if not giving square dimensions
	function testGetSquareResizeDimensionsWithNoIdenticalDimensions() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 150, 'height' => 300, 'resize' => 'square'));
		$this->assertEqual($result['width'], 300);
		$this->assertEqual($result['height'], 300);
	}

	// Square resize with dimensions bigger than original
	function testGetSquareResizeDimensionsWithDimensionsBiggerThanOriginal() {
		$result = CaracoleImage::getResizeDimensions($this->landscape, array('width' => 800, 'height' => 800, 'resize' => 'square'));
		$this->assertEqual($result['width'], 800);
		$this->assertEqual($result['height'], 800);
	}



}
?>