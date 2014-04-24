/**
 * User Model
 *
 * @module     UserModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'App'],
	function(Backbone, App)
	{

		var UserModel = Backbone.Model.extend(
		{

			urlRoot: App.config.baseurl + App.config.apiuri + '/users',
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

				if (this.get('allowed_methods').change_role)
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