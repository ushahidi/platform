/**
 * Tag Model
 *
 * @module     TagModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'App'],
	function(Backbone, App) {
		var TagModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + App.config.apiuri +'/tags',
			schema : function ()
			{
				var schema = {
					tag: {
						type: 'Text',
						title: 'Name',
						editorAttrs : {
							placeholder : 'Enter name'
						}
					},
					slug: {
						type: 'Text',
						title: 'Slug',
						editorAttrs : {
							placeholder : 'Enter a slug'
						}
					},
					description: {
						type: 'Text',
						title: 'description',
						editorAttrs : {
							placeholder : 'Enter a description'
						}
					},
					type : {
						type: 'Text',
						title: 'Type',
						editorAttrs : {
							placeholder : 'Enter a type'
						}
					},
					priority : {
						type: 'Select',
						title: 'Priority',
						options: {
							2: 'High',
							1: 'Medium',
							0: 'Low'
						}
					},
					color : {
						type: 'Text',
						title: 'Color',
						editorAttrs : {
							placeholder : 'Enter a color'
						}
					}
				};
				return schema;
			},
			validation : function ()
			{
				var rules = {
					tag : {
						maxLength : 150,
						required : true
					},
					slug : {
						maxLength : 150,
						required: false
					},
					description : {
						maxLength : 150,
						required: false
					},
					type : {
						maxLength: 150,
						required : false
					},
					color : {
						maxLength: 7,
						required : false
					}
				};

				return rules;
			},
		});

		return TagModel;
	});