/**
 * User Model
 *
 * @module     UserModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'App', 'modules/config', 'backbone-model-factory'],
	function(Backbone, App, config)
	{
		var UserModel = Backbone.ModelFactory(
		{

			urlRoot: config.get('apiurl') + 'users',

			// Initialize `change_role` to true by default to allow users with role
			// to add a new user to be able to set the new user's role
			defaults : {
				allowed_methods : {
					change_role: true,
				}
			},

			schema : function ()
			{
				var schema = {
					username: {
						type: 'Text',
						title: 'Username',
						editorAttrs : {
							placeholder : 'Enter username'
						}
					},
					realname: {
						type: 'Text',
						title: 'Name',
						editorAttrs : {
							placeholder : 'Enter full name'
						}
					},
					email: {
						type: 'Text',
						title: 'Email address',
						editorAttrs : {
							placeholder : 'Enter email address'
						}
					},
					password: {
						type: 'Password',
						title: 'Password'
					}
				};

				if (typeof this.get('allowed_methods') !== 'undefined' && this.get('allowed_methods').change_role)
				{
					schema.role = {
						type: 'Select',
						title: 'Role',
						options : App.Collections.Roles
					};
				}

				return schema;
			},
			validation : function ()
			{
				var rules = {
					username : {
						maxLength : 150,
						required : true
					},
					firstname : {
						maxLength : 150,
						required: false
					},
					lastname : {
						maxLength : 150,
						required: false
					},
					email : {
						pattern: 'email',
						required : false
					},
					password : {
						minLength: 7,
						required : false
					}
				};

				return rules;
			},
		});
		return UserModel;
	});
