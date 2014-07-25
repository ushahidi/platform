/**
 * Settings Application
 *
 * @module     SettingsApp
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'collections/SetCollection', 'sets/SetListView', 'sets/SetDetailView'],
	function(App, Marionette, SetCollection, SetListView, SetDetailView)
	{
		var SetsAPI = {
			/**
			 * Show sets listing view
			 */
			showSets : function ()
			{
				App.vent.trigger('page:change', 'sets');
				App.layout.mainRegion.show(new SetListView({
					collection : sets
				}));
			},
			/**
			 * Show set detail view
			 */
			showSetDetail : function(/* id */)
			{
				App.vent.trigger('page:change', 'sets/:id');
				App.layout.mainRegion.show(new SetDetailView());
			},
		},
		sets = new SetCollection();

		sets.fetch();

		App.addInitializer(function(){
			new Marionette.AppRouter({
				appRoutes : {
					'sets' : 'showSets',
					'sets/:id' : 'showSetDetail',
				},
				controller : SetsAPI
			});
		});
	});
