/**
 * Post Detail Layout
 *
 * @module     PostDetailLayout
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'hbs!messages/settings/ProviderLayout'],
	function(App, Marionette, template) {
		return Marionette.Layout.extend(
		{
			className: 'layout-posts',
			template : template,
			regions : {
				tabNavList : '.js-tab-nav-list',
				main : '.js-data-provider-main'
			}
		});
	});