/**
 * Form Model
 *
 * @module     FormModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'modules/config', 'backbone-model-factory'],
	function(Backbone, config)
	{
		var valueToString = function(item) { return item.value; },
		// Map API 'input' to Backbone Forms Fields
		inputFieldMap = {
			'text' : 'Text',
			'textarea' : 'TextArea',
			'radio' : 'Radio',
			'checkbox' : 'Checkbox',
			'date' : 'Date',
			'datetime' : 'DateTime',
			'select' : 'Select',
			'location' : 'Location',
			'number' : 'Number',
			'file' : 'Text'
		},

		FormModel = Backbone.ModelFactory(
		{
			urlRoot: config.get('apiurl') + 'forms',
			initialize : function()
			{
				this.processForm();
				this.listenTo(this, 'change', this.processForm);
			},

			postSchema : {},
			postFieldsets : [],
			postValidation : {},
			formAttributes : {},

			processForm : function()
			{
				var groups = this.get('groups'),
					group,
					attributes,
					attribute,
					g,
					a,
					fieldset,
					added;

				if (! groups)
				{
					return;
				}

				// Reset postSchema, postFieldsets and postValidation
				this.postSchema = {};
				this.postFieldsets = [];
				this.postValidation = {};
				this.formAttributes = {};

				for (g = 0; g < groups.length; g++)
				{
					group = groups[g];

					fieldset = {
						legend: group.label,
						name : group.id,
						fields: []
					};

					attributes = groups[g].attributes;
					for (a = 0; a < attributes.length; a++)
					{
						attribute = attributes[a];
						this.formAttributes[attribute.key] = attribute;

						added = this.processAttribute(attribute);
						if (added)
						{
							fieldset.fields.push('values-' + attribute.key);
						}
					}

					this.postFieldsets.push(fieldset);
				}
			},

			processAttribute : function(attribute)
			{
				// Skip attribute if missing options
				if (attribute.input === 'Select' && ! attribute.options)
				{
					return false;
				}

				// Add postValidation
				this.postValidation['values-' + attribute.key] = {};
				this.postValidation['values-' + attribute.key].required = attribute.required;
				if (attribute.postValidation === 'link')
				{
					this.postValidation['values-' + attribute.key].pattern = 'url';
				}

				// Single value field
				if (parseInt(attribute.cardinality, 10) === 1)
				{
					// Add postSchema
					this.postSchema['values-' + attribute.key] = {
						title : attribute.label,
						type : inputFieldMap[attribute.input],
						options : attribute.options
					};
				}
				// Multi-value field
				else
				{
					// Use list editor for postSchema
					this.postSchema['values-' + attribute.key] = {
						title : attribute.label,
						type : 'List',
						itemToString : valueToString,
						itemType : 'Object',
						subSchema : {
							id : 'Hidden',
							value : {
								title: null,
								type: inputFieldMap[attribute.input],
								options : attribute.options
							}
						}
					};

					// Pipe postValidation through validateArray
					this.postValidation['values-' + attribute.key] = {
						validateArray : this.postValidation['values-' + attribute.key]
					};
				}

				return true;
			},

			getAttribute : function (key)
			{
				return this.formAttributes[key];
			}
		});
		return FormModel;
	});
