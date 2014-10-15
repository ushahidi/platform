/**
 * User List View
 *
 * @module     UserListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'jquery', 'alertify', 'drop',
		'views/tags/TagListItemView',
		'views/EmptyView',
		'hbs!templates/tags/TagList'
	],
	function( App, Marionette, _, $, notify, alertify, Drop,
		TagListItemView,
		EmptyView,
		template
	)
	{
		return Marionette.CompositeView.extend(
		{
			template: template,
			modelName: 'tags',

			onDomRefresh: function()
			{
				var that = this;

				this.actionsDrop = new Drop({
					target: this.$('.js-tag-actions-drop')[0],
					content: this.$('.js-tag-actions-drop-content')[0],
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
						.on('click.actions-drop', '.js-tag-create', function(e)
						{
							that.actionsDrop.close();
							that.showCreateTag.call(that, e);
						})
						.on('click.actions-drop', '.js-tag-bulk-delete', function(e)
						{
							that.actionsDrop.close();
							that.bulkDelete.call(that, e.originalEvent);
						})
						;
				});
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
				'click .js-tag-bulk-delete' : 'bulkDelete'
			},

			behaviors: {
				PageableView: {
					modelName : 'tags'
				},
				SelectableList: {}
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

				notify.bulkDestroy(selected, 'tag');
			}
		});
	});
