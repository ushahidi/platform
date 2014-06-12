/**
 * Ushahidi Main Controller
 *
 * @module     Controller
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license	https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'App', 'backbone', 'marionette', 'underscore', 'alertify',
	'controllers/ModalController',

	'views/AppLayout',
	'views/HomeLayout',

	'views/HeaderView',
	'views/FooterView',

	'views/WorkspacePanelView',

	'collections/PostCollection',
	'collections/TagCollection',
	'collections/FormCollection',
	'collections/SetCollection',
	'collections/RoleCollection',
	'collections/UserCollection',
	'collections/DataProviderCollection',

	'models/UserModel'
	],
	function($, App, Backbone, Marionette, _, alertify,
		ModalController,

		AppLayout,
		HomeLayout,

		HeaderView,
		FooterView,

		WorkspacePanelView,

		PostCollection,
		TagCollection,
		FormCollection,
		SetCollection,
		RoleCollection,
		UserCollection,
		DataProviderCollection,

		UserModel
		)
	{
		return Backbone.Marionette.Controller.extend(
		{
			initialize : function()
			{
				var user = new UserModel({ id: 'me' });

				if (App.loggedin()) {
					// only fetch the user when logged in
					user.fetch();
				}

				this.layout = App.layout = new AppLayout();
				App.body.show(this.layout);

				this.layout.headerRegion.show(new HeaderView());
				this.layout.footerRegion.show(new FooterView());
				this.layout.workspacePanel.show(new WorkspacePanelView({ model: user }));

				App.vent.on('workspace:toggle', function (close)
				{
					if (close)
					{
						App.body.$el.removeClass('active-workspace');
					}
					else
					{
						App.body.$el.toggleClass('active-workspace');
					}
				});

				$(document).on('click.workspace', 'body.active-workspace #main-region', function()
				{
					// Close the workspace menu when clicking on anything in the body
					App.vent.trigger('workspace:toggle', true);
				});

				App.Collections = {};
				App.Collections.Posts = new PostCollection();
				App.Collections.Posts.fetch();

				App.Collections.Forms = new FormCollection();
				App.Collections.Forms.fetch();
				App.Collections.Sets = new SetCollection();
				App.Collections.Sets.fetch();

				// Fake roles collection
				App.Collections.Roles = new RoleCollection([
					{
						name : 'admin',
						display_name : 'Admin',
						description : 'Administrator'
					},
					{
						name : 'user',
						display_name : 'Member',
						description : 'Default logged in user role'
					},
					{
						name : 'guest',
						display_name : 'Guest',
						description : 'Unprivileged role given to users who are not logged in'
					}
				]);

				// Open the user collection, but do not fetch it until necessary
				App.Collections.Users = new UserCollection();

				App.Collections.DataProviders = new DataProviderCollection();
				App.Collections.DataProviders.fetch();

				// Grab tag collection, use client-side paging and fetch all tags from server at once
				App.Collections.Tags = new TagCollection([], { mode: 'client' });
				App.Collections.Tags.fetch();

				this.homeLayout = new HomeLayout({
					collection : App.Collections.Posts
				});
				App.vent.trigger('views:change', 'full');

				this.modalController = new ModalController({
					modal : this.layout.modal
				});
			},
			//gets mapped to in AppRouter's appRoutes
			index : function()
			{
				App.vent.trigger('page:change', 'posts');
				this.homeLayout.setViews({
					map: true,
					search: true,
					list: true
				});
				App.vent.trigger('views:change', 'full');
				App.Collections.Posts.setFilterParams({}, true);
				this.showHomeLayout();
			},
			postsAll : function()
			{
				App.vent.trigger('page:change', 'posts/all');
				App.Collections.Posts.setFilterParams({
					status : 'all'
				}, true);
				this.showHomeLayout();
			},
			postsUnpublished : function()
			{
				App.vent.trigger('page:change', 'posts/unpublished');
				App.Collections.Posts.setFilterParams({
					status : 'draft'
				}, true);
				this.showHomeLayout();
			},
			postsPublished : function()
			{
				this.index();
			},
			viewsFull : function()
			{
				App.vent.trigger('views:change', 'full');
				this.homeLayout.setViews({
					map: true,
					search: true,
					list: true
				});
				this.showHomeLayout();
			},
			viewsList : function()
			{
				App.vent.trigger('views:change', 'list');
				this.homeLayout.setViews({
					map: false,
					search: true,
					list: true
				});
				this.showHomeLayout();
			},
			viewsMap : function()
			{
				App.vent.trigger('views:change', 'map');
				this.homeLayout.setViews({
					map: true,
					search: true,
					list: false
				});
				this.showHomeLayout();
			},
			showHomeLayout : function()
			{
				if (this.layout.mainRegion.currentView instanceof HomeLayout === false)
				{
					this.layout.mainRegion.show(this.homeLayout);
				}
				this.homeLayout.showRegions();
			},
			postDetail : function(id)
			{
				var that = this,
						postDetailLayout,
						model,
						relatedPosts;

				require(['views/posts/PostDetailLayout', 'views/posts/PostDetailView', 'views/posts/RelatedPostsView', 'views/MapView', 'models/PostModel'],
					function(PostDetailLayout, PostDetailView, RelatedPostsView, MapView, PostModel)
				{
					App.vent.trigger('page:change', 'posts/:id');
					// @TODO find a way to reuse post detail views
					postDetailLayout = new PostDetailLayout();
					that.layout.mainRegion.show(postDetailLayout);

					// @todo improve this to avoid double loading of model (and race conditions)
					//model = App.Collections.Posts.get({id : id});
					model = new PostModel({id: id});
					model.fetch().done(function ()
					{
						model.fetchRelations();

						// If post has tags, load related posts
						if (model.get('tags').length > 0)
						{
							relatedPosts = new PostCollection();
							relatedPosts
								.setPageSize(4, {
									first : true,
									fetch : false,
									data : {
										tags : model.get('tags').join(',')
									}
								})
								.done(function () {
									// Remove current post from the collection
									relatedPosts.remove(model);
									// Remove extra posts if we still have 4 posts..
									relatedPosts.remove(relatedPosts.at(3));
								});
						}
					})
					// Couldn't load post - redirect to homepage
					.fail(function ()
					{
						alertify.error('The post you requested could not be found.');
						App.appRouter.navigate('', { trigger : true });
					});

					// Make sure we have loaded the form and user before we render the post details
					model.relationsCallback.done(function()
					{
						postDetailLayout.postDetailRegion.show(new PostDetailView({
							model: model
						}));

						// If post has tags, show related posts
						if (model.get('tags').length > 0)
						{
							postDetailLayout.relatedPostsRegion.show(new RelatedPostsView({
								collection : relatedPosts,
								model : model
							}));
						}
					});

					postDetailLayout.mapRegion.show(new MapView({
						className : 'map-view post-details-map-view',
						collapsed : true,
						model : model
					}));
				});
			},

			sets : function ()
			{
				var that = this;
				require(['views/sets/SetListView'], function(SetListView)
				{
					App.vent.trigger('page:change', 'sets');
					that.layout.mainRegion.show(new SetListView({
						collection : App.Collections.Sets
					}));
				});
			},

			setDetail : function(/* id */)
			{
				var that = this;
				require(['views/sets/SetDetailView'], function(SetDetailView)
				{
					App.vent.trigger('page:change', 'sets/:id');
					that.layout.mainRegion.show(new SetDetailView());
				});
			},

			users : function()
			{
				var that = this;
				require(['views/users/UserListView'], function(UserListView)
				{
					App.Collections.Users.fetch();

					App.vent.trigger('page:change', 'users');

					that.layout.mainRegion.show(new UserListView({
						collection : App.Collections.Users
					}));
				});
			},

			tags : function()
			{
				var that = this;
				require(['views/tags/TagListView'], function(TagListView)
				{
					App.vent.trigger('page:change', 'tags');
					App.Collections.Tags.fetch();

					that.layout.mainRegion.show(new TagListView({
						collection : App.Collections.Tags
					}));
				});
			},

			// Extra postCreate handler to give us a direct URL to posts/create
			postCreate : function ()
			{
				if (typeof this.layout.mainRegion.currentView === 'undefined')
				{
					this.index();
				}

				this.modalController.postCreate();
			},
			messages : function (view)
			{
				var that = this;
				this.homeLayout.close();
				require(['views/messages/MessageListView', 'collections/MessageCollection'], function(MessageListView, MessageCollection)
				{
					App.vent.trigger('page:change', view ? 'messages/' + view : 'messages');

					App.Collections.Messages = new MessageCollection();
					//App.Collections.Messages.fetch();

					switch (view)
					{
						// Filter by type. Will also default to incoming + received status
						case 'email':
							App.Collections.Messages.fetch({data : {type : 'email'}});
							break;
						case 'sms':
							App.Collections.Messages.fetch({data : {type : 'sms'}});
							break;
						case 'twitter':
							App.Collections.Messages.fetch({data : {type : 'twitter'}});
							break;
						// Filter by archived status. Will also default to incoming only
						case 'archived':
							App.Collections.Messages.fetch({data : {status : 'archived'}});
							break;
						// Show all statuses. Will still default to incoming only
						case 'all':
							App.Collections.Messages.fetch({data : {status : 'all'}});
							break;
						// Grab default: incoming + received + all types
						default:
							App.Collections.Messages.fetch();
							break;
					}

					that.layout.mainRegion.show(new MessageListView({
						collection : App.Collections.Messages
					}));
				});
			},
			apiExplorer : function ()
			{
				// Api Explorer not enabled, show index page
				if (!App.feature('api_explorer'))
				{
					// Go to index page.
					this.index();
					App.appRouter.navigate('', { trigger : true });

					return;
				}

				var that = this;
				require(['views/api-explorer/ApiExplorerView','models/ApiExplorerModel',], function(ApiExplorerView, ApiExplorerModel)
				{
					App.vent.trigger('page:change', 'apiexplorer');
					that.layout.mainRegion.show(new ApiExplorerView({
						model : new ApiExplorerModel()
					}));
				});
			},
			/**
			 * Shows a form listing
			 */
			forms : function ()
			{
				var that = this;
				require(['views/settings/FormList'], function(FormList)
				{
					App.vent.trigger('page:change', 'forms');
					that.layout.mainRegion.show(new FormList({
						collection : App.Collections.Forms
					}));
				});
			},
			/**
			 * Set up data provider layout
			 * @todo refactor to better handle loading dplayout
			 */
			_setupDataProviderLayout : function (DataProviderLayout)
			{
				var that = this,
					dpTypes,
					dpLayout;

				if (! this._dpLayout)
				{
					dpTypes = new Backbone.Collection([
							{ id: 'sms', name: 'SMS', icon: 'mobile' },
							{ id: 'email', name: 'Email', icon: 'envelope-o' },
							{ id: 'twitter', name: 'Twitter', icon: 'twitter' },
							{ id: 'rss', name: 'RSS', icon: 'rss' }
						]);
					dpLayout = new DataProviderLayout({
						collection : dpTypes
					});

					this._dpLayout = dpLayout;
				}

				that.layout.mainRegion.show(this._dpLayout);
				return this._dpLayout;
			},
			/**
			 * Shows a data provider listing
			 */
			messageSettingsMain : function ()
			{
				var that = this;

				if (!App.feature('data_provider_config'))
				{
					App.appRouter.navigate('', { trigger : true });
					return;
				}

				require(['views/settings/DataProviderLayout', 'views/settings/DataProviderList', 'models/ConfigModel'],
					function(DataProviderLayout, DataProviderList, ConfigModel)
				{
					App.vent.trigger('page:change', 'messages/settings');

					var dpConfig = new ConfigModel({'@group': 'data-provider'}),
						dpList = new DataProviderList({
							collection : App.Collections.DataProviders,
							configModel : dpConfig
						}),
						dpLayout = that._setupDataProviderLayout(DataProviderLayout);

					// Grab data-provider config and bind 'enabled'
					dpConfig.fetch().done(function ()
					{
						_.each(dpConfig.get('providers'), function (enabled, index)
						{
							App.Collections.DataProviders.get(index).set('enabled', enabled);
						});
					});

					dpLayout.main.show(dpList);
				});
			},
			/**
			 * Show a config form for an individual data provider
			 * @param  String provider id
			 */
			dataProvidersConfig : function(id)
			{
				var that = this;

				if (!App.feature('data_provider_config'))
				{
					App.appRouter.navigate('', { trigger : true });
					return;
				}

				require(['views/settings/DataProviderLayout', 'views/settings/DataProviderConfig', 'models/ConfigModel'],
					function(DataProviderLayout, DataProviderConfigView, ConfigModel)
				{
					App.vent.trigger('page:change', 'messages/settings');

					var
						dpLayout = that._setupDataProviderLayout(DataProviderLayout),
						dpModel = App.Collections.DataProviders.get(id),
						dpConfig = new ConfigModel({'@group': 'data-provider'});

					dpConfig.fetch().done(function ()
					{
						dpLayout.main.show(new DataProviderConfigView({
							dataProviderModel : dpModel,
							configModel : dpConfig
						}));
					});
				});
			},

			// FIXME: temp controller for sms hard coding
			dataProvidersConfigSMS : function(/*id*/)
			{
				var that = this;

				if (!App.feature('data_provider_config'))
				{
					App.appRouter.navigate('', { trigger : true });
					return;
				}

				require(['views/settings/DataProviderLayout', 'views/settings/DataProviderConfig', 'models/ConfigModel', 'text!templates/settings/DataProviderConfigSms.html', 'handlebars'],
					function(DataProviderLayout, DataProviderConfigView, ConfigModel, template, Handlebars)
				{
					App.vent.trigger('page:change', 'messages/settings');

					var
						dpLayout = that._setupDataProviderLayout(DataProviderLayout),
						dpModel = App.Collections.DataProviders.get('smssync'),
						dpConfig = new ConfigModel({'@group': 'data-provider'});

					dpConfig.fetch().done(function ()
					{
						dpLayout.main.show(new DataProviderConfigView({
							dataProviderModel : dpModel,
							configModel : dpConfig,
							template: Handlebars.compile(template)
						}));
					});
				});
			},
			/**
			 * Show a post wizard for an editing a post form
			 * @param  String form id
			 */
			formEdit : function(id)
			{
				var that = this;
				require(['views/settings/FormEditor', 'views/settings/AvailableAttributeList', 'views/settings/FormAttributeList', 'collections/FormAttributeCollection'],
					function(FormEditor, AvailableAttributeList, FormAttributeList, FormAttributeCollection)
				{
					App.vent.trigger('page:change', 'forms');
					var form = App.Collections.Forms.get(id),
						formEditor = new FormEditor({
							model : form
						}),
						availableAttributes = new FormAttributeCollection([
							{
								label: 'Text',
								input: 'Text',
								type: 'varchar'
							},
							{
								label: 'TextArea',
								input: 'TextArea',
								type: 'text'
							},
							{
								label: 'Number (Decimal)',
								input: 'Number',
								type: 'decimal'
							},
							{
								label: 'Number (Integer)',
								input: 'Number',
								type: 'integer'
							},
							{
								label: 'Select',
								input: 'Select',
								type: 'varchar', // what about numeric selections?
								options: []
							},
							{
								label: 'Radio',
								input: 'Radio',
								type: 'varchar', // not totally sure about this
								options: []
							},
							{
								label: 'Checkbox',
								input: 'Checkbox',
								type: 'varchar' // not totally sure about this
							},
							{
								label: 'Checkboxes',
								input: 'Checkboxes',
								type: 'varchar' // not totally sure about this
							},
							{
								label: 'Date',
								input: 'Date',
								type: 'datetime'
							},
							{
								label: 'DateTime',
								input: 'DateTime',
								type: 'datetime'
							},
							{
								label: 'Location',
								input: 'Location',
								type: 'point'
							}
						]),
						formAttributes = new FormAttributeCollection(_.values(form.formAttributes)),
						formAttributeList = new FormAttributeList({
							collection : formAttributes,
							form_group_id : form.get('groups')[0].id // @todo check this exists
						});

					that.layout.mainRegion.show(formEditor);

					formEditor.formAttributes.show(formAttributeList);
					formEditor.availableAttributes.show(new AvailableAttributeList({
						collection : availableAttributes,
						sortableList : formAttributeList
					}));
				});
			}
	});
});
