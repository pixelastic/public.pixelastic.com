<?php
	/**
	 *	Instead of displaying the view as is, we will look in each available path and search for a view named
	 *	as the slug. If not, we search the default view.ctp.
	 *
	 *	This will allow us to override the default plugin view as well as define custom views for custom pages
	 *
	 *	This will effectively search the files in this order :
	 *	- app/views/slug.ctp
	 *	- app/views/view.ctp
	 *	- app/plugins/caracole/views/slug.ctp
	 *	- app/plugins/caracole/views/view.ctp
	 *	- app/plugins/caracole/plugins/caracole_pages/views/slug.ctp
	 *	- app/plugins/caracole/plugins/caracole_pages/views/view.ctp
	 **/

	$viewPaths = $this->_paths();
		$viewFiles = array($this->params['pageSlug'], 'view');
	foreach($viewPaths as $viewPath) {
		foreach($viewFiles as $viewFile) {
			$viewFilePath = $viewPath.$this->viewPath.DS.$viewFile.'.ctp';
			if (!file_exists($viewFilePath)) continue;
			// The file exists, we render it
			echo $this->element('../pages/'.$viewFile);
			return;
		}
	}
