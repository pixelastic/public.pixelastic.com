/**
 *	formHabtm
 *	Adds controls on the habtm multiple select list to ease the way one can add or remove habtm relationships
 *
 *	It will hide the multiple select and replace it with an autocomplete text input field.
 *	Each related model will be set as a link with a 'remove' link.
 *	Everytime an item is selected in the autocomplete list, a link is added (and the corresponding select value is secretly checked)
 *	If the entered text does not exists, a custom method is fired. Default is making a request to add the defined text
 **/
(function($) {

	$.fn.formHabtm = function(settings) {
		// Options
		var options = $.extend({}, $.fn.formHabtm.defaults, settings);

		/**
		 *	selectItem
		 *	Will select an item
		 *	@argument	int		itemId		The id of the item to select
		 *	@argument	element	select		The select element
		 **/
		function selectItem(itemId, select) {
			var val = select.val() || [];

			// Already selected
			if (val.indexOf(itemId)!=-1) return false;

			// Saving in select
			val.push(itemId);
			select.val(val);

			// We create a new item in the list
			addItemToList(itemId, select);

			return true;
		}

		/**
		 *	unselectItem
		 *	Will deselect a given item
		 *	@argument	int		itemId		The id of the item to select
		 *	@argument	element	select		The select element
		 **/
		function unselectItem(itemId, select) {
			var val = select.val() || []
			// Delete from the select
			val.splice(val.indexOf(itemId), 1);
			select.val(val);
			// Delete from the list
			select.data('itemsUl').find('#item-'+select.data('modelName')+'-'+itemId).remove();
		}

		/**
		 *	addItemToList
		 *	Adds an item to the item list
		 *	@argument	int		itemId		The id of the item to select
		 *	@argument	element	select		The select element
		 **/
		function addItemToList(itemId, select) {
			var item = select.find('option[value='+itemId+']'),
				modelName = select.data('modelName');

			$('<li id="item-'+modelName+'-'+itemId+'"><span class="icon icon'+modelName+'_delete"></span>'+item.html()+'</li>')
				.appendTo(select.data('itemsUl'));
		}

		/**
		 *	onSelectInput
		 *	Fired when the text is selected
		 **/
		function onSelectInput(event) {
			// Only if pressing ENTER
			if (event.keyCode!=13) return true;
			if (this.value=='') return true;

			var inputText = $(this),
				select = inputText.closest('div.habtm').find('select'),
				value = this.value,
				source = inputText.autocomplete('option', 'source'),
				id = null;

			// Hiding the autocomplete
			inputText.autocomplete('close');

			// Getting the id of a value already in the source
			for(var i in source) {
				if (source[i].value.toLowerCase()!=value.toLowerCase()) continue;
				id = source[i].id;
				break;
			}

			// Already in source, we just add it
			if (id) {
				selectItem(id, select);
				inputText.val('');
				event.preventDefault();
				return false;
			}

			// Not in source, so we need to request the server to add a new one
			$.ajax({
				url: 'http://'+document.domain+'/admin/editors/habtm.json',
				data: {
					'value' : this.value,
					'model' : select.data('modelName')
				},
				success: function(data) {
					if (data.error) return false;
					// We add this entry to the select
					select.append('<option value="'+data.id+'">'+data.value+'</option>');
					// We update the autocomplete source
					source.push({'value' : data.value, 'id' : data.id});
					inputText.autocomplete('option', 'source', source);
					// And we select this item
					inputText.val('');
					selectItem(data.id, select);
					return true;
				}
			});

			inputText.val('');
			event.preventDefault();
			return false;
		}

		/**
		 *	onClickList
		 *	Fired when clicking on the item list
		 **/
		function onClickList(event) {
			var li = $(event.target).closest('li'),
				idInfos = li.attr('id').split('-');
			// Unselect item
			unselectItem(idInfos[2], $('#'+idInfos[1]+idInfos[1]));
			return false;
		}

		/**
		 *	each
		 *	Adding the behavior to each multiple habtm select in the page
		 **/
		return this.each(function() {
			var select = $(this),
				val = select.val(),
				source = [];

			// Getting the autocomplete list
			select.find('option').each(function() {
				source.push({'value' :  this.innerHTML, 'id': this.value})
			});

			// Adding the input and links before the select
			var textInput = $('<input type="text" />')
				// Autocomplete
				.autocomplete({ source: source })
				// Adding item when pressing enter
				.bind('keypress.formHabtm', onSelectInput);

			var itemsUl = $('<ul class="items"></ul>')
				// removing item
				.bind('click.formHabtm', onClickList)

			// Save for later use
			var selectId = select.attr('id');
			select
				.data('textInput', textInput)
				.data('itemsUl', itemsUl)
				.data('modelName', selectId.substr(0,selectId.length/2));

			// Adding items to the list
			for(var i in val) {
				addItemToList(val[i], select);
			}

			// Insert in DOM
			select.before(textInput).before(itemsUl)


		});
	}

	// Defaults
	$.fn.formHabtm.defaults = {

	}



})(jQuery);
