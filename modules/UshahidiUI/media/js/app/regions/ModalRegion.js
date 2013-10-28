/**
 * Modal Region
 *
 * Handles transitions for showing / hiding the modal
 *
 * @module     ModalRegion
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'backbone', 'marionette'],
	function(App, Backbone, Marionette)
	{
		return Marionette.Region.extend(
		{
			// Override open to trigger foundation reveal
			open : function(view){
				this.$el.empty().append(view.el);
				this.$el.foundation('reveal', 'open');
			}
		});
	});
