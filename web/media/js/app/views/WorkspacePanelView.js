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

			counts: {
				messages: {},
				posts: {},
				tags: {},
				users: {}
			},

			initialize : function ()
			{
				App.vent.on('page:change', this.selectMenuItem, this);
				App.vent.on('config:change', this.render, this);

				var that = this;
				App.oauth.ajax({
						type: 'GET',
						dataType: 'json',
						url: config.get('apiurl') + 'stats',
					})
					.done(function(data) {
						that.counts = data;
						that.render();
					});
			},
			serializeData: function()
			{
				var data = _.clone(this.counts);

				// Add loaded totals into data, with textification
				_.each(data, function(stats, group) {
					// this can probably be optimized in some way by using _.map
					// or _.invoke, but not quite sure how...
					_.each(stats, function(value, key) {
						data[group][key] = textifyNumber(value);
					});
				});

				// TODO: don't assume the user is loaded
				data.user = this.model.toJSON();

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
