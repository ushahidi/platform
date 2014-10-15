define(['marionette', 'underscore'], function (Marionette, _) {
	var SelectableListItemBehaviour = Marionette.Behavior.extend({
		initialize: function()
		{
			// Value to track if checkbox for this item has been selected
			this.view.selected = false;

			this.view.select = _.bind(this.select, this);
			this.view.unselect = _.bind(this.unselect, this);
		},
		ui : {
			selectInput : '.js-select-input',
		},
		events : {
			'change @ui.selectInput' : 'updatedSelected'
		},
		/**
		 * Select this item (for bulk actions)
		 */
		select : function ()
		{
			var selected = this.view.selected = true;
			this.ui.selectInput.prop('checked', true)
				.parent()
				.addClass('selected-button', selected);
			this.view.trigger('select');
		},
		/**
		 * Unselect this item (for bulk actions)
		 */
		unselect : function ()
		{
			var selected = this.view.selected = false;
			this.ui.selectInput.prop('checked', false)
				.parent()
				.removeClass('selected-button', selected);
			this.view.trigger('unselect');
		},
		/**
		 * Updated 'selected' value from DOM
		 */
		updatedSelected : function ()
		{
			var selected = this.view.selected = this.ui.selectInput.is(':checked');
			this.view.trigger(selected ? 'select' : 'unselect');

			this.ui.selectInput.parent()
				.toggleClass('selected-button', selected);
		},
	});

	return SelectableListItemBehaviour;
});