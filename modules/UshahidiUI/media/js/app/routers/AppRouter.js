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
				'views/full' : 'viewsFull',
				'views/list' : 'viewsList',
				'views/map' : 'viewsMap',
				'posts' : 'postsPublished',
				'posts/all' : 'postsAll',
				'posts/unpublished' : 'postsUnpublished',
				'posts/create' : 'postCreate',
				'posts/:id' : 'postDetail',
				'messages' : 'messages',
				'messages/list/:view' : 'messages',
				'sets' : 'sets',
				'sets/:id' : 'setDetail',
				'users' : 'users',
				'tags' : 'tags',
				'settings/site' : 'settings',
				//'login' : 'login',
				//'register' : 'register',
				'*path' : 'index'
			}
		});
	});
