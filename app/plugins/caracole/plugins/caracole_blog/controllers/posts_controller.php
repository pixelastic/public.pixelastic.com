<?php
/**
 *	PostsController
 *	Helps in displaying blog posts
 **/
class PostsController extends AppController {
	var $components = array('CaracoleAntispam.Antispam' => array('model' => array('Comment'),));
	// Paginate options
	var $paginate = array(
		'limit' => 10
	);


	/**
	 *	beforeFilter
	 *	We will pass common vars to the view
	 **/
	function beforeFilter() {
		if (empty($this->params['admin'])) {
			$this->set(array(
				'recentPostList' => $this->model->find('recent'),
				'recentCommentList' => $this->model->Comment->find('recent'),
				'popularList' => $this->model->Tag->find('popular', array('limit' => 5))
			));
		}
	}

	/**
	 *	archive
	 *	Displays a calendar of all the previous year and months with blog posts
	 *	Or blog posts of a given date
	 **/
	function archive($year = null, $month = null) {
		// Displaying the whole calendar
		if (empty($year)) {
			$this->set('itemList', $this->model->find('calendar'));
			return $this->render('archive_index');
		}

		// Otherwise we make a conditional search
		if (empty($month)) {
			$conditions = array('SUBSTRING(Post.publish_start, 1, 4)' => $year);
		} else {
			$conditions = array('SUBSTRING(Post.publish_start, 1, 7)' => $year.'-'.$month);
		}
		$this->paginate = Set::merge($this->paginate, array('conditions' => $conditions));
		// Used to use a special find method
		array_unshift($this->paginate, 'published');
		$itemList = $this->paginate();

		$this->set(array(
			'itemList' => $itemList,
		));
	}

	/**
	 *	index
	 *	Displays the latest blog posts
	 **/
	function index() {
		// Getting the published paginated post list
		array_unshift($this->paginate, 'published');
		$itemList = $this->paginate($this->model);

		$this->set(array(
			'itemList' => $itemList
		));
	}

	/**
	 *	search
	 *	Search through all the blog posts to find those that match the search keyword.
	 *	The posted information will be redirected to a get request to use a REST search feature
	 **/
	function search($keyword = null) {
		// We redirect to get it in GET mode
		if (!empty($this->data)) {
			return $this->redirect(array('keyword' => urlencode($this->data['Post']['keyword'])));
		}

		// Search index
		if (empty($keyword)) {
			return $this->setAction('archive');
		}

		// Adding conditions to name and text
		$keyword = urldecode($keyword);
		$this->paginate = Set::merge(
			$this->paginate,
			array(
				'conditions' => array(
					'AND' => array(
						'OR' => array(
							'Post.name LIKE' => '%'.$keyword.'%',
							'Post.text LIKE' => '%'.$keyword.'%'
						)
					)
				)
			)
		);
		// Getting paginated result
		array_unshift($this->paginate, 'published');
		$itemList = $this->paginate();

		$this->set(array(
			'keyword' => $keyword,
			'itemList' => $itemList

		));
	}

	/**
	 *	view
	 *	Display one blog post.
	 **/
	function view($id) {
		// We get the corresponding post
		$item = $this->model->find('firstPublished', array(
			'conditions' => array('Post.id' => $id),
			'contain' => array(
				'Tag',
				'Comment' => array(
					'User' => array(
						'fields' => array('User.is_admin')
					)
				)
			)
		));

		// If the id slug in the url does not match the slug in the database, we redirect to the correct page
		if ($this->params['postSlug']!=$item['Post']['slug']) {
			return $this->redirect(Post::url($item), 301);
		}

		// Comment that do not validate
		if ($this->Session->check('Comment.validationErrors')) {
			// Passing the errors and data
			$this->model->Comment->validationErrors = $this->Session->read('Comment.validationErrors');
			$this->data = $this->Session->read('Comment.validationData');

			// Deleting sessions
			$this->Session->delete('Comment.validationErrors');
			$this->Session->delete('Comment.validationData');
		}

		// We will prefill the comment form with data of the loggued in user
		if (!empty($this->CaracoleAuth->activeUser['is_loggued'])) {
			$this->data = Set::merge(array(
				'Comment' => array(
					'author' => $this->CaracoleAuth->activeUser['User']['nickname'],
					'email' => $this->CaracoleAuth->activeUser['User']['name']
				)
			), $this->data);
		}

		// We will also prefill the comment form with data coming from the cookie
		$cookieData = $this->Cookie->read('Comment.userData');
		if ($cookieData) {
			$this->data = Set::merge($this->data, array(
				'Comment' => $cookieData,
				'Options' => array(
					'is_remember' => true
				)
			));
		}

		$this->set(array(
			'item' => $item
		));
	}





	/**
	 *	admin_edit
	 *	Editing a blog post.
	 **/
	function admin_edit($id = null) {
		$defaultDate = date('Y-m-d H:i:s');

		// Displaying form
		if (empty($this->data)) {
			parent::admin_edit($id);
			// Adding
			if (empty($id)) {
				// Setting the display start date to now
				$this->data['Post']['publish_start'] = $defaultDate;
			}

			return;
		}


		// Default start value is now
		if (empty($this->data['Post']['publish_start']) || $this->data['Post']['publish_start']=='0000-00-00 00:00:00') {
			$this->data['Post']['publish_start'] = $defaultDate;
		}

		parent::admin_edit($id);
	}



}
