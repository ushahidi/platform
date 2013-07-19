define(['marionette', 'handlebars', 'App', 'leaflet', 'App.oauth'],
	function(Marionette, Handlebars, App, L, OAuth) {
		// Hack to fix default image url
		L.Icon.Default.imagePath = "/media/kohana/images";
		
		return Marionette.ItemView.extend(
		{
			// Blank template just so it renders
			template : Handlebars.compile(""),
			initialize: function(options) {
			},
			id: 'map',
			className: 'map',
			// Use onDomRefresh rather than render() because we need this.$el in the DOM first
			onDomRefresh: function()
			{
				// Don't re-render the map
				if (typeof this.map != "undefined") return this;
				
				// add an OpenStreetMap tile layer
				var osm = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
					attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
				});
				
				var cloudmadeUrl = 'http://{s}.tile.cloudmade.com/528babad266546698317425055510f96/{styleId}/256/{z}/{x}/{y}.png',
					cloudmadeAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade';
				
				var minimal   = L.tileLayer(cloudmadeUrl, {styleId: 22677, attribution: cloudmadeAttribution}),
					midnight  = L.tileLayer(cloudmadeUrl, {styleId: 999,   attribution: cloudmadeAttribution}),
					motorways = L.tileLayer(cloudmadeUrl, {styleId: 46561, attribution: cloudmadeAttribution});
				
				//this.$el.html(this.template(this.model.attributes));
				// create a map in the "map" div, set the view to a given place and zoom
				var map = this.map = L.map(this.$el[0], {
					center : new L.LatLng(-36.85, 174.78),
					zoom : 5,
					layers : [osm, minimal, midnight]
				});
				
				var baseMaps = {
					"Minimal": minimal,
					"Night View": midnight,
					"OSM Mapnik": osm
				};
				
				posts = L.geoJson().addTo(this.map);
				OAuth.ajax({
					url : '/api/v2/posts/geojson',
					success: function (data) { posts.addData(data); }
				});
				
				var overlayMaps = {
					"Motorways": motorways,
					"Posts": posts
				};
				
				L.control.layers(baseMaps, overlayMaps).addTo(this.map);
				
				// add a marker in the given location, attach some popup content to it and open the popup
				L.marker([-36.85, 174.78]).addTo(this.map)
					.bindPopup('A pretty CSS3 popup. <br> Easily customizable.')
					.openPopup();
				return this;
			}
		});
	}); 