define(['marionette', 'handlebars', 'App', 'text!templates/Header.html', 'text!templates/partials/views-dropdown-nav.html'],
	function(Marionette, Handlebars, App, template, viewsDropdown)
	{
		// Hacky - make sure we register partials before we call compile
		Handlebars.registerPartial('views-dropdown-nav', viewsDropdown);
		
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			initialize: function() {
				// @todo update this for real UI
				App.vent.on('page:change', this.updateActiveNav, this);
			},
			events : {
				'click .js-views-menu-link' : 'showViewsMenu'
			},
			triggers : {
				'click .js-workspace-panel-button, .js-workspace-panel-button-small' : 'workspace:toggle'
			},
			updateActiveNav : function (page)
			{
				// De-activate all nav items
				this.$('nav[role="sub-navigation"]').removeClass('subnav');
				this.$('nav[role="sub-navigation"] ul li').removeClass('active');
				this.$('.js-sets-menu, .js-views-menu').removeClass('active');
				
				// Active current
				var pageSegments = page.split('/');
				// Ignore first segment for now
				
				if (pageSegments[0] === 'index')
				{
					this.$('.views-full').addClass('active');
				}
				else if (pageSegments[0] === 'views' && typeof pageSegments[1] !== 'undefined')
				{
					this.$('.views-'+pageSegments[1]).addClass('active');
				}
			},
			showViewsMenu : function(e)
			{
				// Hide other menu
				this.$('.js-sets-menu').removeClass('subnav');
				this.$('.js-sets-menu-link').removeClass('active');
				// Toggle this menu
				this.$('.js-views-menu').toggleClass('subnav');
				this.$('.js-views-menu-link').toggleClass('active');
				
				e.preventDefault();
			}
		});
	});