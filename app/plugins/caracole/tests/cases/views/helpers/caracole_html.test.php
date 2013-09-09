<?php
App::import('Helper', 'Caracole.CaracoleHtml');
App::import('Helper', 'Html');
class CaracoleHelperTestCase extends CakeTestCase {

	function startTest() {
		$this->helper = new CaracoleHtmlHelper();
		$this->helper->Html = new HtmlHelper();
	}


}
?>