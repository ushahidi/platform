/**
 * Post Item Parent View
 *
 * @module     PostItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'util/notify'],
	function(App, Marionette, _, notify)
	{
		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend(
		{

			events: {
				'click .js-post-delete': 'deletePost',
				'click .js-post-edit' : 'showEditPost',
				'click .js-post-set' : 'showAddToSet',
				'click .js-post-publish' : 'publishPost',
				'click .js-post-unpublish' : 'unpublishPost'
			},

			modelEvents: {
				'sync': 'render'
			},

			deletePost: function(e)
			{
				e.preventDefault();
				notify.destroy(this.model, 'post');
			},

			publishPost: function(e)
			{
				e.preventDefault();

				this.model.set('status', 'published');

				notify.save(this.model, 'post', 'publish');
			},

			unpublishPost: function(e)
			{
				e.preventDefault();

				this.model.set('status', 'draft');

				notify.save(this.model, 'post', 'unpublish');
			},

			serializeData: function()
			{
				// @todo move to serializeModel
				var data = _.extend(this.model.toJSON(), {
					isPublished : this.model.isPublished(),
					tags : this.model.getTags(),
					user : this.model.user ? this.model.user.toJSON() : null,
					location : this.model.getLocation(),
					hasNext : this.model.hasNext(),
					hasPrev : this.model.hasPrev()
				});
				return data;
			},
			showEditPost : function ()
			{
				App.vent.trigger('post:edit', this.model);
			},
			showAddToSet : function (e)
			{
				e.preventDefault();
				App.vent.trigger('post:set', this.model);
			}
		});
	});
