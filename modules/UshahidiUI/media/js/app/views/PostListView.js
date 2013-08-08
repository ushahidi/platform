define(['App', 'marionette', 'handlebars', 'views/PostItemView',
	'text!templates/PostList.html', 'App.oauth', 'models/PostModel'],
	function( App, Marionette, Handlebars, PostItemView,
		template, OAuth, PostModel)
	{
		//CollectionView provides some default rendering logic
		return Marionette.CompositeView.extend( {
			//Template HTML string
			template: Handlebars.compile(template),
			initialize: function(params) {
			},
			
			itemView: PostItemView,
			itemViewOptions: {
				//foo: "bar"
			},

			itemViewContainer: '.list-view-posts-list',
			
			events: {
				'click .js-list-view-select-post' : 'showHideBulkActions'
			},
			
			showHideBulkActions : function ()
			{
				$checked = this.$('.js-list-view-select-post input[type="checkbox"]:checked')
				if ($checked.length > 0)
				{
					this.$('.js-list-view-bulk-actions').removeClass('hidden');
					this.$('.js-list-view-bulk-actions').addClass('visible');
				}
				else
				{
					this.$('.js-list-view-bulk-actions').removeClass('visible');
					this.$('.js-list-view-bulk-actions').addClass('hidden');
				}
			}
		});
	});
