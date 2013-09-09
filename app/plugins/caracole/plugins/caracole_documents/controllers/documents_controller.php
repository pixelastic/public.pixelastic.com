<?php
/**
 *	DocumentsController
 *	Helps in handling file uploads
 **/
class DocumentsController extends AppController {

	/**
	 *	upload
	 *
	 *	This method is called by the asynchronous uploader.
	 *	It can only be called by SWFUpload, with a special set of data.
	 *	First, we need an actionUrl variable that match the url where the form was supposed to be submitted.
	 *	We also need the fieldName that upload is refering to (without the upload_part)
	 *		We'll use this two informations to grab the Model (and possibly plugin) to use when validating the field
	 *
	 *	The file upload data itself must be send in the Filedata key
	 *
	 *	TODO : It is quite trivial for an attacker to change the fieldName and actionUrl vars to bypass the whole validation
	 *	stuff. If you really need secure upload validation, just don't use the SWFUploader
	 **/
	function upload() {
		// Fast fail if not from Flash
		if (!$this->RequestHandler->isFlash()) return $this->cakeError('error404');
		// Fast fail if actionUrl nor fieldName are set
		if (empty($this->params['form']['actionUrl']) || empty($this->params['form']['fieldName'])) {
			return $this->cakeError('error404');
		}
		// Fast fail if no Filedata key
		if (!isset($this->params['form']['Filedata']) || !isset($this->params['form']['Filedata']['name'])) {
			$this->Session->setFlash(__d('caracole_documents', 'No file uploaded', true), 'error');
			return;
		}

		// Parsing the url to find the modelName, plugin Name and initiating the corresponding model
		$actionUrl = Router::parse($this->params['form']['actionUrl']);
		$modelName = Inflector::classify($actionUrl['controller']);
		$modelIncludeName = empty($actionUrl['plugin']) ? $modelName : Inflector::camelize($actionUrl['plugin']).'.'.$modelName;
		$model = &ClassRegistry::init($modelIncludeName);

		// Getting the fieldName and its upload counterpart
		$fieldName = substr(str_replace('data['.$modelName.'][', '', $this->params['form']['fieldName']), 0, -1);
		$uploadFieldName = 'upload_'.$fieldName;

		// Creating an instance of the model and uploading its inner field
		$model->create(array(
			$modelName => array(
				$fieldName => null,
				$uploadFieldName => $this->params['form']['Filedata']
			)
		));
		// Create the new Document item if validates and populate the model data with it
		$this->model->loadIDFromUpload($model);

		// If do not validate, we return an error
		if (!empty($model->validationErrors) || !$model->validates()) {
			$this->Session->setFlash(array_pop($model->validationErrors), 'error');
			return;
		}

		// We get the newly saved document
		$documentData = $this->model->find('first', array('conditions' => array($this->model->name.'.id' => $model->data[$modelName][$fieldName])));

		// Returning data
		$this->set('data', array('error' => false, 'data' => $documentData));

	}


function admin_upload() {
	$this->setAction('upload');
}









}
