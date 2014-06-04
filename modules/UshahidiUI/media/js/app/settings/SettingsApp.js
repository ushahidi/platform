/**
 * Settings Application
 *
 * @module     SettingsApp
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'settings/SettingsView'],
	function(Marionette, SettingsView)
	{
		// Require app here, we can't do this in the define without creating a circular dependency
		var App = require('App'),

		SettingsRouter = Marionette.AppRouter.extend({
			appRoutes: {
				'settings/site' : 'showSiteSettings',
			}
		}),

		SettingsAPI = {
			showSiteSettings : function()
			{
				var that = this;
				App.vent.trigger('page:change', 'settings');
				App.layout.mainRegion.show(new SettingsView());
			},
		};

		App.addInitializer(function(){
			new SettingsRouter({
				controller: SettingsAPI
			});
		});
	});
