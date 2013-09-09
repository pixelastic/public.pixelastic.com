/**
 *	init
 *	Loading Javascript methods
 **/
$(function() {
	// Space invader in the header
	(function() {
		var spaceinvader = $('#spaceinvader').spaceinvader().click(function() {
			$(this).spaceinvader();
			return false;
		});
		// Regenerate every 3 secs
		setInterval(function() { spaceinvader.spaceinvader()}, 3000);
		// Move every 5 seconds
		setInterval(function() {
			spaceinvader.animate({
				'right':Math.round(Math.random()*600),
				'opacity': 0.5 - Math.random()*0.5
			}, {
				'duration': 4000,
				'easing': 'swing'
			});
		}, 4000);
	})();

	// Lightbox on images
	$('a.lightbox').live('click.lightbox', function(e) {
		e.preventDefault();
		$.slimbox(this.href, this.title);
	});

	// Konami code secret !
	(function() {
		if (!window.addEventListener) return;

        var kkeys = [], konami = "38,38,40,40,37,39,37,39,66,65";
        window.addEventListener("keydown", function(e) {
            kkeys.push(e.keyCode);
            if ( kkeys.toString().indexOf( konami )>=0) {
				$('img[src^=http://www.gravatar.com/avatar/]').each(function() {
					$(this).attr('src', this.src.replace('www.gravatar.com', 'unicornify.appspot.com'));
				});
				kkeys = [];
			}
        }, true);
	})();

});
