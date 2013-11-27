/**
 * Ushahidi Application Router
 *
 * @module     AppRouter
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette'],
	function(Marionette) {
		return Marionette.AppRouter.extend(
		{
			appRoutes :
			{
				'' : 'index',
				'views/list' : 'viewsList',
				'views/map' : 'viewsMap',
				'posts/create' : 'postCreate',
				'posts/:id' : 'postDetail',
				'sets' : 'sets',
				'sets/:id' : 'setDetail',
				'posts?*querystring' : 'searchPosts',
				'*path' : 'index'
			}
		});
	});
