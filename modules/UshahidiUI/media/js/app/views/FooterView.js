/**
 * Footer View
 *
 * @module     FooterView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'handlebars', 'App', 'text!templates/Footer.html'],
	function(Marionette, Handlebars, App, template)
	{
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			initialize: function() {
				App.vent.on('config:change', this.render, this);
			},
			serializeData : function()
			{
				return {
					site_name : App.config.site.site_name,
					owner_name : App.config.site.owner_name
				};
			}
		});
	});