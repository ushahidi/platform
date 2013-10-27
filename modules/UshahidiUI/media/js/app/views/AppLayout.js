/**
 * Application Layout
 *
 * @module     AppLayout
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars', 'text!templates/AppLayout.html', 'text!templates/partials/modal.html', 'regions/ModalRegion'],
	function(App, Marionette, Handlebars, template, modalTemplate, ModalRegion)
	{
		// Hacky - make sure we register partials before we call compile
		Handlebars.registerPartial('modal', modalTemplate);
		return Marionette.Layout.extend(
		{
			className: 'app-layout',
			template : Handlebars.compile(template),
			regions : {
				headerRegion : '#header-region',
				mainRegion :   '#main-region',
				footerRegion : '#footer-region',
				workspacePanel : '#workspace-panel',
				modal : {
					selector : '#modal',
					regionType : ModalRegion
				}
			}
		});
	});