/**
 * Form Editor Controller
 *
 * @module     FormEditorController
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'underscore',
		'form-manager/editor/FormEditor',
		'form-manager/defaultFormAttrs'
	],
	function(App, _,
		FormEditor,
		defaultFormAttrs
	)
{
	var FormEditorController = {
		showEditor : function (id)
		{
			App.vent.trigger('page:change', 'forms');

			this.form = App.Collections.Forms.get(id);

			// Force a refresh of the form, to make sure we have complete
			// and updated groups/attributes. See T676.
			this.form.fetch().done(this.renderEditor);
		},
		renderEditor : function() {
			var
				form = this.form;

			this.layout = new FormEditor({
				model : form,
				availableAttributes : defaultFormAttrs
			});

			// Show the layout and attribute lists
			App.layout.mainRegion.show(this.layout);
		}
	};

	_.bindAll(FormEditorController, 'renderEditor');

	// Return just the public methods
	return FormEditorController;
});
