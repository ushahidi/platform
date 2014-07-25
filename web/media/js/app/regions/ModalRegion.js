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

define(['App', 'backbone', 'marionette', 'underscore', 'jquery'],
	function(App, Backbone, Marionette, _, $)
	{
		var delayClose,
			KEY_ESCAPE = 27;
		return Marionette.Region.extend(
		{
			onShow : function(view)
			{
				var that = this,
					$body = $('body'),
					close = function(e)
					{
						e.preventDefault();
						ddt.log('ModalRegion', 'sending modal:close to view');
						view.trigger('modal:close', e);
						that.close();
					};

				if (delayClose)
				{
					// A very fast close, show sequence happens when we change
					// views very quickly. Rather than rebinding everything,
					// we abort the close actions.
					clearTimeout(delayClose);
					ddt.log('ModalRegion', 'onShow ignored, modal is open');
				}
				else
				{
					$body
						.addClass('modal-active')
						.on('click.modal', '.js-modal-close', close)
						.on('keyup.modal', function(e)
						{
							if (e.which === KEY_ESCAPE)
							{
								close(e);
							}
						});
				}

				// Setting a short timeout ensures the DOM is ready. Without this,
				// maps often end up with buggy tiles.
				setTimeout(function()
				{
					if ($body.hasClass('modal-active'))
					{
						ddt.log('ModalRegion', 'sending modal:open to view');
						view.trigger('modal:open', {});
					}
				}, 100);
			},
			onClose : function()
			{
				var $body = $('body');
				if (!$body.hasClass('modal-active'))
				{
					return ddt.log('ModalRegion', 'onClose ignored, modal is not active');
				}
				// Do not immediately close the modal, this might be a view change.
				delayClose = setTimeout(function()
				{
					$body.off('.modal').removeClass('modal-active');
					delayClose = null;
				}, 50);
			}
		});
	});
