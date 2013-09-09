<?php
/**
 *	PackerHelper
 *	Helper to aggregate and compress all the javascript and css assets in one file.
 *	It will help reduce the number of HTTP requests and file size.
 *
 *	Allow for multiple filter options to serve files only to Internet Explorer or Javascript-enabled browsers.
 *
 *	JS files are compressed independently and then are merged
 *	CSS files are merged then compressed
 *
 *	On production mode (debug=0) files are only regenerated at most once a day, if changed occured.
 *	During development, files are regenerated everytime a change to its inner files are made. In consequences, generation can be a little
 *	slower on dev mode because of all the filemtime() calls.
 *
 *	TODO : See http://www.phpied.com/iphone-caching/ and the part about the cache manifest. Could be a great way of improving cache
 *			 We should write in the cache manifest all the files we are querying in absolute urls ExpiresByType text/cache-manifest       "access plus 0 seconds"
 *	TODO : Should find a way to insert the Google Analytics code in an optimized way : http://mathiasbynens.be/notes/async-analytics-snippet
 **/
class PackerHelper extends AppHelper {
	//	Helpers used
	var $helpers = array(
		'CaracolePacker.PackerCss',
		'CaracolePacker.PackerJs'
	);


	/**
	 *	css
	 *	Adds a css file to the list
	 *
	 *	@param	mixed	$files	Same argument as add()
	 *	@param	array	$options	Same options as add(), with the addition of :
	 *								- media : The media type of the css file (default to 'screen, projection, print')
	 **/
	function css($files, $options = array()) {
		return $this->PackerCss->add($files, $options);
	}

	/**
	 *	js
	 *	Adds a js file to the list
	 *
	 *	@param	mixed	$files	Same argument as add()
	 *	@param	array	$options	Same options as add()
	 **/
	function js($files, $options = array()) {
		return $this->PackerJs->add($files, $options);
	}

	/**
	 *	add
	 *	Adds a file to the list
	 *
	 *	@param	mixed	$files	A file to add, or an array of files.
	 *							-	Files are grabbed from the app/webroot/css/ directory and do not need extensions
	 *							-	The plugin syntax PluginName.filename will grab the corresponding file in the plugin webroot/css directory
	 *	@param	array	$options	An array of options to use with this file(s). Possible values are :
	 *								- ie (boolean) : If true, the file will only be loaded for IE browsers.
	 *								- admin (boolean) : If true, the file will only be loaded for admin actions
	 *								- debug (boolean) : If true, the file will only be loaded if the debug level > 1
	 *								- promote (boolean) : If true, the fille will be loaded in front of the others
	 *								- direct (boolean) : If true, the file will be added in its own script tag, without compression
	 *								- content (text) : If set, this content will be appended directly, as if it was the content of a file
	 **/
	function add($files, $options = array()) {
		// Making sure there is something to add
		if (empty($files)) return false;
		//	Converting a unique file to an array
		if (!is_array($files)) $files = array($files);

		// Default options
		$options = array_merge($this->defaultOptions, $options);

		// Creating a tmp array for this files containing the options and a name key
		$tmpFiles = array();
		foreach($files as &$file) {
			$tmpFiles[] = array_merge($options, array('name' => $file));
		}
		// Adding them at the start or at the end, depending on the promote options
		$this->files = (empty($options['promote'])) ? array_merge($this->files, $tmpFiles) : array_merge($tmpFiles, $this->files);
	}




	/**
	 *	top
	 *	Writing scripts that should be on top of the page, like CSS scripts
	 **/
	function top() {
		// Prepare the script array to get only valid options
		$this->PackerCss->prepare();
		// Classic css
		$this->PackerCss->write();
	}

	/**
	 *	bottom
	 *	Writing scripts that should be at the end of the page to avoid FoC, like Javascript
	 **/
	function bottom() {
		// Prepare the script array to get only valid options
		$this->PackerJs->prepare();
		// Displaying Javascript
		$this->PackerJs->write();
	}




