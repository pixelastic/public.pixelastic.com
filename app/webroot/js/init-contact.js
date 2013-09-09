/**
 *	init-contact
 *	Javascript of the contact page
 **/
$(function() {
	var form = $('#PixelasticContactAddForm');
	if (!form.length) return false;

	// The project fieldset should be visible only when the checkbox is checked
	function toggleProjectFieldset(checked) {
		if (checked) {
			$('#fieldsetProject').show();
		} else {
			$('#fieldsetProject').hide();
		}
	}
	toggleProjectFieldset(
		$('#OptionsIsProject').change(function() {
			toggleProjectFieldset($(this).attr('checked'));
		}).attr('checked')
	);

	// We will convert into sliders the select dropdowns
	function convertSelectIntoSlider(input) {
		var select = input.find('select'),
			selectedIndex = select[0].selectedIndex,
			options = select.find('option'),
			help = input.find('.help'),
			helpText = help.find('span.tooltip').html(),
			values = [];

		// We get the values as an array
		options.each(function() {
			values.push({value: this.value, label:this.innerHTML });
		});
		// Hiding the initial select
		select.hide();
		help.hide();
		// Adding a slider after the help
		var slider = $('<div></div>').slider({
			min: 0,
			max: values.length-1,
			value: selectedIndex,
			slide: function(event, ui) {
				// Updating title and label
				$(this).find('.ui-slider-handle').attr('title', values[ui.value].value);
				sliderInfo.html(values[ui.value].label);
				// Updating the corresponding select
				select.val(values[ui.value].value);
			}

		});
		// Adding a slider info
		var sliderInfo = $('<div class="sliderInfo">'+helpText+'</div>');

		// We prefill the handler title
		slider.find('.ui-slider-handle').attr('title', values[selectedIndex].value);

		// And insert them in the DOM
		sliderInfo.insertAfter(slider.insertAfter(help));
	}
	convertSelectIntoSlider($('#PixelasticContactTimeframe').closest('.input'));
	convertSelectIntoSlider($('#PixelasticContactBudget').closest('.input'));


});
