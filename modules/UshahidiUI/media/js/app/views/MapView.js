/**
 * Map View
 *
 * @module     MapView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

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
			className : 'map-view',
			modelEvents : {
			  'sync': 'updateMarkers'
			},
			collectionEvents : {
				'filter:change' : 'updateMarkers'
			},
			events : {
				'click .js-collapse-map' : 'collapseMap'
			},
			/**
			 * Initialize the map view
			 *
			 * @param <object> options - Configuration object. Possible params:
			 *   collapsed  - Starting 'collapsed' state for the map
			 *   dataUrl    - Data Url to load geoJSON from. Takes precedence over model or collection URLs.
			 *   model      - Model to show location data for, used to populate dataUrl. Takes precedence over collection URL.
			 *   collection - Collection to show location data for, used to populate dataUrl
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

				// Save custom dataUrl to view object
				if (typeof options.dataUrl !== 'undefined')
				{
					this.dataUrl = options.dataUrl;
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
				if (this.map instanceof L.map)
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
				map = this.map = L.map(this.$('#map')[0], {
					center : new L.LatLng(-36.85, 174.78),
					zoom : 5,
					layers : [minimal],
					scrollWheelZoom : false
				});
				// Disable 'Leaflet prefix on attributions'
				map.attributionControl.setPrefix(false);

				// Add the posts marker layer
				// @TODO split this out so we can manually update the map layer, without redrawing the map
				posts = this.posts = L.geoJson([], {
					onEachFeature: function (feature, layer)
					{
						// does this feature have a property named popupContent?
						if (feature.properties && feature.properties.title)
						{
							layer.bindPopup(that.popupTemplate(feature.properties));
						}
					}
				}).addTo(this.map);

				this.updateMarkers();

				baseMaps = { 'Minimal': minimal };
				overlayMaps = { 'Posts': posts };

				L.control.layers(baseMaps, overlayMaps).addTo(this.map);

				// Set initial collapsed state
				// @TODO Maybe move this into the view html: set classes when we render
				this.collapseMap(this.collapsed);

				// Fix any leaflet weirdness after map resizes
				// @TODO check if this works in older browsers, add backup delayed call if not
				this.$el.on('transitionend', function (e)
				{
					// Make sure we only trigger this on size change for the actual map div
					if ((e.originalEvent.target && e.originalEvent.target.id === 'map') ||
							(e.originalEvent.originalTarget && e.originalEvent.originalTarget.id === 'map'))
					{
						that.map.invalidateSize();
					}
				});

				return this;
			},

			onClose : function()
			{
				if (this.map instanceof L.map)
				{
					this.map.remove();
					delete this.map;
				}
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
			},

			getDataUrl : function()
			{
				var dataUrl;

				// Get data url
				if (typeof this.dataUrl !== 'undefined')
				{
					dataUrl = this.dataUrl;
				}
				else if (typeof this.model !== 'undefined')
				{
					dataUrl = typeof this.model.url === 'function' ? this.model.url() : this.model.url;
					dataUrl = dataUrl + (dataUrl.charAt(dataUrl.length - 1) === '/' ? '' : '/') + 'geojson';
				}
				else if (typeof this.collection !== 'undefined')
				{
					// @TODO improve this to handle query params, etc
					dataUrl = typeof this.collection.url === 'function' ? this.collection.url() : this.collection.url;
					dataUrl = dataUrl + (dataUrl.charAt(dataUrl.length - 1) === '/' ? '' : '/') + 'geojson';
				}
				else
				{
					throw {
						name:    'System Error',
						message: 'Error detected. Could not get dataUrl for MapView'
					};
				}

				return dataUrl;
			},

			/**
			 * Reload map markers from the server and add to map
			 */
			updateMarkers : function ()
			{
				var map = this.map,
					posts = this.posts;

				OAuth.ajax({
					url : this.getDataUrl(),
					success: function (data) {
						// If geojson was empty, return
						if (data.features.length === 0)
						{
							return;
						}

						posts.clearLayers();
						posts.addData(data);

						// Center map on post markers
						map.fitBounds(posts.getBounds());
						// Avoid zooming further than 15 (particularly when we just have a single point)
						if (map.getZoom() > 15)
						{
							map.setZoom(15);
						}
					},
					data : (typeof this.collection !== 'undefined') ? this.collection.getFilterParams() : {}
				});
			}
		});
	});