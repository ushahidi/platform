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
				
				if (typeof this.get('user') !== 'undefined')
				{
					user = new UserModel({
						id: this.get('user').id
					});
					requests.push(user.fetch());
				}

				if (typeof this.get('form') !== 'undefined')
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
			}
		});

		return PostModel;
	});