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
			urlRoot: App.config.baseurl + 'api/v2/posts',
			user : null,
			form : null,
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
						id: this.get('user').id
					});
					requests.push(user.fetch());
				}

				if (this.get('form'))
				{
					form = new FormModel({
						id: this.get('form').id
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
					var tagModel = App.Collections.Tags.get(tag.id);
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
			}
		});

		return PostModel;
	});