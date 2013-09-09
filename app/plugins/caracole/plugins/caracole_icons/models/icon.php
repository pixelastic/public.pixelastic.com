<?php
/**
 *	Icon
 *	This model is used to automatically generate a CSS Sprite image file and its corresponding CSS file
 *	for illustrating most of the common actions and models.
 *	It is usually used in the admin panel but can also be used on the front-end.
 *	By default, the generated files will be located at :
 *		- app/webroot/img/common/icons.png 		: sprite
 *		- app/webroot/css/icons.css				: css definitions
 *
 *	Every icon can then be accessed in your view by calling : $this->Fastcode->icon('icon_name')
 *
 *	It can be conveniently used to add your own icons for your project or to completly change the look of the default icons.
 *
 *	By default, Caracole is shipped with icons from the nice free Silk icon set (http://www.famfamfam.com/lab/icons/silk/)
 *
 *	If you want to add your own, just drop them in the app/webroot/img/icons/ folder and run the generate script from the admin panel
 *	If you are developing a plugin, add the files to the plugins/pluginName/webroot/img/icons folder
 *
 *	TODO : Instead of generating a PNG file, we should try putting the icon as dataURI in the CSS file. Need to test it first to see if the gain
 *	is worth it. We would need to keep the image based version for IE, tho...
 *
 */

class Icon extends AppModel {
	//	Do not use table
	var $useTable = false;

	var $adminSettings = array(
		'views' => array('index'),
		'toolbar' => array(
			'main' => array(
				'index' => false
			)
		)
	);

	/**
	 *	findAll
	 *	Returns the full list of all available icons found in the app.
	 *	Icons can be found in app/webroot/img/icons an each plugin webroot/img/icon directory
	 **/
	function findAll() {
		// We get all the dir where we are supposed to find icons
		$iconDirs = array();
		$pluginList = CaracoleConfigure::getPluginInfo('list');
		foreach($pluginList as $pluginName => &$pluginPath) {
			$iconDirs[] = $pluginPath.'webroot'.DS.'img'.DS.'icons';
		}
		// Adding app icon dir
		$iconDirs[] = WWW_ROOT.'img'.DS.'icons';

		// Getting the whole icon list
		$iconList = array();
		$folder = &new Folder();
		foreach($iconDirs as $iconPath) {
			// Stop if no dir
			if (!$folder->cd($iconPath)) continue;
			// Stop if no icons
			$icons = $folder->find('.*\.png');
			if (empty($icons)) continue;
			// Saving icons
			foreach($icons as $icon) {
				$file = new File($icon);
				$iconList[$file->name()] = $iconPath.DS.$icon;
			}
		}
		ksort($iconList);

		return $iconList;
	}


	/**
	 *	generate
	 *	Generate the CSS Sprite image and CSS file
	 *
	 *@param	array	$list	An array list of icons. Each key is an icon name and corresponding value is the icon
	 *							filepath
	 **/
	function generate($list) {
		// Calculate dimensions
		$nbrIcons = count($list);
		$nbrCols = floor(sqrt($nbrIcons));
		$nbrRows = $nbrCols + ceil(($nbrIcons/$nbrCols) -$nbrCols);
		$cellWidth = CaracoleConfigure::read('Icons.cell.width');
		$cellHeight = CaracoleConfigure::read('Icons.cell.height');
		$spriteWidth = $cellWidth * $nbrCols;
		$spriteHeight = $cellHeight * $nbrRows;

		// private method for creating an alpha png
		function __png($width, $height) {
			$img = imagecreatetruecolor($width, $height);
			imagealphablending($img, true);
			imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
			imagesavealpha($img, true);
			return $img;
		}

		//	We create a large squared transparent png image
		$spriteImg = __png($spriteWidth, $spriteHeight);

		// We add a CSS rule to load the background image for IE < 9 (IE8 is limited to 32Ko, we will exceed that with default settings)
		$spriteFilename = 'icon-'.mktime().'.png';
		$cssRules = array(
			sprintf('.ie-lt9 .icon { background-image:url(../img/caracole/%1$s); }', $spriteFilename)
		);


		// We add the icons to the png file
		$index = 0;
		foreach($list as $iconName => $iconPath) {
			// Icon position
			$indexRow = floor($index/$nbrCols);
			$indexCol = $index%$nbrCols;

			// We create a cell with the icon centered
			$cellImg = __png($cellWidth, $cellHeight);
			list($iconWidth, $iconHeight) = getimagesize($iconPath);
			$centeredX = floor(($cellWidth - $iconWidth) / 2);
			$centeredY = floor(($cellHeight - $iconHeight) /2);
			imagecopy($cellImg, imagecreatefrompng($iconPath), $centeredX, $centeredY, 0, 0, $iconWidth, $iconHeight);

			//	We place that cell in the grid
			$gridX = $indexCol * $cellWidth;
			$gridY = $indexRow * $cellHeight;
			imagecopy($spriteImg, $cellImg, $gridX, $gridY, 0, 0, $cellWidth, $cellHeight);
			imagedestroy($cellImg);

			// We add the css rule
			$cssRules[] = sprintf('%1$s { background-position:-%2$spx -%3$spx; }', '.icon'.ucfirst($iconName), $gridX, $gridY);

			$index++;
		}

		// Deleting old sprite file
		$folder = &new Folder(WWW_ROOT.'img'.DS.'caracole');
		$sprites = $folder->find('icon-([0-9]{10}).png');
		if (!empty($sprites)) {
			unlink(WWW_ROOT.'img'.DS.'caracole'.DS.$sprites[0]);
		}

		// Saving the CSS Sprite
		$spritePath = WWW_ROOT.'img'.DS.'caracole'.DS.$spriteFilename;
		imagesavealpha($spriteImg, true);
		imagepng($spriteImg, $spritePath, 9);

		// We'll convert the sprite in a dataURI
		$dataURI = base64_encode(file_get_contents($spritePath));
		array_unshift($cssRules, sprintf('.icon { background-image:url(data:image/png;base64,%1$s); }', $dataURI));

		// Saving the CSS rules in file
		$file = &new File(CSS.'icons.css');
		$file->write(implode("\n", $cssRules));


		return $list;
	}



}
