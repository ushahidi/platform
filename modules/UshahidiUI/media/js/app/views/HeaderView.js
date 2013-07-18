define(['marionette', 'handlebars', 'App', 'text!templates/header.html'],
	function(Marionette, Handlebars, App, template) {
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			initialize: function() {
				App.vent.on("page:change", this.updateActiveNav, this);
			},
			triggers : {
				
			},
			updateActiveNav : function (page)
			{
				this.$('li').removeClass('active');
				this.$('li[data-page="'+page+'"]').addClass('active')
			}
		});
	}); 