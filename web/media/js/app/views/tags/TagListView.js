/**
 * User List View
 *
 * @module     UserListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'alertify',
		'views/tags/TagListItemView',
		'views/EmptyView',
		'hbs!templates/tags/TagList',
		'mixin/PageableViewBehavior'
	],
	function( App, Marionette, _, alertify,
		TagListItemView,
		EmptyView,
		template,
		PageableViewBehavior
	)
	{
		return Marionette.CompositeView.extend(
		{
			template: template,
			modelName: 'tags',

			initialize: function ()
			{
				// Bind select/unselect events from childviews
				this.on('childview:select', this.showHideBulkActions, this);
				this.on('childview:unselect', this.showHideBulkActions, this);
			},

			childView: TagListItemView,

			childViewContainer: '.list-view-tag-list',

			emptyViewOptions:
			{
				emptyMessage: 'No tags found.',
			},

			emptyView: EmptyView,

			events:
			{
				'click .js-tag-create' : 'showCreateTag',
				'click .js-tag-bulk-delete' : 'bulkDelete',
				'click .js-select-all' : 'toggleSelectAll'
			},

			collectionEvents :
			{
				reset : 'unselectAll',
				request : 'unselectAll',
			},

			behaviors: {
				PageableViewBehavior: {
					behaviorClass : PageableViewBehavior,
					modelName : 'tags'
				}
			},

			getSelected : function ()
			{
				return this.children.filter('selected');
			},

			/**
			 * Show / Hide bulk actions toolbar when users are selected
			 */
			showHideBulkActions : function ()
			{
				var selected = this.getSelected();
				this.$('.js-bulk-action').toggleClass('disabled', selected.length === 0);
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					pageSizes: this.collection.pageSizes,
					sortKeys: this.collection.sortKeys,
					// @todo move to serializeModel
					modelName : this.modelName
				});

				return data;
			},

			showCreateTag : function (e)
			{
				e.preventDefault();
				App.vent.trigger('tag:create', this.model);
			},

			bulkDelete : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				if (selected.length === 0)
				{
					return;
				}

				alertify.confirm('Are you sure you want to delete ' + selected.length + ' tags?', function(e)
				{
					if (e)
					{
						_.each(selected, function(item) {
						var model = item.model;
						model
								.destroy({wait : true})
								.done(function()
								{
									alertify.success('Tag has been deleted');
									// Trigger a fetch. This is to remove the model from the listing and load another
									App.Collections.Tags.fetch();
								})
								.fail(function ()
								{
									alertify.error('Unable to delete tag, please try again');
							});
						} );
					}
					else
					{
						alertify.log('Delete cancelled');
					}
				});
			},

			/**
			 * Select all users
			 */
			toggleSelectAll : function (e, select)
			{
				_.result(e, 'preventDefault');

				this.selectAllValue = (typeof select !== 'undefined') ? select : ! this.selectAllValue;

				if (this.selectAllValue)
				{
					this.children.each(function (child) { _.result(child, 'select'); });
				}
				else
				{
					this.children.each(function (child) { _.result(child, 'unselect'); });
				}
				this.$('.select-text').toggleClass('visually-hidden', this.selectAllValue);
				this.$('.unselect-text').toggleClass('visually-hidden', ! this.selectAllValue);
			},

			selectAll : function(e)
			{
				this.toggleSelectAll(e, true);
			},

			unselectAll : function (e)
			{
				this.toggleSelectAll(e, false);
			},
		});
	});
