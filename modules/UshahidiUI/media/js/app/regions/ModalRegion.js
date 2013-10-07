define(['App', 'backbone', 'marionette'],
	function(App, Backbone, Marionette)
	{
		return Marionette.Region.extend(
		{
			// Override open to trigger foundation reveal
			onShow : function()
			{
				this.$el.foundation('reveal', 'open');
			},
			onClose : function()
			{
				this.$el.foundation('reveal', 'close');
			}
		});
	});
