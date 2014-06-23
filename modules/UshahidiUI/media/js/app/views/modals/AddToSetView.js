/**
 * Add to set
 *
 * @module     AddToSetView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'marionette', 'handlebars', 'hbs!templates/modals/AddToSet', 'hbs!templates/sets/SetItemMini'],
	function( Marionette, Handlebars, template, setItemMiniTemplate)
	{
		// Hacky - make sure we register partials before we call compile
		Handlebars.registerPartial('SetItemMini', setItemMiniTemplate);
		return Marionette.ItemView.extend( {
			template: template,
			initialize: function() { },
			events : {
				'click .js-sets-grid li' : 'selectSet'
			},
			selectSet : function (e)
			{
				e.preventDefault();
				var $el = this.$(e.currentTarget);
				$el.toggleClass('selected');
			}
		});
	});
