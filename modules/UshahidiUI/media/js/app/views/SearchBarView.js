define(['marionette', 'handlebars', 'App', 'text!templates/SearchBar.html'],
	function(Marionette, Handlebars, App, template) {
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			initialize: function() {
			},
			events : {
				'click #collapse-map' : 'collapseMap'
			},
			// @todo move this into MapView somehow
			collapseMap : function () {
				$('#map').toggleClass('map-collapse');
				console.log('You clicked #collapse-map');
				this.$('#collapse-tab').text(this.$('#collapse-tab').text() == 'collapse map' ? 'expand map' : 'collapse map');
				this.$('.leaflet-container .leaflet-control-zoom').toggle();
				return false;
			}
		});
	}); 