define(['underscore', 'handlebars', 'backbone', 'marionette', 'leaflet', 'text!forms/templates/LocationEditor.html',
	'backbone-forms', 'l.geosearch/l.control.geosearch', 'l.geosearch/l.geosearch.provider.openstreetmap', 'leaflet-locatecontrol/L.Control.Locate'],
	function(_, Handlebars, Backbone, Marionette, L, template)
{
	var Location = Backbone.Form.editors.Location = Backbone.Form.editors.Base.extend({
		tagName : 'div',
		template : Handlebars.compile(template),
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
					osm,
					cloudmadeUrl,
					cloudmadeAttribution,
					minimal,
					map;

			//this.setElement($editor);
			this.$el.append($editor);
			//this.setValue(this.value);

			// Don't re-render the map
			if (typeof this.map !== 'undefined')
			{
				return this;
			}

			// add an OpenStreetMap tile layer
			osm = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a>'
			});

			cloudmadeUrl = 'http://{s}.tile.cloudmade.com/528babad266546698317425055510f96/{styleId}/256/{z}/{x}/{y}.png';
			cloudmadeAttribution =
				'<span class="hide-for-medium-down">Map data &copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors, Imagery &copy; <a href="http://cloudmade.com">CloudMade</a></span>' +
				'<span class="show-for-medium-down">&copy; <a href="http://osm.org/copyright">OpenStreetMap</a>, &copy; <a href="http://cloudmade.com">CloudMade</a></span>';
			minimal = L.tileLayer(cloudmadeUrl, {styleId: 22677, attribution: cloudmadeAttribution});

			// create a map in the 'map' div, set the view to a given place and zoom
			map = this.map = L.map(this.$('.map')[0], {
				center : new L.LatLng(this.value.lat, this.value.lon),
				zoom : 15,
				layers : [minimal],
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
			if (value.lng)
			{
				value.lon = value.lng;
			}

			if (value.lat && value.lon)
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