	/**
	 *	prepare
	 *	Clean the existing $files list to get a valid array, it will do a number of things, namely
	 *		-	Removing debug scripts only
	 *		-	Remove non-existent files
	 *		-	Adding a filepath key to each file
	 **/
	function prepare() {
		// Prepending plugin files
		$this->addConfigureFiles(null, true);
		// Prepending default files
		$this->addConfigureFiles('default', true);

		// Flags
		$isDebug = (Configure::read('debug')>0);
		$isAdmin = !empty($this->params['admin']);

		// Scanning the files
		$validFiles = array();
		$scannedFiles = array();
		foreach($this->files as &$file) {
			//	Removing debug files if debug is disabled
			if (!$isDebug && !empty($file['debug'])) continue;
			// Removing admin files if not requesting and admin action
			if (!$isAdmin && !empty($file['admin'])) continue;
			// Removing files already included
			if (in_array($file['name'], $scannedFiles)) continue;

			// Flagging absolute links as direct scripts
			if (strpos($file['name'], 'http://')===0) $file['direct'] = true;

			// Direct files or direct content can stop here
			if (!empty($file['direct']) || !empty($file['content'])) {
				$validFiles[] = $file;
				$scannedFiles[] = $file['name'];
				continue;
			}

			// Getting the exact filepath
			$file['filepath'] = $this->filepath($file['name']);

			// Adding a .php extension to i18n files
			$name = $file['name'];
			if (strpos($name, '.')) list(,$name) = explode('.', $name);
			if (substr($name, 0, 5)=='i18n/') {
				$file['i18n'] = true;
				$file['filepath'].='.php';
			}

			// Removing non-existent files
			if (!file_exists($file['filepath'])) continue;

			// Saving this files
			$validFiles[] = $file;
			$scannedFiles[] = $file['name'];

		}
		$this->files = $validFiles;
	}


	/**
	 *	filepath
	 *	Getting the filepath of a script from its name
	 *
	 *	@param	string	$file	The name of the script, without extension, in the webroot/(css|js) dir. If a plugin is supplied
	 *							(eg. PluginName.file), the file will be search in plugins/plugin_name/webroot/(css|js)
	 **/
	function filepath($file) {
		// Getting correct separator
		$file = str_replace('/', DS, $file);

		// Is that a plugin ?
		if (strpos($file, '.')) {
			list($pluginName, $fileName) = explode('.', $file);
			return App::pluginPath($pluginName).$this->pluginDir.$fileName.'.'.$this->ext;
		}

		// Classic notation, in the app webroot
		return $this->appDir.$file.'.'.$this->ext;
	}

	/**
	 *	write
	 *	Will insert into the document the correct tag to load the specified files compressed into one.
	 *	Files can be filtered by passing the filter parameters
	 *
	 *	@param	array	$filters	An array of filters. Will return only files that match the filters set. If not filter set, all
	 *								files will be returned.
	 *	@param	boolean	$return		If set to true, will return the result instead of displaying it. Default to false
	 **/
	function write($filters = array(), $return = false) {
		$tag = '';

		// First getting the direct elements
		$filteredDirect = $this->filter(array_merge($filters, array('direct' => true)));
		foreach($filteredDirect as $fileDirect) {
			// Directly writing the code
			if (!empty($fileDirect['content'])) {
				$tag.= $this->Javascript->codeBlock($fileDirect['name']);
				continue;
			}

			// Or fetching the file
			$tag.=$this->tag($fileDirect['name'], $filters);
		}

		// Then compressing the others
		$filteredCompress = $this->filter(array_merge($filters, array('direct' => false)));
		if (!empty($filteredCompress)) {
			$tag.= $this->tag($this->url($filteredCompress), $filters);
		}

		// Returning the tag or displaying it
		if ($return) return $tag;
		else echo $tag."\n";
	}

	/**
	 *	filter
	 *	Will filter the current file list to return only the elements matching the filters
	 *
	 *	@param	array	$filters	An array of filters. Will return only files that match the filters set. If not filter set, all
	 *								files will be returned.
	 **/
	function filter($filters = array()) {
		$results = array();
		// Scanning files
		foreach($this->files as $index => $file) {
			$keepFile = true;
			foreach($filters as $filterKey => $filterValue) {
				// If one filter does not match, we discard the file and test the next one
				if ($file[$filterKey]!=$filterValue) {
					$keepFile = false;
					break;
				}
			}
			// Keeping the file
			if ($keepFile) $results[] = $file;
		}

		return $results;
	}

	/**
	 *	url
	 *	Gets the url of the compressed files
	 **/
	function url($files = array()) {
		// Getting file informations
		$compressedFile = $this->getCompressedFile($files);

		// This combinations of files have never been created before, so we will generate a new one
		if (empty($compressedFile)) {
			$url = $this->generate($files);
		}
		// The actual version is too old, we regenerate it too
		else {
			if($this->needsToBeRegenerated($files, filemtime($this->packedDir.$compressedFile))) {
				$url = $this->generate($files);
			} else {
				// We keep the same file if it hasn't changed
				$url = $compressedFile;
			}
		}

		return $this->Fastcode->shardUrl($this->webDir.$url);
	}



