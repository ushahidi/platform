/**
 * Settings Application
 *
 * @module     SettingsApp
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'modules/config',
		'settings/SettingsView',
		'settings/MapSettingsView',
	],
	function(App, Marionette, config,
		SettingsView,
		MapSettingsView)
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
				var mapSettingsView = new MapSettingsView({
					model : config.get('map'),
					postCollection : App.Collections.Posts
				});

				App.vent.trigger('page:change', 'mapSettings');
				App.layout.mainRegion.show(mapSettingsView);
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
