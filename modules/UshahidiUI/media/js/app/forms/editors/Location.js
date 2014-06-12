define(['underscore', 'handlebars', 'backbone', 'marionette', 'leaflet', 'text!forms/templates/LocationEditor.html', 'text!templates/MapAttribution.html',
	'backbone-forms', 'l.geosearch/l.control.geosearch', 'l.geosearch/l.geosearch.provider.openstreetmap', 'leaflet-locatecontrol'],
	function(_, Handlebars, Backbone, Marionette, L, template, mapAttributionTemplate)
{
	var Location = Backbone.Form.editors.Location = Backbone.Form.editors.Base.extend({
		tagName : 'div',
		template : Handlebars.compile(template),
		baseMaps : function () {
			return {
				'MapQuest': L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {attribution: mapAttributionTemplate, subdomains: '1234'}),
				'MapQuest Aerial': L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.png', {attribution: mapAttributionTemplate, subdomains: '1234'})
			};
		},
		defaultMap : 'MapQuest',
		marker : null,
		defaultValue : {
			lat : -36.85,
			lon : 174.78
		},

		events: {
			'click .map-search-btn' : 'search',
			'keyUp .map-search-field' : 'searchKeyUp',
			'click .geolocate-btn' : 'geolocate'
		},

		initialize : function(options)
		{
			// Call parent constructor
			Backbone.Form.editors.Base.prototype.initialize.call(this, options);

			this.form.on('dom:refresh', this.refreshMap, this);
		},

		render : function()
		{
			var that = this,
				$editor = this.template(_.result(this, 'templateData')),
				baseMaps,
				map;

			this.$el.append($editor);

			// Don't re-render the map
			if (typeof this.map !== 'undefined')
			{
				return this;
			}

			baseMaps = _.result(this, 'baseMaps');
			ddt.log('LocationEditor', 'baseMaps', baseMaps);

			if (!this.value) {
				this.value = {lat: 0, lon: 0};
			}

			// create a map in the 'map' div, set the view to a given place and zoom
			map = this.map = L.map(this.$('.map')[0], {
				center : new L.LatLng(this.value.lat, this.value.lon),
				zoom : 15,
				layers : [baseMaps[this.defaultMap]],
				scrollWheelZoom : false
			});
			// Disable 'Leaflet prefix on attributions'
			map.attributionControl.setPrefix(false);

			this.setValue(this.value);
			this.marker.addTo(map);

			// Update map marker on location found events
			this.map.on('locationfound', function (e)
			{
				that.setValue(e.latlng);
			});

			L.control.layers(baseMaps, {}).addTo(this.map);

			// Add geolocation search control
			this.geosearch = new L.Control.GeoSearch({
				provider: new L.GeoSearch.Provider.OpenStreetMap(),
				zoomLevel : 15
			});
			this.geosearch._positionMarker = this.marker;
			this.geosearch._map = this.map;

			// Add locate control to get user location
			this.locate = new L.Control.Locate({
				setView : true, // not sure we need this here?
				locateOptions : {
					setView : true,
					maxZoom : 15
				}
			}).addTo(this.map);

			return this;
		},

		/**
		 * Override default remove function in order to remove map
		 */
		remove: function() {
			ddt.log('LocationEditor', 'LocationEditor.remove', this.map);
			if (this.map)
			{
				ddt.log('LocationEditor', 'Calling map.remove()');
				this.map.remove();
				delete this.map;
			}

			Backbone.Form.editors.Base.prototype.remove.call(this);
		},

		/**
		 * Returns the data to be passed to the template
		 *
		 * @return {Object}
		 */
		templateData: function()
		{
			//var schema = this.schema;

			return {
				id : this.id,
				name: this.getName()
			};
		},

		getValue: function()
		{
			var latlng = this.marker.getLatLng(),
				label = this.$('#' + this.id + '_label').val();

			return {
				label : label,
				lat : latlng.lat,
				lon : latlng.lng
			};
		},

		setValue: function(value)
		{
			// Handle LatLng object as value, make it match API value object.
			if (value && value.lng)
			{
				value.lon = value.lng;
			}

			if (value && typeof value.lat !== 'undefined' && typeof value.lon !== 'undefined')
			{
				if (this.marker === null)
				{
					this.marker = L.marker([value.lat, value.lon], { draggable : true })
						.addEventListener('dragend', function ()
							{
								this.value = this.getValue();
							}, this);
				}
				else
				{
					this.marker.setLatLng(new L.LatLng(value.lat, value.lon));
				}

				// Center map on post markers
				this.map.panTo(new L.LatLng(value.lat, value.lon));
			}
		},

		refreshMap : function ()
		{
			if (typeof this.map !== 'undefined')
			{
				ddt.log('LocationEditor', 'Calling map.invalidateSize()', this.map);
				this.map.invalidateSize();
			}
		},

		geolocate : function(e)
		{
			e.preventDefault();

			this.map.locate({
				setView : true,
				maxZoom : 15
			});
		},

		searchKeyUp : function(e)
		{
			var enter = 13;

			if (e.keyCode === enter) {
				this.search(e);
			}
		},

		search : function(e)
		{
			e.preventDefault();
			var value = this.$('#' + this.id + '_label').val();

			this.geosearch.geosearch(value);
		}
	});
	return Location;
});
