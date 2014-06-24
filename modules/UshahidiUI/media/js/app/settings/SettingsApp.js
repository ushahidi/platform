/**
 * Settings Application
 *
 * @module     SettingsApp
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'settings/SettingsView'],
	function(App, Marionette, SettingsView)
	{
		var SettingsAPI = {
			/**
			 * Show site settings page
			 */
			showSiteSettings : function()
			{
				App.vent.trigger('page:change', 'settings');
				App.layout.mainRegion.show(new SettingsView());
			},
			/**
			 * Show map settings page
			 */
			showMapSettings : function()
			{
				if (!App.feature('map_settings'))
				{
					// @todo figure out what happens with multiple routers.
					App.appRouter.navigate('', { trigger : true });
					return;
				}

				require(['settings/MapSettingsView'], function(MapSettingsView)
				{
					App.vent.trigger('page:change', 'mapSettings');
					App.layout.mainRegion.show(new MapSettingsView());
				});
			},
		};

		App.addInitializer(function(){
			new Marionette.AppRouter({
				appRoutes: {
					'settings/site' : 'showSiteSettings',
					'settings/map' : 'showMapSettings',
				},
				controller: SettingsAPI
			});
		});
	});
