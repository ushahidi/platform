define(['App', 'backbone', 'marionette'],
	function(App, Backbone, Marionette)
	{
		return Backbone.Marionette.Region.extend(
		{
			// Override open to trigger foundation reveal
			open : function(view){
				this.$el.empty().append(view.el);
				this.$el.foundation('reveal', 'open');
			}
		});
	});
