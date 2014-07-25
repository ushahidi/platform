/**
 * Ushahidi RequireJS Main file
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// Includes Desktop Specific JavaScript files here (or inside of your Desktop router)
require(['Config'], function () {
	require(['App', 'routers/AppRouter', 'controllers/Controller', 'jquery', 'ddt',
		'settings/SettingsApp',
		'messages/MessagesApp',
		'sets/SetsApp'
	],
	function(App, AppRouter, Controller, $)
	{
		App.appRouter = new AppRouter(
		{
			controller : new Controller()
		});

		App.start();
		window.App = App;
		$(document).on('click.app', '.js-stub', function(e)
		{
			e.preventDefault();
			var alertify = require('alertify');
			alertify.log('This action has not been implemented yet.');
		});
	});
});
