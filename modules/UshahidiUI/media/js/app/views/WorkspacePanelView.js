define(['marionette', 'handlebars', 'App', 'text!templates/WorkspacePanel.html'],
	function(Marionette, Handlebars, App, template)
	{
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			events : {
				'click .js-title' : 'toggleSection',
				'click .workspace-menu li' : 'toggleMenuItem'
			},
			toggleSection : function(e)
			{
				var $el = this.$(e.currentTarget.parentNode);
				$el.toggleClass('active');
				e.preventDefault();
			},
			toggleMenuItem : function(e)
			{
				e.preventDefault();
				this.$('.workspace-menu li').removeClass('active');
				this.$(e.currentTarget).addClass('active');
			}
		});
	});