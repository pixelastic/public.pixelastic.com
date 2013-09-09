<?php
/**
 *	CaracoleAdminRoute
 *	Handle the routing of all the admin actions. Will strip plugin name from caracole plugins url
 **/
class CaracoleAdminRoute extends CakeRoute {

    /**
     *  parse
     *  Parses an admin url
     **/
    function parse($url) {
        // Home page
        if ($url=='/admin' || $url=='/admin/') {
            return array(
                'admin' => true,
                'prefix' => 'admin',
                'plugin' => 'caracole_pages',
                'controller' => 'pages',
                'action' => 'home',
                'named' => array(),
                'pass' => array()
            );
        }

        // We parse the params and stop if unparsable
        $params = parent::parse($url);
		if (empty($params)) {
            return false;
        }

        // We check if the controller is in a Caracole plugin
        $controller = $params['controller'];
        $pluginControllerList = CaracoleConfigure::getPluginInfo('controllers');
        // It is, so we add the plugin
        if (array_key_exists($controller, $pluginControllerList)) {
            $params['plugin'] = Inflector::underscore($pluginControllerList[$controller]);
        }
		// We pass the id if set
		if (!empty($params['id'])) {
			$params['pass'][] = 'id';
		}

        return $params;
    }

    /**
     *  match
     *  Returns a string url from an array of parameters
     **/
    function match($url) {
		// Fast fail if not and admin action
		if (empty($url['admin'])) return false;
		// We build the returned url in an array
		$returnedUrl = array();
		// We add the admin prefix
		if (!empty($url['admin'])) {
			$returnedUrl[] = 'admin';
			if (!empty($url['action'])) {
				$url['action'] = str_replace('admin_', '', $url['action']);
			}
			unset($url['admin']);
		}
		// We add, in this order, the controller, action and id
		$keys = array('controller', 'action', 'id');
		foreach($keys as $key) {
			if (empty($url[$key])) continue;
			$returnedUrl[] = $url[$key];
			unset($url[$key]);
		}

		// Removing the plugin key
		unset($url['plugin']);

		// Adding the others keys as named parameters
		if (!empty($url)) {
			foreach($url as $key => &$value) {
				// Adding directly numeric keys and key/value for string keys
				$returnedUrl[] = is_numeric($key) ? $value : $key.':'.$value;
			}
		}

		return implode('/', $returnedUrl);
    }

}
