define(['App', 'marionette', 'handlebars', 'views/PostItemView', 'text!templates/RelatedPosts.html', 'text!templates/RelatedPostItem.html'],
	function( App, Marionette, Handlebars, PostItemView, template, itemTemplate)
	{
		var itemTemplateCpl = Handlebars.compile(itemTemplate);
		return Marionette.CompositeView.extend( {
			template: Handlebars.compile(template),
			itemView: PostItemView,
			itemViewContainer: '.related-posts-body',
			itemViewOptions: {
				template : itemTemplateCpl,
				className: 'related-post-module-wrapper'
			}
		});
	});
