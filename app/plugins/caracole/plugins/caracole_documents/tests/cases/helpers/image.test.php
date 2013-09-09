<?php
App::import('Core', array('Controller', 'View'));
App::import('Helper', array(
	'Form', 'Html', 'Time',
	'Caracole.CaracoleForm', 'Caracole.CaracoleHtml', 'Caracole.Fastcode',
	'CaracoleIcons.Icon',
	'CaracoleDocuments.Document', 'CaracoleDocuments.Image'
));
class ImageHelperTestCase extends CakeTestCase {

	function startTest() {
		// Init both controller and view, used by the main helper
		$this->controller =& new Controller();
		$this->view =& new View($this->controller);

		// Init needed helpers
		$this->helper = &new ImageHelper();
			$this->helper->Html = &new HtmlHelper();

		$this->helper->Fastcode = &new FastcodeHelper();
		$this->helper->Fastcode->Time = &new TimeHelper();
		$this->helper->Fastcode->CaracoleHtml = &new CaracoleHtmlHelper();
			$this->helper->Fastcode->CaracoleHtml->Html = $this->helper->Html;
			$this->helper->Fastcode->CaracoleHtml->Fastcode= $this->helper->Fastcode;
		$this->helper->Fastcode->CaracoleForm = &new CaracoleFormHelper();
			$this->helper->Fastcode->CaracoleForm->Form = &new FormHelper();
			$this->helper->Fastcode->CaracoleForm->Fastcode= $this->helper->Fastcode;
		$this->helper->Fastcode->Icon = &new IconHelper();
			$this->helper->Fastcode->Icon->Html = $this->helper->Html;


		$this->helper->Fastcode->beforeRender();	// Loads all helpers in Fastcode

		$this->data = array(
			'id' => 'uuid',
			'ext' => 'png',
			'mimetype' => 'image/png',
			'filename' => 'foo',
			'filesize' => 3,
			'path' => 'foo/bar/baz/uuid.png',
			'width' => '800',
			'height' => '600'
		);

	}

	// Default image without options should set an img tag to the original file
	function testGetImage() {
		$result = $this->helper->image($this->data);
		$this->assertTags($result, array(
			'img' => array(
				'src' => '/foo/bar/baz/uuid/foo.png',
				'width' => '800',
				'height' => '600',
				'alt' => ''
			)
		));
	}



	// Getting an image tag for a resized url
	function testGetEncodedResizeSquareUrl() {
		$result = $this->helper->image($this->data, array('width' => 400, 'height' => 300));
		$this->assertTags($result, array(
			'img' => array(
				'src' => 'preg:/.*/',
				'width' => '400',
				'height' => '300',
				'alt' => ''
			)
		));
	}

	/**
	 *
	 *	Ensuite je fais quelques tests du controller.
	 *		Passe un param square, retourne un carré centré
	 *		Vérifier que ça créé bien une entrée dans la table des documents
	 *		Vérifier que ca ajoute bien des entrées pour les resize_width et resize_height
	 *		Vérifier que faire un appel à quelque chose de déjà généré ne reprocess pas tout
	 *		Possible de vérifier les headers renvoyés ? Pour que la mise en cache fonctionne bien

	 *
	 *	Une fois que le controller est testé, je reviens au helper
	 *		Vérifier que passer des params déjà resizés enclenche direct l'url sans process
	 *		Si je créé d'abord un 600/10, puis que je demande un 600/400 il doit me regénerer tout
	 *		Idem si je demande un 600/10 puis juste un 600, il doit me le regénerer (on checke les resize_truc)
	 *		Idem si j'ajoute un nouveau param, comme resize = square, il me le regénere si resize = relative
	 *
	 *		Warning : Si je demande un resize relatif, je ne sais pas quelles vont etre les dimensions réelles après le resize
	 *		Je ne peux donc pas mettre des atrributs width et height corrects la première fois... Je peux en mettre des proches, mais pas des exacts
	 *		A moins que je mette la fonction de calcul dans une classe externe que les deux appelleront.
	 *
	 *
	 *	Ensuite, je peux faire de l'optimisation
	 *		Je mets tous les optimisateurs : optipng, pngcrush, etc, je fais quelques benchmarks à coté
	 *		A réfléchir comment optimiser ça, mais ça doit etre transparent. Je peux me limiter à juste un seul tool pour le moment
	 *
	 *		En tout cas, checker que le résultat est plus petit que l'original
	 *
	 *		function getResizeDimensions($data, $width, $height, $type) {
	 *			// Relative


	 }
	 *
	 *
	 **/



}
?>