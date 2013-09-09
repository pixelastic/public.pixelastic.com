<?php
/**
 *	TagsController
 *	Helps in displaying blog tags
 **/
class TagsController extends AppController {

	/**
	 *	beforeFilter
	 *	We will pass common vars to the view
	 *
	 *	For a reason I don't realy get, the ordre of the calls is important. Getting the recent comments before the recent posts
	 *	will mess some associations...
	 **/
	function beforeFilter() {
		if (empty($this->params['admin'])) {
			$this->set(array(
				'recentCommentList' => $this->model->Post->Comment->find('recent'),
				'recentPostList' => $this->model->Post->find('recent'),
				'popularList' => $this->model->find('popular', array('limit' => 5))
			));
		}
	}


	/**
	 *	index
	 *	Displays a list of all available tags
	 **/
	function index() {
		// Getting the full list of tags
		$this->set('itemList', $this->model->find('popular'));

	}

	/**
	 *	view
	 *	Displays a list of all posts associated with a given tag
	 **/
	function view($tagSlug) {
		// We first get the tag id and name
		$item = $this->model->find('first', array(
			'conditions' => array('Tag.slug' => $tagSlug),
			'contain' => false
		));

		// We bind the Post model to PostsTag to be able to make conditions on the tag id
		$this->model->Post->bindModel(array(
			'hasOne' => array(
				'PostsTag'
			)
		), false);

		// We set some pagination options to Post
		$this->paginate['Post'] = array(
			'published',
			'limit' => 10,
			'conditions' => array(
				'PostsTag.tag_id' => $item['Tag']['id']
			)
		);
		$itemList = $this->paginate('Post');

		//'recentCommentList' => $this->model->Post->Comment->find('recent'),


		$this->set(array(
			'item' => $item,
			'itemList' => $itemList,
			'recentList' => $this->model->Post->find('recent')
		));
	}



	/**
	 *	admin_merge
	 *	Will delete a tag by moving all its associated posts to an other tag
	 **/
	function admin_merge() {
		// Form submit
		if (!empty($this->data)) {
			$this->model->validate = $this->model->mergeValidate;
			$this->model->create($this->data);

			// If validates, we merge
			if ($this->model->validates()) {
				// Moving all source into destination
				$sourceTags = $this->model->PostsTag->find('all', array(
					'conditions' => array('PostsTag.tag_id' => $this->data['Tag']['source'])
				));
				foreach($sourceTags as &$sourceTag) {
					$this->model->PostsTag->create($sourceTag);
					$this->model->PostsTag->saveField('tag_id', $this->data['Tag']['destination']);
				}
				// Deleting original tag
				$this->model->delete($this->data['Tag']['source']);
				// Adding a flash message
				$this->Session->setFlash(sprintf(
					__d('caracole_blog', 'Source tag has been deleted and its associated posts moved to the destination tag', true),
					$this->data['Tag']['source'],
					$this->data['Tag']['destination']
				), 'success');

				return $this->redirect(array('action' => 'index'));
			}
		}


		// Tag list
		$options = $this->model->find('list');

		// Fields
		$fields = array(
			'source' => array(
				'label' => __d('caracole_blog', 'Source', true),
				'type' => 'select',
				'options' => $options,
				'empty' => __d('caracole_blog', '-- Select a source --', true),
				'help' => __d('caracole_blog', 'Select the source tag. This tag will be deleted and all associated posts will be associated to the destination tag.', true)
			),
			'destination' => array(
				'label' => __d('caracole_blog', 'Destination', true),
				'type' => 'select',
				'empty' => __d('caracole_blog', '-- Select a destination --', true),
				'options' => $options,
				'help' => __d('caracole_blog', 'All tags previously associated with the source tag will now be associated to this tag.', true)
			)
		);

		$this->set(array(
			'fields' => $fields,
			'title' => $this->model->translate('human'),
			'title_for_layout' => __d('caracole_blog', 'Merge tags', true)
		));
	}







}
