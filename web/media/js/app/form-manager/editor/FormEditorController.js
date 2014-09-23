/**
 * Form Editor Controller
 *
 * @module     FormEditorController
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'underscore', 'alertify',
		'forms/UshahidiForms',
		'form-manager/editor/FormEditor',
		'form-manager/editor/AttributeList',
		'collections/FormAttributeCollection',
		'form-manager/defaultFormAttrs'
	],
	function(App, _, alertify,
		BackboneForm,
		FormEditor,
		AttributeList,
		FormAttributeCollection,
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
				form = this.form,
				formAttributes = new FormAttributeCollection(_.values(form.formAttributes)),
				formGroup = (form.get('groups')[0] || {}),
				attributeList = new AttributeList({
					collection : formAttributes,
					form_group_id : formGroup.id
				});

			this.layout = new FormEditor({
				model : form,
				availableAttributes : defaultFormAttrs,
				sortableList : attributeList
			});

			attributeList.on('childview:edit', this.showAttributeEditor, this);

			// Show the layout and attribute lists
			App.layout.mainRegion.show(this.layout);
			this.layout.formAttributes.show(attributeList);
		},
		showAttributeEditor: function(childView, attribute)
		{
			ddt.log('FormEditor', 'showEditor', attribute);

			this.attributeForm = new BackboneForm({
				model: attribute,
				submitButton : 'Save'
			});

			this.attributeForm.on('submit', this.saveAttribute, this);

			this.layout.attributeEditor.show(this.attributeForm);
		},
		saveAttribute: function(e)
		{
			e.preventDefault();
			var data = this.attributeForm.getValue();

			ddt.log('FormEditor', 'form data', data);

			// Split options apart since server expects an array
			if (data.options)
			{
				data.options = data.options.split(',');
			}

			this.attributeForm.model.set(_.pick(data, 'label', 'options', 'default', 'format', 'required', 'cardinality'));
			this.attributeForm.model.save({
					wait: true
				})
				.done(function ()
				{
					alertify.success('Field saved');
				})
				.fail(function ()
				{
					alertify.error('Unable to save field, please try again');
				});
		}
	};

	_.bindAll(FormEditorController, 'renderEditor');

	// Return just the public methods
	return FormEditorController;
});