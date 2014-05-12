/**
 * Workspace Panel View
 *
 * @module     WorkspacePanelView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'marionette', 'handlebars', 'App', 'text!templates/WorkspacePanel.html'],
	function(_, Marionette, Handlebars, App, template)
	{
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			modelEvents : {
				'sync' : 'render'
			},
			events : {
				'click .js-title' : 'toggleSection',
				'click .js-logout' : 'logout',
				'click .js-edit-profile' : 'editUser'
			},
			initialize : function ()
			{
				App.vent.on('page:change', this.selectMenuItem, this);
			},
			serializeData: function()
			{
				var data = {};

				// TODO: don't assume the user is loaded
				data.user = this.model.toJSON();

				// TODO: add real info, probably need to fetch this data from
				// somewhere else, or even break up this view.
				// also note that formatting these values need to be i18n compatible.
				data.stats = {
					'posts' : _.random(0, 10000),
					'users' : _.random(1, 800),
					'views' : _.random(100, 100000000)
				};
				data.posts = {
					'published'   : _.random(1,500),
					'unpublished' : _.random(1,500),
					'review'      : 0,
					'total'       : 0
				};
				data.posts.total = data.posts.published + data.posts.unpublished;
				data.posts.review = _.random(0, data.posts.total);
				data.messages = {
					'email'    : _.random(1, 1000),
					'sms'      : _.random(1, 1000),
					'unread'   : 0,
					'archived' : 0,
					'total'    : 0
				};
				data.messages.total = data.messages.email + data.messages.sms;
				data.messages.unread = _.random(0, data.messages.total);
				data.messages.archived = data.messages.total - data.messages.unread;

				return data;
			},
			toggleSection : function(e)
			{
				var $el = this.$(e.currentTarget);
				$el.nextAll('.js-content').toggleClass('active');
				e.preventDefault();
			},
			selectMenuItem : function(page)
			{
				var target = this.$('.workspace-menu li[data-page="'+page+'"]');

				if (target.length > 0)
				{
					this.$('.workspace-menu li').removeClass('active');
					target.addClass('active');
				}

				// Close workspace panel
				App.vent.trigger('workspace:toggle', true);
			},
			logout : function(e)
			{
				e.preventDefault();
				App.vent.trigger('logout');
				App.vent.trigger('workspace:toggle', true);
			},
			editUser : function(e) {
				e.preventDefault();
				App.vent.trigger('user:edit', this.model);
				App.vent.trigger('workspace:toggle', true);
			}
		});
	});
