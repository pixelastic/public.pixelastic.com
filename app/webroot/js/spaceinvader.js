/**
 *	spaceinvader
 *	Will create a random spaceinvader in a given div.spaceinvader
 *
 *	The div targeted should contain a list of div.pixel. The first pixel of each line should have a class of .pixel-first
 **/
(function($) {
	/**
	 *	random
	 *	Return a string reprentation (0 and 1) of a random space invader.
	 *	A space invader offers a vertical symetry, so we only need to generate the left part and copy its mirror image
	 *	to the right
	 **/
	function random(width, height) {
		var middle = Math.ceil(width/2),
			value = [],
			line = [];

		// For each line we grab the x first pixels, then apply their mirror image. And finally, we add the full line to the value
		for(var i = 0; i!=height;i++) {
			line = [];
			// Lightning the first part
			for(var j = 0;j!=middle;j++) line.push(Math.round(Math.random(0,1)*10)%2);
			// Applying mirror image (the way to copy is slightly different if we have an odd or even width)
			line = (width%2) ? line.concat(line.slice(0).reverse().slice(1)) : line.concat(line.slice(0).reverse());
			value = value.concat(line);
		}

		return value.join('');
	}

	/**
	 *	spaceinvader
	 *	Will light the pixels on and off
	 **/
	$.fn.spaceinvader = function(value) {
		return $(this).each(function() {
			var that = $(this),
				pixelList = that.find('.pixel'),
				height = pixelList.filter('.pixel-first').length,
				width = pixelList.length / height,
				randomSpace, line, column;

			// Getting the binary space representation
			var randomSpace = random(width, height);

			pixelList.removeClass('pixel-on');
			// We will now light some pixels up
			for (var i = 0, max = height*width;i!=max;i++) {
				if (randomSpace.substring(i,i+1)=="0") continue;
				line = Math.floor(i/width);
				column = i%width;
				pixelList.filter('.pixel-'+line+'-'+column).addClass('pixel-on');
			}

			// We save the value of the space invader in the object
			that.data('spaceinvader.value', randomSpace);
			return this;
		});
	}

	// Just for fun, a list of good-looking spaceys
	$.fn.spaceinvader.listing = [
		'0000001110101010010001110',
		'1111100100010101000101110',
		'0000001110101010111010001',
		'1010101110110111010100000',
		'1101100100011100000001110',
		'0101010001010101111110101',
		'1000101110100011010101010',
		'0101010101111111101101010',
		'1111111111101010101010101',
		'1111110101011101000100100',
		'0111010101110111101110001',
		'1000101010101011111110101',
		'0111010001110110101011111',
		'1101100100100011111100100',
		'1101111111101010111001010',
		'1111110101011100010001110',
		'1101101110101011000101010',
		'1111101110101010111000100',
		'0000010001111111010101110',
		'0101011111100010101000000',
		'0000010001011101010110101',
		'1101101110100011101100100',
		'0111011011111111000101010',
		'0101010001111110101010101',
		'0101000000000001000101010',
		'1111110101110110000000100',
		'0111010101111110111011111',
		'0101011111101011010111011',
		'0000001110101011101110001',
		'0111010101010100101010001',
		'1101101110101010111000000',
		'0101010101111111010101110',
		'0101010101111110111011011'
	];

})(jQuery);
