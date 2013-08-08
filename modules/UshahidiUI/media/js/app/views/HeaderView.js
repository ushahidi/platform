define(['marionette', 'handlebars', 'App', 'text!templates/Header.html', 'text!templates/partials/sets-dropdown-nav.html', 'text!templates/partials/views-dropdown-nav.html'],
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
			events : {
				'click .js-views-menu-link' : 'showViewsMenu',
				'click .js-sets-menu-link' : 'showSetsMenu',
				'click nav[role="sub-navigation"] ul li' : 'toggleSubNav'
			},
			triggers : {
				'click .js-workspace-panel-button, .js-workspace-panel-button-small' : 'workspace:toggle'
			},
			updateActiveNav : function (page)
			{
				this.$('li').removeClass('active');
				this.$('li[data-page="'+page+'"]').addClass('active')
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
			},
			showSetsMenu : function(e)
			{
				// Hide other menu
				this.$('.js-views-menu').removeClass('subnav');
				this.$('.js-views-menu-link').removeClass('active');
				// Toggle this menu
				this.$('.js-sets-menu').toggleClass('subnav');
				this.$('.js-sets-menu-link').toggleClass('active');
				
				e.preventDefault();
			},
			toggleSubNav : function (e)
			{
				console.log($(e.currentTarget))
				var $el = this.$(e.currentTarget);
				$el.siblings().removeClass('active');
				$el.addClass('active');
			}
		});
	}); 