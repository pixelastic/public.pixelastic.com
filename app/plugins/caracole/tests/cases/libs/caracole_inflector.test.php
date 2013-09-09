<?php
class CaracoleInflectorTestCase extends CakeTestCase {
	function startTest() {
	

	}

	// Translate special signs to full words
	function testTranslateSpecialSigns() {
		CaracoleInflector::init();
		$result = Inflector::slug('cœur');
		$this->assertEqual($result, 'coeur');

		$result = Inflector::slug('CŒUR');
		$this->assertEqual($result, 'COEUR');

		$result = Inflector::slug('µTorrent');
		$this->assertEqual($result, 'uTorrent');

		$result = Inflector::slug('3 €');
		$this->assertEqual($result, '3_euro');

		$result = Inflector::slug('3 £');
		$this->assertEqual($result, '3_pound');

		$result = Inflector::slug('3 ¥');
		$this->assertEqual($result, '3_yen');

		$result = Inflector::slug('© myself');
		$this->assertEqual($result, 'copyright_myself');

		$result = Inflector::slug('® myself');
		$this->assertEqual($result, 'registered_myself');

		$result = Inflector::slug('™ myself');
		$this->assertEqual($result, 'trademark_myself');
	}

	// Remove useless words from slugs
	function testCleanSlug() {
		$result = CaracoleInflector::cleanSlug('how-to-correctly-optimize-web-performance-anywhere', 'eng');
		$this->assertEqual($result, 'correctly-optimize-web-performance');

		$result = CaracoleInflector::cleanSlug('i-eat-an-apple-yesterday-and-it-was-delicious', 'eng');
		$this->assertEqual($result, 'eat-apple-yesterday-delicious');
	}

	// Clean slug un french
	function testCleanSlugFrench() {
		$result = CaracoleInflector::cleanSlug('comment-correctement-optimiser-les-performances-web-partout', 'fre');
		$this->assertEqual($result, 'correctement-optimiser-performances-web');

		$result = CaracoleInflector::cleanSlug('j-ai-mange-une-pomme-hier-et-elle-etait-delicieuse', 'fre');
		$this->assertEqual($result, 'mange-pomme-hier-delicieuse');
	}




}
?>