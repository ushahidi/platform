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

			// To prevent tag selector widget in the create post form from breaking
			toString : function ()
			{
				return this.get('tag');
			},

			defaults : {
				type : 'category',
				priority : 0
			},

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
					description: {
						type: 'Text',
						title: 'Description',
						editorAttrs : {
							placeholder : 'Enter a description'
						}
					},
					color : {
						type: 'Text',
						title: 'Color',
						editorAttrs : {
							placeholder : 'Enter a color. Eg. #c96880'
						}
					}
				};
				return schema;
			},
			validation : function ()
			{
				var rules = {
					tag : {
						required : true,
						maxLength : 150
					},
					description : {
						required: false,
						maxLength : 150
					},
					color : {
						required : false,
						maxLength: 7,
						pattern: '^#?(([0-9a-fA-F]{2}){3}|([0-9a-fA-F]){3})$',
						msg: 'Please enter a valid hex color code. Eg. #000000 or fff or c96880 or #fff'
					}
				};

				return rules;
			},
		});

		return TagModel;
	});