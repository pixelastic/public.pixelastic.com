<?php
/**
 *	EditorsController
 *	This controller is used to deal with all the back-end of the tinyMCE editor. Most of the plugins makes call to this
 *	controller to either parse data or display special views
 **/
class EditorsController extends AppController {

	/**
	 *	abbr
	 *	Lets the user enter the complete definition of the abbr
	 **/
	function abbr() {
		$this->data['Editor'] = $this->params['form'];
	}

	/**
	 *	charmap
	 *	Lets the user choose a special char in the list
	 **/
	function charmap() {

	}

	/**
	 *	codeblock
	 *	Choose a language for the code block
	 **/
	function codeblock() {
		$this->data['Editor'] = $this->params['form'];
	}

	/**
	 *	css_content
	 *	Will return a packed version of the main css for styling the tinyMCE editable content + custom
	 *	styles for the current website, packed in one unique file
	 **/
	function css_content() {
		// Setting a javascript header
		$this->header('Content-type: text/javascript');
		$this->layout = 'ajax';
	}

	/**
	 *	habtm
	 *	Creates a new item with a given name for a given model and returns its id
	 *	Used to create on-the-fly habtm relationships.
	 *	TODO : Will mostly work with simple models, like tags that only require one field. The jQuery plugin should
	 *		be extended to provide callbacks for improved handling
	 **/
	function admin_habtm() {
		// We stop if we don't have the necessary vars
		if (empty($this->params['form']['model']) || empty($this->params['form']['value'])) return;

		$modelName = $this->params['form']['model'];
		$value = $this->params['form']['value'];

		// Loading the model (from plugins if necessary)
		$pluginModels = CaracoleConfigure::getPluginInfo('models');
		if (array_key_exists($modelName, $pluginModels)) {
			App::import($pluginModels[$modelName].'.'.$modelName);
		} else {
			App::import($modelName);
		}

		// Saving it
		$model = new $modelName();
		$model->data[$modelName][$model->displayField] = $value;
		if (!$model->validates()) {
			$this->set(array(
				'id' => null,
				'value' => $value
			));
			return;
		}
		$model->save();

		// Adding a flash message
		$this->Session->setFlash(sprintf($model->translate('added'), $value), 'success');
		// Passing the newly added id to the view
		$this->set(array(
			'id' => $model->id,
			'value' => $value
		));
	}

	/**
	 *	help
	 *	Display a simple help text explaining hox the editor works
	 **/
	function help() {
		// Passing tinyMCE version number from the client
		$this->set('data', $this->params['form']);
	}

	/**
	 *	link
	 *	Lets the user add links to tinyMCE area. Default available options are href, title and a checkbox to set target="_blank"
	 **/
	function link() {
		// Passing default values to tinyMCE
		$this->data['Editor'] = $this->params['form'];
	}

	/**
	 *	packer
	 *	tinyMCE packer. Will pack and compress in one file all the needed javascript files (core, themes, plugins and core).
	 *	Will replace the default tiny_mce_gzip.php file
	 **/
	function packer() {
		// Setting a javascript header
		$this->header('Content-type: text/javascript');
		$this->layout = 'ajax';

		// Taking plugin, theme and language list
		$languageList = explode(',', $this->params['url']['languages']);
		$themeList = explode(',', $this->params['url']['themes']);
		$pluginList = explode(',', $this->params['url']['plugins']);

		// Getting the list of files, they will be prepend to the tinyMCE base path
		$fileList = array('tiny_mce');
		// Language files
		foreach ($languageList as $language) {
			$fileList[] = 'langs'.DS.$language;
		}
		// Themes
		foreach($themeList as $theme) {
			$fileList[] = 'themes'.DS.$theme.DS.'editor_template';
			foreach ($languageList as $language) {
				$fileList[] = 'themes'.DS.$theme.DS.'langs'.DS.$language;
			}
		}
		// Plugins
		foreach($pluginList as $plugin) {
			$fileList[] = 'plugins'.DS.$plugin.DS.'editor_plugin';
			foreach ($languageList as $language) {
				$fileList[] = 'plugins'.DS.$plugin.DS.'langs'.DS.$language;
			}
		}

		// Passing the filelist to the view
		$this->set('fileList', $fileList);
	}

	/**
	 *	quote
	 *	Lets the user quote a specified text and add additional information like original link and author
	 **/
	function quote() {
		// Passing default values to tinyMCE
		$this->data['Editor'] = $this->params['form'];
	}

	/**
	 *	admin_search
	 *	Returns a JSON source ready to be used by the autocomplete feature
	 **/
	function admin_search($modelName = null) {
		// We need the model name
		if (empty($modelName)) return;

		// Getting the model and fieldName
		$model =& ClassRegistry::init($modelName);
		$fieldName = $this->params['form']['fieldName'];

		// Getting full list
		$itemList = $model->find('all', array(
			'conditions' => $model->adminSettings['index']['paginate'][$modelName]['conditions'],
			'fields' => array($model->alias.'.'.$model->primaryKey, $model->alias.'.'.$fieldName),
			'contain' => false
		));
		// Building source (passing label and url)
		$source = array();
		$controllerName = Inflector::tableize($modelName);
		foreach($itemList as &$item) {
			$source[] = array(
				'value' => $item[$model->alias][$fieldName],
				'label' => htmlspecialchars($item[$model->alias][$fieldName]),
				'url' => Router::url(array(
					'controller' => $controllerName,
					'action' => 'edit',
					'id' => $item[$model->alias][$model->primaryKey]
				))
			);
		}
		// Passing source to view
		$this->set('source', $source);
	}

	/**
	 *	source
	 *	Edit the HTML Source
	 **/
	function source() {
		// We got the source from the source key in the form
		$this->data['Editor'] = $this->params['form'];
	}



}
