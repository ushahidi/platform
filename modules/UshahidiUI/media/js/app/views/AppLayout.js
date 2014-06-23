/**
 * Application Layout
 *
 * @module     AppLayout
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'hbs!templates/AppLayout', 'regions/ModalRegion'],
	function(App, Marionette, template, ModalRegion)
	{
		return Marionette.Layout.extend(
		{
			className: 'app-layout',
			template : template,
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