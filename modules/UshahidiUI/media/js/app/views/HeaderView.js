define(['marionette', 'handlebars', 'App', 'text!templates/header.html', 'text!templates/partials/sets-dropdown-nav.html', 'text!templates/partials/views-dropdown-nav.html'],
	function(Marionette, Handlebars, App, template, setsDropdown, viewsDropdown) {
		// Hacky - make sure we register partials before we call compile
		Handlebars.registerPartial('views-dropdown-nav', viewsDropdown);
		Handlebars.registerPartial('sets-dropdown-nav', setsDropdown);
		
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			initialize: function() {
				// @todo update this for real UI
				//App.vent.on("page:change", this.updateActiveNav, this);
			},
			triggers : {
				'click .js-workspace-panel-button, .js-workspace-panel-button-small' : 'workspace:toggle'
			},
			updateActiveNav : function (page)
			{
				this.$('li').removeClass('active');
				this.$('li[data-page="'+page+'"]').addClass('active')
			}
		});
	}); 