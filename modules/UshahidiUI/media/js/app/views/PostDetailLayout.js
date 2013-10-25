/**
 * Post Detail Layout
 *
 * @module     PostDetailLayout
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars', 'text!templates/PostDetailLayout.html'],
	function(App, Marionette, Handlebars, template) {
		return Marionette.Layout.extend(
		{
			className: 'layout-posts',
			template : Handlebars.compile(template),
			regions : {
				mapRegion : '#mapRegion',
				postDetailRegion : '#post-details',
				relatedPostsRegion : '#related-posts'
			}
		});
	});