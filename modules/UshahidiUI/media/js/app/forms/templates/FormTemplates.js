/**
 * Include this template file after backbone-forms.amd.js to override the default templates
 *
 * 'data-*' attributes control where elements are placed
 */
define(['jquery', 'handlebars', 'backbone', 'backbone-forms'], function($, Handlebars, Backbone) {
	var Form = Backbone.Form;


	/**
	 * Bootstrap templates for Backbone Forms
	 */
	Form.template = Handlebars.compile('<form data-fieldsets></form>');


	Form.Fieldset.template = Handlebars.compile(
		'<fieldset id="fieldset-{{ name }}" class="fieldset-{{ name }} {{#if active}}active{{/if}}" data-fields>' +
		'</fieldset>'
	);


	Form.Field.template = Handlebars.compile(
		'<div class="field-{{ key }}">' +
		'	<label for="{{ editorId }}">{{ title }}</label>' +
		'	<div>' +
		'		<div data-error></div>' +
		'		<span data-editor></span>' +
		'		{{#if help}}<div>{{ help }}</div>{{/if}}' +
		'	</div>' +
		'</div>'
	);


	// @todo should this show title? (maybe in a title attr)
	Form.NestedField.template = Handlebars.compile(
		'<div class="NestedField field-{{ key }}">' +
		'	<label for="{{ editorId }}">{{ title }}</label>' +
		'	<div>' +
		'		<div data-error></div>' +
		'		<span data-editor></span>' +
		'		{{#if help}}<div>{{ help }}</div>{{/if}}' +
		'	</div>' +
		'</div>'
	);

	Form.NestedForm.template = Handlebars.compile(
		'<span class="NestedForm" data-fieldsets></span>'
	);

	if (Form.editors.List) {

		Form.editors.List.template = Handlebars.compile(
			'<div>' +
			'	<ul data-items></ul>' +
			'	<button type="button" data-action="add">Add</button>' +
			'</div>'
		);


		Form.editors.List.Item.template = Handlebars.compile(
			'<li>' +
			'	<span data-editor></span>' +
			'	<button type="button" data-action="remove">&times;</button>' +
			'</li>'
		);

		Form.editors.List.Object.template = Form.editors.List.NestedModel.template = Handlebars.compile(
			'<div>{{ summary }}</div>'
		);

	}


});
