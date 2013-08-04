define( [ 'App', 'marionette', 'handlebars', 'views/PostItemView',
'text!templates/postlist.html', 'App.oauth', 'models/PostModel'],
	function( App, Marionette, Handlebars, PostItemView, template,
	OAuth,PostModel) {
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

			itemViewContainer: '.posts',
			
			events: {
			
			},
			
		
			onDomRefresh: function()
			{
				this.collection.fetch({});
				return this;
			}

			
		});
	});
