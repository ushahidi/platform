/**
 * Form Model
 *
 * @module     FormModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'App'],
	function($, Backbone, App) {
		var FormModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + App.config.apiuri + '/forms',
			getPostSchema : function()
			{
				var schema = {},
					groups = this.get('groups'),
					valueToString = function(item) { return item.value; alert(item.value); },
					inputFieldMap,
					attributes,
					attribute,
					i,
					j;

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
					'file' : 'File'
				};

				for (i = 0; i < groups.length; i++)
				{
					attributes = groups[i].attributes;
					for (j = 0; j < attributes.length; j++)
					{
						attribute = attributes[j];
						// Skip attribute if missing options
						if (attribute.input === 'Select' && ! attribute.options)
						{
							continue;
						}

						// Single value field
						if (parseInt(attribute.cardinality, 10) === 1)
						{
							schema['values.' + attribute.key] = {
								title : attribute.label,
								type : inputFieldMap[attribute.input],
								options : attribute.options
							};
						}
						// Multi-value field, handled with List editor
						else
						{
							schema['values.' + attribute.key] = {
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
						}
					}
				}

				return schema;
			},
			getPostFieldsets : function ()
			{
				var fieldsets = [],
					fieldset,
					groups = this.get('groups'),
					group,
					attributes,
					attribute,
					i,
					j;

				for (i = 0; i < groups.length; i++)
				{
					group = groups[i];
					attributes = group.attributes;

					fieldset = {
						legend: group.label,
						name : group.id,
						fields: []
					};

					for (j = 0; j < attributes.length; j++)
					{
						attribute = attributes[j];
						// Skip attribute if missing options
						if (attribute.input === 'Select' && ! attribute.options)
						{
							continue;
						}

						fieldset.fields.push('values.' + attribute.key);
					}

					fieldsets.push(fieldset);
				}

				return fieldsets;
			},
			getPostValidation : function ()
			{
				var rules = {},
					groups = this.get('groups'),
					group,
					attributes,
					attribute,
					i,
					j,
					key;

				for (i = 0; i < groups.length; i++)
				{
					group = groups[i];
					attributes = group.attributes;

					for (j = 0; j < attributes.length; j++)
					{
						attribute = attributes[j];
						// Skip attribute if missing options
						if (attribute.input === 'Select' && ! attribute.options)
						{
							continue;
						}

						key = 'values.' + attribute.key;
						rules[key] = {};
						rules[key].required = attribute.required;
						if (attribute.type === 'link')
						{
							rules[key].pattern = 'url';
						}

						// Multi value field - pipe through validateArray
						if (parseInt(attribute.cardinality, 10) !== 1)
						{
							rules[key] = {
								validateArray : rules[key]
							};
						}
					}
				}

				return rules;
			}
		});
		return FormModel;
	});