define(['marionette', 'underscore', 'jquery'], function (Marionette, _, $) {
	var SelectableListBehaviour = Marionette.Behavior.extend({
		initialize: function()
		{
			// Value to track if checkbox for select all has been selected
			this.selectAllValue = false;

			this.on('childview:select', this.showHideBulkActions, this);
			this.on('childview:unselect', this.showHideBulkActions, this);

			this.view.getSelected = _.bind(this.getSelected, this);
			this.view.selectAll = _.bind(this.selectAll, this);
			this.view.unselectAll = _.bind(this.unselectAll, this);
		},
		events : {
			'click .js-select-all' : 'toggleSelectAll'
		},
		collectionEvents :
		{
			reset : 'unselectAll',
			request : 'unselectAll'
		},
		toggleSelectAll : function (e, select)
		{
			e && e.preventDefault();

			var selectAllValue = this.selectAllValue = (typeof select !== 'undefined') ? select : ! this.selectAllValue;

			this.view.children.each(function (child) {
				// Call select/unselect if set
				// if the view is an EmptyView, _.result() does nothing and returns undefined.
				_.result(child, selectAllValue ? 'select' : 'unselect');
			});

			this.$('.select-text').toggleClass('visually-hidden', selectAllValue);
			this.$('.unselect-text').toggleClass('visually-hidden', ! selectAllValue);
		},
		/**
		 * Select all
		 */
		selectAll : function ()
		{
			this.toggleSelectAll(false, true);
		},
		/**
		 * Select all
		 */
		unselectAll : function ()
		{
			this.toggleSelectAll(false, false);
		},
		/**
		 * Get select child views
		 * This gets assigned to view.getSelected so will run in that scope
		 */
		getSelected : function ()
		{
			return this.view.children.filter('selected');
		},
		/**
		 * Enable/Disable bulk actions
		 */
		showHideBulkActions : function ()
		{
			var selected = this.getSelected();

			// Toggle bulk action buttons
			this.$('.js-bulk-action')
				.add('.js-bulk-actions')
				.toggleClass('disabled', selected.length === 0);

			// If present, toggle buttons in actionsDrop too
			if (this.view.actionsDrop)
			{
				$(this.view.actionsDrop.content).find('.js-bulk-action')
					.toggleClass('disabled', selected.length === 0);
			}
		},
	});

	return SelectableListBehaviour;
});