	/**
	 *	needsToBeRegenerated
	 *	Checks if a given file list needs to be regenerated in a new one
	 *	In debug mode, a new file will be generated everytime changes has been made to its inner files
	 *	In production, a new one will be generated once a day at most if changed occured
	 *
	 *	For this to correctly work, it is advised that you clean your packed/ directory on every commit on the
	 *	production server.
	 **/
	function needsToBeRegenerated($files, $creationDate) {
		//return true;
		// In production, we won't regenerate if the file is younger than 24 hours
		if (Configure::read('debug')==0) {
			if (mktime()-$creationDate<5184000) return false;
		}
		// Scanning files to check if one is newer than the compressed one
		foreach($files as &$file) {
			if (empty($file['filepath'])) continue;
			if (filemtime($file['filepath'])>$creationDate) return true;
		}
		return false;
	}

	/**
	 *	getCompressedFile
	 *	Gets information about the actual compressed file (name, type, lang, creation date)
	 *
	 **/
	function getCompressedFile($files) {
		// Getting a matching file
		$hash = $this->hash($files);
		$folder = &new Folder();
		$folder->cd($this->packedDir);
		$folderResults = $folder->find($this->hash($files).'_([0-9]{10}).'.$this->ext);

		// Returning the file if found
		if (!empty($folderResults)) {
			return $folderResults[0];
		}
		return false;
	}

	/**
	 *	generate
	 *	Generate a new file from the specified files
	 **/
	function generate($files) {
		// Aggregating the content of the files
		$content = $this->aggregate($files);

		// Css files must be compressed once the whole content is known
		if ($this->ext=='css') {
			$content = $this->compress($content);
		}

		// Adding a header
		$header = array('/**', ' * Original files');
		foreach($files as &$file) {
			$header[] = " * - \t".$file['name'];
		}
		$header[] = ' **/';
		$content = implode("\n", $header)."\n".$content;

		// Getting the old file and removing it
		if ($currentFile = $this->getCompressedFile($files)) {
			unlink($this->packedDir.$currentFile);
		}

		// Formatting the new filename
		$filename = $this->hash($files).'_'.time().'.'.$this->ext;

		// Saving the file
		$compressedFile = new File($this->packedDir.$filename);
		$compressedFile->write($content);

		// Returning the filename
		return $filename;
	}

	/**
	 *	aggregate
	 *	Aggregate the content of multiples files into one big string
	 **/
	function aggregate($files) {
		$content = '';
		// We aggregate the content of all the given files
		foreach($files as &$file) {
			// Appending content
			$content.= $this->getFileContent($file);
			// Adding a final ';' to prevent parsing on js files
			if ($this->ext=='js') $content.=';';
			// Adding a new line
			$content.="\n";
		}
		// Js files should be compressed on a per file basis
		if ($this->ext=='js') {
			$content = $this->compress($content);
		}

		return $content;
	}

	/**
	 *	hash
	 *	Gets a unique hash for a given file list
	 *	The hash is a md5 of the list of names with the language appended
	 **/
	function hash($files = array()) {
		// Appending names
		$tmp = '';
		foreach($files as &$file) $tmp.=$file['name'];
		return md5($tmp.'_'.Configure::read('Config.language'));
	}

	/**
	 *	getFileContent
	 *	Get the content of a file
	 *
	 *	@param	string	$file	The file info
	 *	@return	string	The content of the file
	 **/
	function getFileContent($file) {
		// If the file is directly some code, we return it
		if (!empty($file['content'])) return $file['content'];
		// Need i18n parsing
		if (!empty($file['i18n'])) return $this->getFileContentI18n($file);

		return file_get_contents($file['filepath']);
	}

	/**
	 *	getFileContentI18n
	 *	Will return the js_i18n element that will contain a i18n object ready to be included in the build
	 *
	 **/
	function getFileContentI18n($file) {
		// Works only for js file for now
		if ($this->ext!='js') return false;

		// Insert the element that will display the javascript code
		$view = &ClassRegistry::getObject('view');
		return $view->element('js_i18n', array('plugin' => 'caracole_packer', 'filepath' => $file['filepath']));
	}
}
