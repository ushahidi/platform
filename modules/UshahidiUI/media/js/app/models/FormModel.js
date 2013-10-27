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
			urlRoot: App.config.baseurl + 'api/v2/forms',
			getPostSchema : function()
			{
				var schema = {},
					groups = this.get('groups'),
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
					'file' : 'Text'
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

						schema['values.' + attribute.key] = {
							title : attribute.label,
							type : inputFieldMap[attribute.input],
							options : attribute.options
						};
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
			}
		});
		return FormModel;
	});