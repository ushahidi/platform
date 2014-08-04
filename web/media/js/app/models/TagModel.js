/**
 * Tag Model
 *
 * @module     TagModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'App', 'modules/config', 'backbone-model-factory',
'models/UserModel'],
	function(Backbone, App, config) {
		var TagModel = Backbone.ModelFactory(
		{
			urlRoot: config.get('apiurl') +'tags',

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
					type : {
						type: 'Radio',
						title: 'Type',
						options: ['category', 'status']
					},
					color : {
						type: 'Select',
						title: 'Color',
						options: {
							'#323a45': 'Gray',
							'#7bd148': 'Green',
							'#5484ed': 'Bold blue',
							'#a4bdfc': 'Blue',
							'#46d6db': 'Turquoise',
							'#7ae7bf': 'Light green',
							'#51b749': 'Bold green',
							'#fbd75b': 'Yellow',
							'#ffb878': 'Orange',
							'#ff887c': 'Red',
							'#dc2127': 'Bold red',
							'#dbadff': 'Purple'
						}
					},
					icon : {
						type: 'Select',
						title: 'Icon',
						options: [
							'tag',
							'medkit',
							'mobile',
							'camera-retro',
							'video-camera',
							'desktop',
							'question',
							'ticket',
							'coffee',
							'cutlery',
							'bell',
							'suitcase',
							'legal',
							'truck',
							'globe'
						]
					},
					role : {
						type:'Checkboxes',
						options: App.Collections.Roles
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
					},
					icon : {
						required: false,
						maxLength: 32,
						pattern: '^[a-zA-Z0-9-]+$',
					},
					role : {
						required: false
				    },
				};

				return rules;
			},
		});

		return TagModel;
	});
