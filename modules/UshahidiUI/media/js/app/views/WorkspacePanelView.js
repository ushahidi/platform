define(['marionette', 'handlebars', 'App', 'text!templates/WorkspacePanel.html'],
	function(Marionette, Handlebars, App, template, setsDropdown, viewsDropdown) {
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			initialize: function() {
			},
			events : {
				'click .js-title' : 'toggleSection',
				'click .workspace-menu > section .js-content' : 'toggleMenuItem'
			},
			toggleSection : function(e) {
				var $el = this.$(e.currentTarget.parentNode);
				$el.toggleClass('active');
				e.preventDefault();
			},
			toggleMenuItem : function(e) {
				e.preventDefault();
				this.$('.js-content').removeClass('active');
				$(e.currentTarget).addClass('active');
			}
		});
	}); 