 <?php
class ErrorWebTestCase extends CakeWebTestCase {

	function setUp() {
		//Base url
		$this->baseUrl = Configure::read('SiteUrl.default');
	}


	// Request on /404 return a 404 error
	function test404ErrorOn404Page() {
		$result = $this->get($this->baseUrl.'404');
		$this->assertResponse(404);
	}

}
  ?>