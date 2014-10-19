/**
 * Form List Item View
 *
 * @module     FormListItem
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'hbs!form-manager/list/FormListItem'],
	function(App, Marionette, template)
	{
		return Marionette.ItemView.extend(
		{
			template: template,
			tagName: 'li',
			className: 'list-view-form',

			events: {
				'click .js-custom-form-delete': 'deleteForm',
				'click .js-custom-form-undelete': 'undeleteForm'
			},

			modelEvents: {
				'sync': 'render',
				'change': 'render'
			},

			deleteForm: function() {
				this.model.disable();
			},

			undeleteForm: function() {
				this.model.enable();
			}
		});
	});
