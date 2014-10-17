/**
 * List View
 *
 * @module     ListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'jquery', 'util/notify', 'drop',
		'views/EmptyView'
	],
	function( App, Marionette, _, $, notify, Drop,
		EmptyView
	)
	{
		return Marionette.CompositeView.extend(
		{
			modelName: 'resource',

			childViewContainer: '.js-list-view-list',

			childViewOptions: function () {
				return {
					modelName: this.modelName
				};
			},

			childEvents : {
				'resource:edit' : function(view, args) {
					this.trigger('resource:edit', args.model);
				}
			},

			emptyViewOptions:
			{
				emptyMessage: 'No resources found.',
			},

			emptyView: EmptyView,

			events:
			{
				'click .js-bulk-delete' : 'bulkDelete'
			},

			triggers:
			{
				'click .js-create' : 'resource:create'
			},

			collectionEvents :
			{
				request: 'showLoading',
				sync : 'hideLoading'
			},

			behaviors: {
				PageableView: {},
				SelectableList: {}
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					pageSizes: this.collection.pageSizes,
					sortKeys: this.collection.sortKeys
				});

				return data;
			},

			bulkDelete : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				if (selected.length === 0)
				{
					return;
				}

				notify.bulkDestroy(selected, this.modelName);
			},

			showLoading : function()
			{
				// Hide the ul li
				this.$('.js-list-view-list').addClass('visually-hidden');

				// Show the loading text
				this.$('.js-loading').removeClass('visually-hidden');
			},

			hideLoading : function()
			{
				// Hide the loading text
				this.$('.js-loading').addClass('visually-hidden');

				// Show the ul li
				this.$('.js-list-view-list').removeClass('visually-hidden');
			},

			onDomRefresh: function()
			{
				var that = this;

				this.actionsDrop = new Drop({
					target: this.$('.js-actions-drop')[0],
					content: this.$('.js-actions-drop-content')[0],
					classes: 'drop-theme-arrows',
					position: 'bottom right',
					openOn: 'click',
					remove: true
				});

				this.actionsDrop.on('open', function()
				{
					var $dropContent = $(this.content);
					$dropContent.off('.actions-drop')
						.on('click.actions-drop', '.js-select-all .select-text', function()
						{
							that.selectAll();
							that.actionsDrop.close();
							$dropContent.find('.select-text').addClass('visually-hidden');
							$dropContent.find('.unselect-text').removeClass('visually-hidden');
						})
						.on('click.actions-drop', '.js-select-all .unselect-text', function()
						{
							that.unselectAll();
							that.actionsDrop.close();
							$dropContent.find('.select-text').removeClass('visually-hidden');
							$dropContent.find('.unselect-text').addClass('visually-hidden');
						})
						.on('click.actions-drop', '.js-create', function(e)
						{
							that.actionsDrop.close();
							that.showCreate.call(that, e);
						})
						.on('click.actions-drop', '.js-bulk-delete', function(e)
						{
							that.actionsDrop.close();
							that.bulkDelete.call(that, e.originalEvent);
						})
						;
				});
			}
		});
	});
