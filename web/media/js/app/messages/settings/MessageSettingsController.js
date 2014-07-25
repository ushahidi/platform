/**
 * Messages Settings Controller
 *
 * @module     MessagesListController
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'backbone', 'App',
		'models/ConfigModel',
		'collections/DataProviderCollection',

		'messages/settings/ProviderLayout',
		'messages/settings/ProviderListView',
		'messages/settings/ProviderSettingsView'
	],
	function(_, Backbone, App,
		ConfigModel,
		DataProviderCollection,

		ProviderLayout,
		ProviderListView,
		ProviderSettingsView
	)
{
	var dataProviders = new DataProviderCollection(),
		providerTypes = new Backbone.Collection([
			{ id: 'sms', name: 'SMS', icon: 'mobile' },
			{ id: 'email', name: 'Email', icon: 'envelope-o' },
			{ id: 'twitter', name: 'Twitter', icon: 'twitter' },
			{ id: 'rss', name: 'RSS', icon: 'rss' }
		]),
		providerLayout = new ProviderLayout({
			collection : providerTypes
		}),
		MessageSettingsController;

	dataProviders.fetch();

	MessageSettingsController = {
		/**
		 * Shows a data provider listing
		 */
		listProviders : function ()
		{
			if (!App.feature('data_provider_config'))
			{
				App.appRouter.navigate('', { trigger : true });
				return;
			}

			App.vent.trigger('page:change', 'messages/settings');

			var providerConfig = new ConfigModel({'@group': 'data-provider'}),
				providerList = new ProviderListView({
					collection : dataProviders,
					configModel : providerConfig
				});

			// Grab data-provider config and bind 'enabled'
			providerConfig.fetch().done(function ()
			{
				_.each(providerConfig.get('providers'), function (enabled, index)
				{
					dataProviders.get(index).set('enabled', enabled);
				});
			});

			App.layout.mainRegion.show(providerLayout);
			providerLayout.main.show(providerList);
		},

		/**
		 * Show a config form for an individual data provider
		 * @param  String provider id
		 */
		showProviderSettings : function(id)
		{
			if (!App.feature('data_provider_config'))
			{
				App.appRouter.navigate('', { trigger : true });
				return;
			}

			App.vent.trigger('page:change', 'messages/settings');

			var
				providerModel = dataProviders.get(id),
				providerConfig = new ConfigModel({'@group': 'data-provider'});

			providerConfig.fetch().done(function ()
			{
				providerLayout.main.show(new ProviderSettingsView({
					dataProviderModel : providerModel,
					configModel : providerConfig
				}));
			});
		},

		// FIXME: temp controller for sms hard coding
		showSMSSettings : function(/*id*/)
		{
			if (!App.feature('data_provider_config'))
			{
				App.appRouter.navigate('', { trigger : true });
				return;
			}

			require(['hbs!messages/settings/ProviderSettingsSms'],
				function(template)
			{
				App.vent.trigger('page:change', 'messages/settings');

				var
					providerModel = dataProviders.get('smssync'),
					providerConfig = new ConfigModel({'@group': 'data-provider'});

				providerConfig.fetch().done(function ()
				{
					providerLayout.main.show(new ProviderSettingsView({
						dataProviderModel : providerModel,
						configModel : providerConfig,
						template: template
					}));
				});
			});
		},
	};

	return MessageSettingsController;
});