/**
 * Workspace Panel View
 *
 * @module     WorkspacePanelView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'handlebars', 'App', 'text!templates/WorkspacePanel.html'],
	function(Marionette, Handlebars, App, template)
	{
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			events : {
				'click .js-title' : 'toggleSection',
				'click .workspace-menu li' : 'toggleMenuItem'
			},
			toggleSection : function(e)
			{
				var $el = this.$(e.currentTarget.parentNode);
				$el.toggleClass('active');
				e.preventDefault();
			},
			toggleMenuItem : function(e)
			{
				e.preventDefault();
				this.$('.workspace-menu li').removeClass('active');
				this.$(e.currentTarget).addClass('active');
			}
		});
	});