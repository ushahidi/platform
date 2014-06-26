/**
 * Workspace Panel View
 *
 * @module     WorkspacePanelView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'marionette', 'App', 'modules/config', 'modules/textifyNumber', 'hbs!templates/WorkspacePanel'],
	function(_, Marionette, App, config, textifyNumber, template)
	{
		return Marionette.ItemView.extend(
		{
			template : template,
			modelEvents : {
				'sync' : 'render'
			},
			events : {
				'click .js-title' : 'toggleSection',
				'click .js-logout' : 'confirmLogout',
				'click .js-edit-profile' : 'editUser'
			},

			totals: {
				stats: {},
				posts: {}
			},

			initialize : function ()
			{
				App.vent.on('page:change', this.selectMenuItem, this);
				App.vent.on('config:change', this.render, this);

				var that = this;
				App.oauth.ajax({
						type: 'GET',
						dataType: 'json',
						url: config.get('apiurl') + '/stats',
					})
					.done(function(data) {
						that.totals = data;
						that.render();
					});
			},
			serializeData: function()
			{
				var data = {
						stats: {},
						posts: {}
					};

				// Add loaded totals into data, with textification
				_.each(this.totals, function(stats, group) {
					_.each(stats, function(value, key) {
						data[group][key] = textifyNumber(value);
					});
				});

				// TODO: don't assume the user is loaded
				data.user = this.model.toJSON();

				// TODO: add real info, probably need to fetch this data from
				// somewhere else, or even break up this view.
				// also note that formatting these values need to be i18n compatible.
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
				$el.nextAll('.js-content').addBack().toggleClass('active');
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
			confirmLogout : function(e)
			{
				if (!window.confirm('Are you sure you want to log out?')) {
					e.preventDefault();
				}
			},
			editUser : function(e) {
				e.preventDefault();
				App.vent.trigger('user:edit', this.model);
				App.vent.trigger('workspace:toggle', true);
			}
		});
	});
