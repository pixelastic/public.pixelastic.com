<?php
/**
 *	ContactsController
 **/
class ContactsController extends AppController {
	var $components = array('Email');

	/**
	 *	add
	 *	This is the public action for visitors to contact you
	 **/
	function add() {
		// Form submitted
		if (!empty($this->data)) {
			$this->model->create($this->data);

			// Validation errors
			if (!$this->model->validates()) {
				return $this->render();
			}

			// We save the form in the database
			$this->model->data = $this->model->save();

			// And we send a mail
			$mailAddress = Configure::read('Contact.mail');
			if (!empty($mailAddress)) {
				$this->set('item', $this->model->data);

				// Preparing mail
				$this->Email->controller = $this;
				$this->Email->to = $mailAddress;
				$this->Email->bcc = Configure::read('Contact.bcc');
				$this->Email->subject = sprintf(Configure::read('Contact.subject'), Configure::read('Site.name'));
				$this->Email->replyTo = $this->model->data[$this->model->alias]['email'];
				$this->Email->from = sprintf('%1$s <%2$s>', $this->model->data[$this->model->alias]['name'], $this->model->data[$this->model->alias]['email']);
				$this->Email->template = 'contact';
				$this->Email->sendAs = 'both';

				// Sending mail
				//$this->Email->delivery = 'debug';
				$this->Email->_createboundary();	// Needed for correct display in some Webmails. Cake won't add it
				$this->Email->__header[] = 'MIME-Version: 1.0';
				$this->Email->send();
				//debug($this->Session->read('Message.email.message'), true);
			}

			// We redirect to a thank you page
			return $this->render('add_ok');
		}

	}

}
