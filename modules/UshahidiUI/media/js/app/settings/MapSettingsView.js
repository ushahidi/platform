/**
 * Map Settings
 *
 * @module     MapSettingsView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'marionette', 'jquery', 'alertify', 'underscore', 'modules/config', 'hbs!settings/MapSettings', 'no-ui-slider'],
	function( Marionette, $, alertify, _, config, template)
	{
		return Marionette.ItemView.extend( {
			template: template,
			initialize: function() {

			},
			onDomRefresh: function() {
				var customToolTip = $.Link({
					target: '-tooltip-<div class="default-zoom-slider-tooltip"></div>',
					method: function ( value ) {
						value = Math.round(value);
						$(this).html(
							'<span>' + value + '%</span>' +
							'<span class="nub"></span>'
						);
					}
				});

				this.$('.default-zoom-slider').noUiSlider({
					start: [20],
					step: 5,
					connect: 'lower',
					range: {
						'min': 0,
						'max': 100
					},
					serialization: {
						lower: [ customToolTip ]
					}
				});
			},
			events : {
				'submit form' : 'formSubmit'
			},
			serializeData : function()
			{
				return {
					map : config.get('map')
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
		});
	});
