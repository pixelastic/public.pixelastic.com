<?php
/**
 *	CaracoleBlogTagRoute
 *	Will check the validity of the given slug
 **/
class CaracoleBlogTagRoute extends CakeRoute {

    /**
	 *	parse
	 *	Parses a string url and return the param array.
	 *	Will be used to check if the given slug exists
	 **/
	function parse($url) {
        // Initial params
		$params = parent::parse($url);
        if (empty($params)) {
            return false;
        }

        // Getting the model
		$Tag = &ClassRegistry::init('CaracoleBlog.Tag');

        // Check if the id exists
		if (!empty($params['tagSlug'])) {
			$count = $Tag->find(
				'count',
				array(
					'conditions' => array('Tag.slug' => $params['tagSlug']),
		            'contain' => false
				)
			);
			if (empty($count)) return false;
		}

		// Returning params
		return $params;
    }
}
