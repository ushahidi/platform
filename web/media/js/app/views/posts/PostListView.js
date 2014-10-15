/**
 * Post List View
 *
 * @module     PostListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'modules/config', 'marionette', 'handlebars','underscore', 'alertify', 'util/notify',
		'views/posts/PostListItemView',
		'hbs!templates/posts/PostList',
		'views/EmptyView'
	],
	function( App, config, Marionette, Handlebars, _, alertify, notify,
		PostListItemView,
		template,
		EmptyView
	)
	{
		return Marionette.CompositeView.extend(
		{
			template: template,
			modelName: 'posts',

			childView: PostListItemView,

			emptyViewOptions:
			{
				emptyMessage: 'No posts found.',
			},

			emptyView: EmptyView,

			childViewContainer: '.list-view-post-list',

			events:
			{
				'click .js-post-bulk-publish' : 'bulkPublish',
				'click .js-post-bulk-unpublish' : 'bulkUnpublish',
				'click .js-post-bulk-delete' : 'bulkDelete',
				'click .js-post-bulk-export' : 'exportPostCsv'
			},

			behaviors: {
				PageableView: {
					modelName: 'posts',
				},
				SelectableList: {}
			},

			/**
			 * Bulk publish selected posts
			 */
			bulkPublish : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				_.each(selected, function(item) {
					var model = item.model;
					model.set('status', 'published');
					notify.save(model, 'post', 'publish');
				} );
			},

			/**
			 * Bulk unpublish selected posts
			 */
			bulkUnpublish : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				_.each(selected, function(item) {
					var model = item.model;
					model.set('status', 'draft');
					notify.save(model, 'post', 'unpublish');
				} );
			},

			/**
			 * Bulk delete selected posts
			 */
			bulkDelete : function (e)
			{
				e.preventDefault();

				var selected = this.getSelected();

				notify.bulkDestroy(selected, 'post');
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					pageSizes: this.collection.pageSizes,
					sortKeys: this.collection.sortKeys,
					modelName : this.modelName
				});

				return data;
			},
			exportPostCsv : function(e) {
				e.preventDefault();
				App.oauth.ajax({
					url : config.get('apiurl') + 'posts/export',
					dataType : 'json'
				}).done(function(data) {
					if (data)
					{
						var download = data.total_count + ' posts exported: <a href="' + data.link + '" download> Click to download as CSV file</a>';
						alertify.confirm( download);
					}
				}).fail(function()
				{
					alertify.error('Unable to export posts as CSV');
				});

				// Close workspace
				App.vent.trigger('workspace:toggle', true);
			}
		});
	});
