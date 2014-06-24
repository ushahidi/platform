/**
 * Settings
 *
 * @module     SettingsView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'modules/config', 'marionette', 'underscore', 'hbs!settings/Settings'],
	function( config, Marionette, _, template)
	{
		return Marionette.ItemView.extend( {
			template: template,
			initialize: function() {
			},
			events : {
				'submit .settings-site form' : 'formSubmitSite',
				'submit .settings-features form' : 'formSubmitFeatures'
			},
			serializeData : function()
			{
				return {
					site : config.get('site'),
					features : config.get('features')
				};
			},
			formSubmitSite : function(e)
			{
				e.preventDefault();

				var form = this.$(e.target),
					data = form.serializeArray(),
					group = 'site',
					hash = {};

				_.each(data, function(input) {
					hash[input.name] = input.value;
				});

				ddt.log('settings', 'update', group, hash);
				config.set(group, hash);
			},
			formSubmitFeatures : function(e)
			{
				e.preventDefault();

				var form = this.$(e.target),
					data = form.serializeArray(),
					group = 'features',
					hash = {};

				_.each(data, function(input) {
					// all feature values are boolean!
					hash[input.name] = (input.value === 'true');
				});

				ddt.log('settings', 'update', group, hash);
				config.set(group, hash);
			}
		});
	});
