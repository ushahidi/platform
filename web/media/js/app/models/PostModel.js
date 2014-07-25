/**
 * Post Model
 *
 * @module     PostModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'App', 'underscore', 'modules/config', 'models/UserModel', 'models/FormModel', 'backbone-model-factory'],
	function($, Backbone, App, _, config, UserModel, FormModel) {
		var PostModel = Backbone.ModelFactory(
		{
			urlRoot: config.get('apiurl') + 'posts',
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
						options : App.Collections.Tags.fullCollection ? App.Collections.Tags.fullCollection : App.Collections.Tags,
						editorAttrs : {
							multiple : true
						}
					}
					// @todo should we include slug?
				};

				// If post already has a user
				if (parseInt(this.get('user'), 10) > 0 || App.loggedin())
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
						'user.realname' : {
							title : 'Name',
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
					_.extend(schema, _.result(this.form, 'postSchema'));
				}

				return schema;
			},
			fieldsets : function ()
			{
				var fieldsets = [],
					mainFieldset = {
						name : 'main',
						active : true,
						legend : 'Main',
						fields : []
					},
					mainFields;

				// Only show user fields if not set yet
				if (parseInt(this.get('user'), 10) > 0 || App.loggedin())
				{
					mainFields = ['title', 'content', 'tags', 'user'];
				}
				else
				{
					mainFields = ['title', 'content', 'tags', 'user.realname', 'user.email'];
				}

				if (App.feature('media_uploads')) {
					mainFields.push('media');
				}

				// Extend with form schema if form_id is set
				if (this.get('form'))
				{
					fieldsets = _.union(fieldsets, _.result(this.form, 'postFieldsets'));
					// Combine main fieldset into the initial fieldset
					mainFieldset = _.extend(mainFieldset, fieldsets[0]);
					mainFieldset.name = 'main'; // Always use 'main' as the name
					// Push default fields onto the start of post form fields
					mainFieldset.fields = mainFields.concat(mainFieldset.fields);
				}

				fieldsets[0] = mainFieldset;
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

				if (parseInt(this.get('user'), 10) > 0 || App.loggedin())
				{
					rules.user = {
						required: false,
						pattern: 'number'
					};
				}
				else
				{
					rules['user.email'] = {
						pattern: 'email',
						required: false
					};
					rules['user.realname'] = {
						maxLength: 150,
						required: false
					};
				}

				// Extend with form schema if form_id is set
				if (this.get('form'))
				{
					rules = _.extend(rules, _.result(this.form, 'postValidation'));
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
				return this.get('values-' + key);
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
				return _.compact(_.map(this.get('tags'), function(tag)
				{
					var tagCollection = App.Collections.Tags.fullCollection ? App.Collections.Tags.fullCollection : App.Collections.Tags,
						tagModel = tagCollection.get(tag);
					return tagModel ? tagModel.toJSON() : false;
				}));
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
				if (! groups)
				{
					ddt.trace('PostModel', 'Get location while post form missing groups', this.form);
					return;
				}

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
						data['values-'+key] = data.values[key];
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
						key.substr(0, 7) === 'values-')
					{
						values[key.substr(7)] = data[key];
						delete data[key];
					}
				}
				data.values = values;

				return data;
			},

			/**
			 * Get next model in collection
			 * @return PostModel|false
			 */
			getNext : function(collection)
			{
				collection = collection || this.collection;
				if (collection)
				{
					return this.collection.getNextModel(this);
				}
				return false;
			},

			/**
			 * Are there more models
			 * @return {Boolean}
			 */
			hasNext : function(collection)
			{
				collection = collection || this.collection;

				// Are there more pages?
				if (collection && collection.hasNextPage())
				{
					return true;
				}
				// Is this the last model?
				else
				{
					return _.isObject(this.getNext(collection));
				}
			},

			/**
			 * Get previous model in collection
			 * @return PostModel|false
			 */
			getPrev : function(collection)
			{
				collection = collection || this.collection;
				if (collection)
				{
					return this.collection.getPrevModel(this);
				}
				return false;
			},

			/**
			 * Are the previous models?
			 * @return {Boolean}
			 */
			hasPrev : function(collection)
			{
				collection = collection || this.collection;

				// Are there more pages?
				if (collection && collection.hasPreviousPage())
				{
					return true;
				}
				// Is this the first model?
				else
				{
					return _.isObject(this.getPrev(collection));
				}
			}
		});

		return PostModel;
	});
