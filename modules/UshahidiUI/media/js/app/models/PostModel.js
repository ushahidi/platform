/**
 * Post Model
 *
 * @module     PostModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'App', 'underscore', 'models/UserModel', 'models/FormModel'],
	function($, Backbone, App, _, UserModel, FormModel) {
		var PostModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + App.config.apiuri + '/posts',
			user : null,
			form : null,
			defaults : {
				locale : 'en_us',
				status : 'draft'
			},
			schema : function ()
			{
				var schema = {
					title: {
						type: 'Text',
						title: 'Title',
						editorAttrs : {
							placeholder : 'Enter a title'
						}
					},
					content: {
						type: 'TextArea',
						title: 'Description',
						editorAttrs : {
							placeholder : 'Enter a short description',
							rows : 30,
							cols : 30
						}
					},
					status : {
						type: 'Radio',
						title: 'Status',
						options: {
							'published' : 'Published',
							'draft' : 'Draft',
							'pending' : 'Pending'
						}
					},
					tags : {
						type : 'Select',
						title : 'Tags',
						options : App.Collections.Tags,
						editorAttrs : {
							multiple : true
						}
					}
					// @todo should we include slug?
				};

				if (parseInt(this.get('user'), 10) > 0)
				{
					_.extend(schema, {
						'user' : {
							'title' : 'User ID',
							'type' : 'Hidden',
							editorAttrs: {
								disabled : true
							}
						}
					});
				}
				else
				{
					_.extend(schema, {
						'user.first_name' : {
							title : 'First Name',
							type: 'Text'
						},
						'user.last_name': {
							title : 'Last Name',
							type: 'Text'
						},
						'user.email' : {
							type : 'Text',
							title : 'Email'
						}
					});
				}

				// Extend with form schema if form_id is set
				if (this.get('form'))
				{
					_.extend(schema, this.form.getPostSchema());
				}

				return schema;
			},
			fieldsets : function ()
			{
				var fieldsets = [];

				// Extend with form schema if form_id is set
				if (this.get('form'))
				{
					fieldsets = _.union(fieldsets, this.form.getPostFieldsets());

					// Push main fields onto first group.
					fieldsets[0].name = 'main';
					fieldsets[0].active = true;
					// Only show user fields if not set yet
					if (parseInt(this.get('user'), 10) > 0)
					{
						fieldsets[0].fields.unshift('user');
					}
					else
					{
						fieldsets[0].fields.unshift('user.email');
						fieldsets[0].fields.unshift('user.last_name');
						fieldsets[0].fields.unshift('user.first_name');
					}
					fieldsets[0].fields.unshift('tags');
					fieldsets[0].fields.unshift('content');
					fieldsets[0].fields.unshift('title');
				}
				else
				{
					fieldsets.push(
						{
							name : 'main',
							legend : 'Main',
							fields : (parseInt(this.get('user'), 10) > 0) ?
								['title', 'content', 'tags', 'user'] :
								['title', 'content', 'tags', 'user.first_name', 'user.last_name', 'user.email'],
							active: true
						}
					);
				}

				fieldsets.push(
					{
						name : 'permissions',
						legend : 'Permissions',
						fields : ['status'],
						icon : 'fa-lock'
					}
				);

				return fieldsets;
			},
			validation : function ()
			{
				var rules = {
					title : {
						required : true,
						maxLength : 150
					},
					content : {
						required : true
					},
					status : {
						required : true,
						oneOf : ['published', 'draft', 'pending']
					},
					locale : {
						required : true
					}
				};

				if (parseInt(this.get('user'), 10) > 0)
				{
					rules.user = {
						required: true,
						pattern: 'number'
					};
				}
				else
				{
					rules['user.email'] = {
						pattern: 'email',
						required: false
					};
					rules['user.first_name'] = {
						maxLength: 150,
						required: false
					};
					rules['user.last_name'] = {
						maxLength: 150,
						required: false
					};
				}

				// Extend with form schema if form_id is set
				if (this.get('form'))
				{
					rules = _.extend(rules, this.form.getPostValidation());
				}

				return rules;
			},
			initialize : function ()
			{
				this.relationsCallback = $.Deferred();
			},
			fetchRelations : function ()
			{
				//@TODO prevent multiple calls to this
				var that = this,
						requests = [],
						user,
						form;

				if (this.get('user'))
				{
					user = new UserModel({
						id: this.get('user')
					});
					requests.push(user.fetch());
				}

				if (this.get('form'))
				{
					form = new FormModel({
						id: this.get('form')
					});
					requests.push(form.fetch());
				}

				//@todo tags

				// When requests have returned,
				// make callback resolved and save models
				$.when.apply($, requests).done(function ()
				{
					that.user = user;
					that.form = form;
					that.relationsCallback.resolve();
				});
			},

			/**
			 * Accessor function for custom field values
			 *
			 * @param string key to return from 'values' object
			 * @return value from 'values' object
			 **/
			getValue : function (key)
			{
				var values = this.get('values');
				if (values)
				{
					return values[key];
				}
			},

			isPublished : function ()
			{
				if (this.get('status') === 'published')
				{
					return true;
				}
			},

			getTags : function ()
			{
				return _.map(this.get('tags'), function(tag)
				{
					var tagModel = App.Collections.Tags.get(tag);
					return tagModel ? tagModel.toJSON() : null;
				});
			},

			/**
			 * Get the first populated location field we find
			 * @TODO update this with a way to control which location field is returned
			 * @return object lat/lon values for the location
			 **/
			getLocation : function ()
			{
				var groups,
						g,
						attributes,
						a,
						attribute;

				if (! this.form)
				{
					return;
				}

				// Loop over all attributes to find a location
				groups = this.form.get('groups');
				loop_groups : for (g = 0; g < groups.length; g++)
				{
					attributes = groups[g].attributes;
					loop_attributes : for (a = 0; a < attributes.length; a++)
					{
						attribute = attributes[a];
						// Is it point? do we have a value for it?
						if (attribute.type === 'point' && this.getValue(attribute.key))
						{
							// Return the first point attribute with a value we find
							return this.getValue(attribute.key);
						}
					}
				}

				return false;
			},
			// Overriding the parse method to handle nested JSON values
			parse : function (data)
			{
				var key;

				if (data.user !== null && data.user.id !== null)
				{
					data.user = data.user.id;
				}

				if (data.form !== null && data.form.id !== null)
				{
					data.form = data.form.id;
				}

				if (data.tags !== null)
				{
					data.tags = _.pluck(data.tags, 'id');
				}

				for (key in data.values)
				{
					if( data.values.hasOwnProperty( key ) )
					{
						data['values.'+key] = data.values[key];
					}
				}
				delete data.values;

				return data;
			},
			// Overriding toJSON to reverse parsing of values
			toJSON : function ()
			{
				var data = Backbone.Model.prototype.toJSON.call(this),
					values = {},
					key;

				for (key in data)
				{
					if (data.hasOwnProperty( key ) &&
						key.substr(0, 7) === 'values.')
					{
						values[key.substr(7)] = data[key];
						delete data[key];
					}
				}
				data.values = values;

				return data;
			}
		});

		return PostModel;
	});