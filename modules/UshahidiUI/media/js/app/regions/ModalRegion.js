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
			onShow : function(view)
			{
				this.$el.foundation('reveal', 'open')
					.on('open', function (e) { view.trigger('modal:open', e); })
					.on('opened', function (e) { view.trigger('modal:opened', e); })
					.on('close', function (e) { view.trigger('modal:close', e); })
					.on('closed', function (e) { view.trigger('modal:closed', e); });
			},
			onClose : function()
			{
				this.$el.foundation('reveal', 'close');
					/*.off('open')
					.off('opened')
					.off('close')
					.off('closed');*/
			}
		});
	});
