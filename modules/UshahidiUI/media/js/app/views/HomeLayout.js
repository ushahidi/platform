/**
 * Home Layout
 *
 * @module     HomeLayout
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars', 'text!templates/HomeLayout.html'],
	function(App, Marionette, Handlebars, template)
	{
		return Marionette.Layout.extend(
		{
			className: 'layout-home',
			template : Handlebars.compile(template),
			regions : {
				mapRegion : '#map-region',
				searchRegion : '#search-bar',
				contentRegion : '#post-list-view'
			}
		});
	});