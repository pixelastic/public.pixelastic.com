<?php
/**
 *	CommentsController
 *	Helps in handling comments
 **/
class CommentsController extends AppController {

	/**
	 *	add
	 *	Adding a new comment
	 *
	 *	Whenever a comment is added, the user is redirected to the corresponding post page.
	 **/
	function add() {
		// We need data passed and a post id
		if (empty($this->data)) return $this->cakeError('error404');
		$postId = $this->data['Comment']['post_id'];
		if (empty($postId)) return $this->cakeError('error404');

		// Original post
		$post = $this->model->Post->find('first', array('conditions' => array('Post.id' => $postId), 'contain' => false));

		// Creating item
		$this->model->create($this->data);

		// Adding user if loggued in
		if (!empty($this->CaracoleAuth->activeUser)) {
			$this->model->data['Comment']['user_id'] = $this->CaracoleAuth->activeUser['User']['id'];
		}

		// Spam flag
		$this->model->data['Comment']['is_spam'] = $this->Antispam->isSpam($this->data);

		// Adding http:// before links
		if (!empty($this->model->data['Comment']['website']) && substr($this->model->data['Comment']['website'], 0, 7)!='http://') {
			$this->model->data['Comment']['website'] = 'http://'.$this->model->data['Comment']['website'];
		}

		// Does not validate, we stop
		if (!$this->model->validates()) {
			// A spam has been blocked, we will add one to our count
			if (!empty($this->model->validationErrors['spambait'])) {
				$this->model->Post->create($post);
				$this->model->Post->saveField('spam_count', $post['Post']['spam_count']+1);
			}
			// We do not redirect in AJAX, direct display
			if ($this->RequestHandler->isAjax()) {
				$this->set(array('item' => $this->model->data));
				return $this->render();
			}
			// Otherwise we redirect to the post page
			$this->Session->write('Comment.validationErrors', $this->model->validationErrors);
			$this->Session->write('Comment.validationData', $this->data);
			return $this->redirect(Router::url(Post::url($post)).'#CommentAddForm');
		}

		// We normalize the email by lowercasing it
		$this->model->data['Comment']['email'] = strtolower($this->model->data['Comment']['email']);

		// We remember the user data in a cookie
		if (!empty($this->data['Options']['is_remember'])) {
			$this->Cookie->write('Comment.userData', array(
				'author' => $this->model->data['Comment']['author'],
				'email' => $this->model->data['Comment']['email'],
				'website' => $this->model->data['Comment']['website']
			), true, '+2 weeks');
		} else {
			// Otherwise we delete the cookie
			if ($this->Cookie->read('Comment.userData')) {
				$this->Cookie->delete('Comment.userData');
			}
		}

		// Saving headers
		$this->model->data['Comment']['spam_headers'] = CaracoleRequest::getAllHeaders();

		// We save it
		$item = $this->model->save();

		// No redirect if AJAX
		if ($this->RequestHandler->isAjax()) {
			// Clearing the text
			$this->data['Comment']['text'] = null;
			// Adding id to item
			$item['Comment']['id'] = $this->model->id;
			// Setting if the user is an admin or not
			$item['Comment'][Configure::read('Auth.modelAlias')]['is_admin'] = $this->CaracoleAuth->activeUser['User']['is_admin'];
			$this->set(array('item' => $item));
			return $this->render();
		}

		// We redirect to the  post page
		return $this->redirect(Router::url(Post::url($post)).'#comment'.$this->model->id);
	}

	/**
	 *	preview
	 *	Preview of a comment with data passed from the form
	 **/
	function preview() {
		// Setting default value for dummy comment
		$item = Set::merge(array(
			'id' => 'Preview',
			'author' => __d('caracole_blog', 'Anonymous', true),
			'website' => null,
			'email' => 'dummymail',
			'created' => date('Y-m-d H:i:s'),
			Configure::read('Auth.modelAlias') => array(
				'is_admin' => $this->CaracoleAuth->activeUser['User']['is_admin']
			)
		), $this->data['Comment']);

		$this->set(array('item' => $item));
	}


	/**
	 *	admin_delete
	 *	Deleting the specified comment
	 **/
	function admin_delete($id = null) {
		// Converting GET to POST
		if (!empty($id)) {
			$this->data = array('Comment' => array($id => array('checked' => true)));
		}
		return parent::admin_delete();
	}

	/**
	 *	admin_spam
	 *	Flagging/Unflagging as spam
	 **/
	function admin_spam($id = null) {
		// Getting the comment an toggling the value
		$this->model->read(null, $id);
		$value = ($this->model->data['Comment']['is_spam']+1)%2;
		$data = $this->model->data;
		$data['Comment']['is_spam'] = $value;

		// Saving new flag value
		$this->model->saveField('is_spam', $value);

		// Passing info to view
		$this->Session->setFlash(
			empty($value) ? __d('caracole_blog', 'Comment no longer flaggued as spam', true) : __d('caracole_blog', 'Comment flaggued as spam', true)
		, 'success');
		$this->set('item', $data['Comment']);
		return;
	}



}
