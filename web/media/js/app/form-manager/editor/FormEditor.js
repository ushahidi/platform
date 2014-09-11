/**
 * Forms
 *
 * @module     Forms
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'hbs!form-manager/editor/FormEditor', 'jqueryui/draggable'],
	function(App, Marionette, _, template)
	{
		return Marionette.Layout.extend(
		{
			template: template,
			form: null,
			availableFields : [],

			regions : {
				formAttributes : '.form-attributes',
				attributeEditor : '.js-edit-form'
			},

			events : {
				'click .js-edit-custom-form' : 'showCustomFormEdit',
				'click .js-edit-attr' : 'toggleEditor',
				'click .js-add-attr' : 'toggleEditor'
			},

			modelEvents : {
				'sync' : 'render',
			},

			initialize : function(options)
			{
				this.availableAttributes = options.availableAttributes;
				this.sortableList = options.sortableList;

				// Make sure editor is visible when showing the form
				this.attributeEditor.on('before:show', function() {
					var $editor = this.$('.js-edit-form'),
					$panel = $editor.closest('.content');

					if (!$panel.hasClass('active')) {
						this.$('.js-edit-attr').click();
					}
				}, this);
			},

			serializeData : function()
			{
				return _.extend(this.model.toJSON(), {
					availableAttributes : this.availableAttributes
				});
			},

			onDomRefresh: function ()
			{
				this.$('.available-attributes li').draggable({
					connectToSortable : this.sortableList.$el,
					helper: 'clone',
					revert: 'invalid'
				});
			},

			onClose : function ()
			{
				this.$('.available-attributes li').draggable('destroy');
			},

			toggleEditor : function(e)
			{
				e.preventDefault();

				this.$(e.currentTarget)
					.closest('.tabs')
						.find('.tab-title')
					.add(this.$('.edit-attribute'))
					.add(this.$('.available-attributes'))
					.toggleClass('active');
			},

			showCustomFormEdit : function(e)
			{
				e.preventDefault();
				App.vent.trigger('customform:edit', this.model);
			}
		});
	});
