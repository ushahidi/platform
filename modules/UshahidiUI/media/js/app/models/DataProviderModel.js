/**
 * Data Provider Model
 *
 * @module     DataProviderModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'underscore', 'backbone', 'App'],
	function($, _, Backbone, App) {
		// Map API 'input' to Backbone Forms Fields
		var inputFieldMap = {
			'text' : 'Text',
			'textarea' : 'TextArea',
			'radio' : 'Radio',
			'checkbox' : 'Checkbox',
			'date' : 'Date',
			'datetime' : 'DateTime',
			'select' : 'Select',
			'location' : 'Location',
			'number' : 'Number',
			'file' : 'Text',
			'read-only-text' : 'ReadOnlyText'
		},

		DataProviderModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + App.config.apiuri + '/dataproviders',
			toString : function ()
			{
				return this.get('name');
			},

			schema : function ()
			{
				var schema = {};

				_.each(this.get('options'), function (element, index) {
					schema[index] = {
						type : inputFieldMap[element.input] ? inputFieldMap[element.input] : 'Text',
						title : element.label,
						options : element.options,
						help : element.description,
						editorAttrs : {
							placeholder : element.placeholder
						}
					};
				});

				ddt.log('DataProviderModel', 'schema', schema);

				return schema;
			},
			defaults :
			{
				enabled : false
			}
		});

		return DataProviderModel;
	});
