<?php
	/**
	 *	CaracoleBlogPostRoute
	 *	Will check the validity of the given slug
	 **/
class CaracoleBlogPostRoute extends CakeRoute {

    /**
	 *	parse
	 *	Parses a string url and return the param array.
	 *	Will be used to check if the given id exists and is published
	 **/
	function parse($url) {
        // Initial params
		$params = parent::parse($url);
        if (empty($params)) {
            return false;
        }

        // Getting the model
		$Post = &ClassRegistry::init('CaracoleBlog.Post');

        // Check if the id exists
		if (!empty($params['id'])) {
			$count = $Post->find('firstPublished', array(
				'conditions' => array('Post.id' => $params['id']),
		        'contain' => false
			));
			if (empty($count)) return false;
		}

		// Returning params
		return $params;
    }
}
