/**
 * Ushahidi RequireJS Main file
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// Includes Desktop Specific JavaScript files here (or inside of your Desktop router)
require(['Config'], function () {
	require(['App', 'routers/AppRouter', 'controllers/Controller', 'jquery', 'i18next', 'ddt',
		'settings/SettingsApp',
		'messages/MessagesApp',
		'sets/SetsApp',
		'form-manager/FormManagerApp'
	],
	function(App, AppRouter, Controller, $, i18n)
	{
		window.App = App;
		i18n.init({
				fallbackLng: 'en',
				resGetPath: window.config.baseurl + 'media/locales/__lng__/__ns__.json',
				escapeInterpolation: true
			}, function ()
			{
				App.appRouter = new AppRouter(
				{
					controller : new Controller()
				});

				// Delay App.start till App.user is loaded or just continue if we're not logged in
				$.when(App.user.loaded || true).done(function() {
					App.start();
				});
			}
		);

		$(document).on('click.app', '.js-stub', function(e)
		{
			e.preventDefault();
			var alertify = require('alertify');
			alertify.log('This action has not been implemented yet.');
		});
	});
});
