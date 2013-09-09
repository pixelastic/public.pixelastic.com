<?php
	/**
	 *	Creating an html space invader canvas
	 **/
	// Default size is 5x5
	if (empty($width)) $width = 5;
	if (empty($height)) $height = 5;
	if (empty($id)) $id = 'spaceinvader';

	echo $this->Fastcode->div(null, array('class' => 'spaceinvader', 'id' => $id));
		for($i=0;$i!=$height;$i++) {
			for($j=0;$j!=$width;$j++) {
				// If it the last in line, we add a special class
				$extraClass = ($j==0) ? ' pixel-first' : '';
				echo '<div class="pixel pixel-'.$i.'-'.$j.$extraClass.'"></div>';
			}
		}
	echo '</div>';
