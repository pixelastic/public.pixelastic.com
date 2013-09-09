<?php
/**
 *	Metadata
 *	This model is related to the Document model. It will help in holding all data relative to the document, regardless of its type
 *	We'll add some convenient methods into the Document model to help saving and finding associated Metadatas
 *
 **/
class Metadata extends AppModel {
	var $belongsTo = array(
		'Document' => array(
			'className' => 'CaracoleDocuments.Document',
		)
	);
}
