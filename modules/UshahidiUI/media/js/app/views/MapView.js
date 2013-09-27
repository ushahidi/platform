define(['marionette', 'handlebars', 'underscore', 'App', 'leaflet', 'util/App.oauth', 'text!templates/Map.html', 'text!templates/Popup.html'],
	function(Marionette, Handlebars, _, App, L, OAuth, template, popupTemplate)
	{
		// Hack to fix default image url
		L.Icon.Default.imagePath = App.config.baseurl + 'media/kohana/images';
		
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			popupTemplate : Handlebars.compile(popupTemplate),
			collapsed : false,
			className : 'mapView',
			/**
			 * Initialize the map view
			 * 
			 * @param <object> options - Configuration object. Possible params:
			 *   collapsed  - Starting 'collapsed' state for the map
			 *   dataURL    - DataURL to load geoJSON from. Takes precedence over model or collection URLs.
			 *   model      - Model to show location data for, used to populate dataURL. Takes precedence over collection URL.
			 *   collection - Collection to show location data for, used to populate dataURL
			 **/
			initialize : function (options)
			{
				// ensure options is an object
				options = _.extend({}, options);
				
				// Should the view start collapsed
				this.collapsed = false;
				if (options.collapsed)
				{
					this.collapsed = true;
				}
				
				// Get data url
				if (typeof options.dataURL !== 'undefined')
				{
					this.dataURL = options.dataURL;
				}
				else if (typeof options.model !== 'undefined')
				{
					this.dataURL = typeof options.model.url === 'function' ? options.model.url() : options.model.url;
					this.dataURL = this.dataURL + (this.dataURL.charAt(this.dataURL.length - 1) === '/' ? '' : '/') + 'geojson';
				}
				else if (typeof options.collection !== 'undefined')
				{
					// @TODO improve this to handle query params, etc
					this.dataURL = typeof options.collection.url === 'function' ? options.collection.url() : options.collection.url;
					this.dataURL = this.dataURL + (this.dataURL.charAt(this.dataURL.length - 1) === '/' ? '' : '/') + 'geojson';
				}
				else
				{
					throw {
						name:    'System Error',
						message: 'Error detected. Could not get dataURL for MapView'
					};
				}
			},

			// Use onDomRefresh rather than render() because we need this.$el in the DOM first
			onDomRefresh: function()
			{
				var that = this,
						osm,
						cloudmadeUrl,
						cloudmadeAttribution,
						minimal,
						map,
						baseMaps,
						overlayMaps,
						posts;
				
				// Don't re-render the map
				if (typeof this.map !== 'undefined') {
					return this;
				}
				
				// add an OpenStreetMap tile layer
				osm = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
					attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
				});
				
				cloudmadeUrl = 'http://{s}.tile.cloudmade.com/528babad266546698317425055510f96/{styleId}/256/{z}/{x}/{y}.png';
				cloudmadeAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade';
				minimal = L.tileLayer(cloudmadeUrl, {styleId: 22677, attribution: cloudmadeAttribution});
				
				// create a map in the 'map' div, set the view to a given place and zoom
				map = this.map = L.map(this.$('#map')[0], {
					center : new L.LatLng(-36.85, 174.78),
					zoom : 5,
					layers : [minimal],
					scrollWheelZoom : false
				});
				
				// Add the posts marker layer
				// @TODO split this out so we can manually update the map layer, without redrawing the map
				posts = L.geoJson([], {
					onEachFeature: function (feature, layer)
					{
						// does this feature have a property named popupContent?
						if (feature.properties && feature.properties.title)
						{
							layer.bindPopup(that.popupTemplate(feature.properties));
						}
					}
				}).addTo(this.map);
				OAuth.ajax({
					url : this.dataURL,
					success: function (data) {
						// If geojson was empty, return
						if (data.features.length == 0) return;

						posts.addData(data);

						// Center map on post markers
						//map.fitBounds(posts.getBounds()).setZoom(5);
						map.panTo(posts.getBounds().getCenter(), { animate: false });
					}
				});
				
				baseMaps = { 'Minimal': minimal };
				overlayMaps = { 'Posts': posts };
				
				L.control.layers(baseMaps, overlayMaps).addTo(this.map);
				
				// Set initial collapsed state
				// @TODO Maybe move this into the view html: set classes when we render
				this.collapseMap(this.collapsed);
				
				// Fix any leaflet weirdness after map resizes
				// @TODO check if this works in older browsers, add backup delayed call if not
				this.$el.on('transitionend', function ()
				{
					that.map.invalidateSize();
				});
				
				return this;
			},
			events : {
				'click .js-collapse-map' : 'collapseMap'
			},
			/**
			 * Toggle map size
			 * 
			 * @param <Boolean> collapse - Set collapsed state rather than toggle (true = collapsed) 
			 **/
			collapseMap : function (collapse)
			{
				if (collapse === true)
				{
					this.collapsed = true;
					this.$('#map').addClass('map-collapse');
					this.$('.js-collapse-tab').addClass('none');
					this.$('.js-expand-tab').removeClass('none');
					this.$('.leaflet-container .leaflet-control-zoom').hide();
				}
				else if (collapse === false)
				{
					this.collapsed = false;
					this.$('#map').removeClass('map-collapse');
					this.$('.js-collapse-tab').removeClass('none');
					this.$('.js-expand-tab').addClass('none');
					this.$('.leaflet-container .leaflet-control-zoom').show();
				}
				else
				{
					this.collapsed = this.collapsed ? false : true;
					this.$('#map').toggleClass('map-collapse');
					this.$('.js-collapse-tab').toggleClass('none');
					this.$('.js-expand-tab').toggleClass('none');
					this.$('.leaflet-container .leaflet-control-zoom').toggle();
				}
				
				return false;
			}
		});
	});