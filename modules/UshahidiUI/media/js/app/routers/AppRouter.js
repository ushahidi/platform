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
				'messages/settings' : 'messageSettingsMain',
				// FIXME: temp route for sms hard coding
				'messages/settings/sms' : 'dataProvidersConfigSMS',
				'messages/settings/:provider' : 'dataProvidersConfig',
				'sets' : 'sets',
				'sets/:id' : 'setDetail',
				'users' : 'users',
				'tags' : 'tags',

				'apiexplorer' : 'apiExplorer',
				'settings/forms' : 'forms',
				// 'settings/forms/new' : 'formNew',
				'settings/forms/:provider' : 'formEdit',
				//'login' : 'login',
				//'register' : 'register',

				// *path needs to be last
				'*path' : 'index'
			}
		});
	});
