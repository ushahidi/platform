/**
 * Choose Form
 *
 * @module     ChooseFormView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'marionette', 'hbs!templates/modals/ChooseForm'],
	function( Marionette, template)
	{
		return Marionette.ItemView.extend( {
			template: template,
			initialize: function(options) {
				this.forms = options.forms;
			},
			events : {
				'click .js-forms-grid li' : 'selectForm'
			},
			selectForm : function (e)
			{
				e.preventDefault();
				var $el = this.$(e.currentTarget);
				this.model.set('form', $el.attr('data-form-id'));
				this.model.form = this.forms.get($el.attr('data-form-id'));
				this.trigger('form:select');
			},
			serializeData : function ()
			{
				return {
					forms: this.forms.toJSON()
				};
			}
		});
	});
