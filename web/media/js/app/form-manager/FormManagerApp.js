/**
 * Form Manager Application
 *
 * @module     MessagesApp
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'App'],
	function(Marionette, App)
	{
		var FormManagerAPI = {
			/**
			 * Shows a form listing
			 */
			formList : function ()
			{
				require(['form-manager/list/FormList'], function(FormList)
				{
					App.vent.trigger('page:change', 'forms');
					App.layout.mainRegion.show(new FormList({
						collection : App.Collections.Forms
					}));
				});
			},
			/**
			 * Show a post wizard for an editing a post form
			 * @param  String form id
			 */
			formEditor : function(id)
			{
				require(['form-manager/editor/FormEditorController'],
					function(FormEditorController)
				{
					FormEditorController.showEditor(id);
				});
			}
		};

		App.addInitializer(function(){
			new Marionette.AppRouter({
				appRoutes: {
					'settings/forms' : 'formList',
					'settings/forms/:id' : 'formEditor',
				},
				controller: FormManagerAPI
			});
		});
	});
