/**
 * Form List
 *
 * @module     FormListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'form-manager/list/FormListItem', 'views/EmptyView', 'hbs!form-manager/list/FormList'],
	function( App, Marionette, _, FormListItem, EmptyView, template)
	{
		return Marionette.CompositeView.extend(
		{
			template: template,

			childView: FormListItem,

			childViewContainer: '.list-view-form-list',

			emptyViewOptions:
			{
				emptyMessage: 'No forms found.',
			},

			emptyView: EmptyView,

			events : {
				'click .js-custom-form-create' : 'showCustomFormCreate',
			},

			showCustomFormCreate : function(e)
			{
				e.preventDefault();
				App.vent.trigger('customform:create');
			}
		});
	});
