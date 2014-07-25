/**
 * Messages Application
 *
 * @module     MessagesApp
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'App'],
	function(Marionette, App)
	{
		var MessagesAPI = {
			/**
			 * Show messages list
			 */
			messages : function (view)
			{
				require(['messages/list/MessageListController'],
					function(MessageListController)
				{
					MessageListController.listMessages(view);
				});
			},
			/**
			 * Show message providers list
			 */
			messageSettingsMain : function()
			{
				require(['messages/settings/MessageSettingsController'],
					function(MessageSettingsController)
				{
					MessageSettingsController.listProviders();
				});
			},
			/**
			 * Show SMS provider settings view
			 */
			messageProviderSettingsSMS : function()
			{
				require(['messages/settings/MessageSettingsController'],
					function(MessageSettingsController)
				{
					MessageSettingsController.showSMSSettings();
				});
			},
			/**
			 * Show message provider settings page
			 * @param {string} provider   Provider name
			 */
			messageProviderSettings : function(provider)
			{
				require(['messages/settings/MessageSettingsController'],
					function(MessageSettingsController)
				{
					MessageSettingsController.showProviderSettings(provider);
				});
			}
		};

		App.addInitializer(function(){
			new Marionette.AppRouter({
				appRoutes: {
					'messages' : 'messages',
					'messages/list/:view' : 'messages',
					'messages/settings' : 'messageSettingsMain',
					// FIXME: temp route for sms hard coding
					'messages/settings/sms' : 'messageProviderSettingsSMS',
					'messages/settings/:provider' : 'messageProviderSettings',
				},
				controller: MessagesAPI
			});
		});
	});
