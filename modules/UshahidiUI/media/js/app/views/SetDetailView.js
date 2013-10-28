/**
 * Set Detail
 *
 * @module     SetDetailView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'App', 'marionette', 'handlebars', 'text!templates/SetDetail.html', 'text!templates/partials/set-module.html'],
    function( App, Marionette, Handlebars, template, setModuleTemplate)
	{
		// Hacky - make sure we register partials before we call compile
		Handlebars.registerPartial('set-module', setModuleTemplate);
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() {
			},
			events : {
			}
		});
	});
