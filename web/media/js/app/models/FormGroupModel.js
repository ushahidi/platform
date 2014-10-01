/**
 * Form Group Model
 *
 * @module     FormGroupModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'modules/config', 'backbone-model-factory'],
	function(Backbone, config) {
		var FormGroupModel = Backbone.ModelFactory(
		{
			urlRoot: function() {
				return config.get('apiurl') + 'forms/' + this.get('form_id') + '/groups';
			},
			// Overriding the parse method to handle nested JSON values
			parse : function (data)
			{
				if (data.form !== null && data.form.id !== null)
				{
					data.form_id = data.form.id;
				}

				return data;
			},

			schema : function ()
			{
				var schema = {
					label: {
						type: 'Text',
						title: 'Group Name',
						editorAttrs : {
							placeholder : 'Untitled Form'
						}
					}
				};

				return schema;
			},
			validation : function ()
			{
				var rules = {
					label : {
						maxLength : 150,
						required : true
					}
				};

				return rules;
			},

		});

		return FormGroupModel;
	});
