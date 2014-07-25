/**
 * Include this template file after backbone-forms.amd.js to override the default templates
 *
 * 'data-*' attributes control where elements are placed
 */
define(['jquery', 'backbone', 'backbone-forms',

		'hbs!forms/templates/Form',
		'hbs!forms/templates/Fieldset',
		'hbs!forms/templates/Field',
		'hbs!forms/templates/NestedField',
		'hbs!forms/templates/NestedForm',
		'hbs!forms/templates/List',
		'hbs!forms/templates/ListItem',
		'hbs!forms/templates/ListObject'
	],
	function($, Backbone, BackboneForm,
		Form,
		Fieldset,
		Field,
		NestedField,
		NestedForm,
		List,
		ListItem,
		ListObject
	)
{
	/**
	 * Bootstrap templates for Backbone Forms
	 */
	BackboneForm.template = Form;


	BackboneForm.Fieldset.template = Fieldset;


	BackboneForm.Field.template = Field;


	// @todo should this show title? (maybe in a title attr)
	BackboneForm.NestedField.template = NestedField;

	BackboneForm.NestedForm.template = NestedForm;

	if (BackboneForm.editors.List) {

		BackboneForm.editors.List.template = List;


		BackboneForm.editors.List.Item.template = ListItem;

		BackboneForm.editors.List.Object.template = BackboneForm.editors.List.NestedModel.template = ListObject;

	}

});
