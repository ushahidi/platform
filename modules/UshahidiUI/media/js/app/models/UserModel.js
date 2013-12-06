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
			//console.log("user "+new UserRoleModel().toJSON());
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
					firstname: {
						type: 'Text',
						title: 'First name',
						editorAttrs : {
							placeholder : 'Enter first name'
						}
					},
					lastname: {
						type: 'Text',
						title: 'Last name',
						editorAttrs : {
							placeholder : 'Enter last name'
						}
					},
					email: {
						type: 'Text',
						title: 'Email address',
						editorAttrs : {
							placeholder : 'Enter email address'
						}
					},
					role: {
						type: 'Select',
						title: 'Roles',
						options: {
							admin: 'Admin',
							user: 'User',
							guest: 'Guest'
						}

					}
				};

				return schema;
			}
		});
		return UserModel;
	});