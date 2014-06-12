/**
 * Attribute Model
 *
 * @module     FormAttributeModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'modules/config'],
	function(Backbone, config) {
		var FormAttributeModel = Backbone.Model.extend(
		{
			urlRoot: config.get('apiurl') + '/attributes',
			defaults : {
				cardinality: 1,
				required: false,
				options: {}
			},
			toString : function ()
			{
				return this.get('label');
			},
			schema : function ()
			{
				var that = this,
					input = this.get('input'),
					options = function(callback)
					{
						callback(that.get('options') || {});
					},
					fields = {
						label: 'Text',
						required: 'Checkbox',
						cardinality: {
							title: 'Allowed entries',
							type: 'Number',
							help: 'Number of entries allowed in this field. 0 is unlimited.'
						}
					};

				if (! input) {
					return ddt.trace('Forms', 'invalid form attribute');
				}

				// todo: stop reformatting input types between server/client
				if (input === 'textarea') {
					input = 'TextArea';
				} else if (input === 'datetime') {
					input = 'DateTime';
				} else {
					// JS equivalent of PHP's ucfirst()
					input = input.charAt(0).toUpperCase() + input.substr(1);
				}

				// Default value should use same input as the current attribute
				fields.default = {
					title: 'Default value',
					type: input,
					options: options
				};

				switch (input) {
					case 'Radio':
					case 'Select':
					case 'Checkboxes':
						// @todo use a better editor here. List editor would be good but has issues
						// https://github.com/powmedia/backbone-forms/pull/372
						fields.options = {
							title: 'Possible Options',
							type: 'TextArea',
						};
					break;
				}

				// todo: the preview field should be disabled, but some Backbone.Form
				// editors don't place nice with editorAttrs using `{disabled: "disabled}`
				// (Date, DateTime, and Location editors, possibly others)
				fields.preview = {
					type: input,
					options: options
				};

				return fields;
			}
		});

		return FormAttributeModel;
	});
