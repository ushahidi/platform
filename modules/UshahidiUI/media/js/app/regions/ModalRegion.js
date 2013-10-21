define(['App', 'backbone', 'marionette', 'underscore'],
	function(App, Backbone, Marionette, _)
	{
		return Marionette.Region.extend(
		{
			// Override open to trigger foundation reveal
			onShow : function(view)
			{
				this.$el.foundation('reveal', 'open')
					.on('open', function (e) { view.trigger('modal:open', e); })
					.on('opened', function (e) { view.trigger('modal:opened', e); })
					.on('close', function (e) { view.trigger('modal:close', e); })
					.on('closed', function (e) { view.trigger('modal:closed', e); });
			},
			onClose : function()
			{
				this.$el.foundation('reveal', 'close')
					.off('open')
					.off('opened')
					.off('close')
					.off('closed');
			}
		});
	});
