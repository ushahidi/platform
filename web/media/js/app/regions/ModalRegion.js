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

define(['App', 'backbone', 'marionette', 'alertify', 'underscore', 'jquery'],
	function(App, Backbone, Marionette, alertify, _, $)
	{
		var delayClose,
			KEY_ESCAPE = 27;
		return Marionette.Region.extend(
		{
			onShow : function(view)
			{
				var that = this,
					$body = $('body'),
					$modal = $('#modal'),
					form_has_changed = false,
					close = function(e)
					{
						e.preventDefault();

						if (!form_has_changed) { return that.empty(); }

						alertify.confirm('You have unsaved changes! Discard them?', function(e) {
							if (e) { that.empty(); }
						});
					};

				if (delayClose)
				{
					// A very fast close, show sequence happens when we change
					// views very quickly. In this case, we abort the close actions.
					clearTimeout(delayClose);
					ddt.log('ModalRegion', 'onShow ignored, modal is open');
				}
				else
				{
					// Keep the modal in viewport
					$modal.css('margin-top', $(document).scrollTop());
					$body.addClass('modal-active');
				}

				// We rebind the close function each time here because otherwise
				// the form_has_changed variable would reference the previous
				// modal during a modal view change
				$body
					.off('click.modal').on('click.modal', '.js-modal-close', close)
					.off('keyup.modal').on('keyup.modal', function(e)
					{
						if (e.which === KEY_ESCAPE)
						{
							close(e);
						}
					});

				// Setting a short timeout ensures the DOM is ready. Without this,
				// maps often end up with buggy tiles.
				setTimeout(function()
				{
					if ($body.hasClass('modal-active'))
					{
						ddt.log('ModalRegion', 'sending modal:open to view');
						view.trigger('modal:open', {});
						that.trigger('modal:open');

						// Record when a form has changed so we can display
						// a confirmation before discarding unsaved changes
						$modal.one('change.modal', 'form :input', function() {
							form_has_changed = true;
						});
					}
				}, 100);
			},
			onBeforeEmpty : function()
			{
				// Workaround for marionette bug that triggers empty() twice
				// https://github.com/marionettejs/backbone.marionette/issues/1920
				this.currentView.off('destroy');
			},

			onEmpty : function(view)
			{
				var that = this,
						$body = $('body');
				if (!$body.hasClass('modal-active'))
				{
					return ddt.log('ModalRegion', 'onEmpty ignored, modal is not active');
				}
				// Do not immediately close the modal, this might be a view change.
				delayClose = setTimeout(function()
				{
					ddt.log('ModalRegion', 'sending modal:close to view');
					that.trigger('modal:close');
					// View is already destroyed by this point so not sure this is useful
					view.trigger('modal:close');

					$body.off('.modal').removeClass('modal-active');
					delayClose = null;
				}, 50);
			}
		});
	});
