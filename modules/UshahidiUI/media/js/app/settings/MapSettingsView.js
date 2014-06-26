/**
 * Map Settings
 *
 * @module     MapSettingsView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'marionette', 'jquery', 'underscore',
		'modules/config',
		'hbs!settings/MapSettings',
		'views/MapView',
		'geocoder',
		'jquery.nouislider'
		],
	function( Marionette, $, _,
		config,
		template,
		MapView,
		GeocoderJS
		)
	{
		var openStreetMapGeocoder = GeocoderJS.createGeocoder('openstreetmap'),
			mapBaseLayers = [
				'MapQuest',
				'MapQuest Aerial',
				'Humanitarian OSM'
			];

		return Marionette.Layout.extend( {
			template: template,
			ui : {
				'defaultZoomSlider' : '.default-zoom-slider',
				'defaultZoom' : '#default-zoom-level',
				'defaultLocation' : '.js-default-location',
				'clusterReportsInput' : '.js-cluster-reports-input',
				'baseLayer' : '.js-base-layer'
			},
			regions : {
				'map' : '.js-map-region'
			},
			events : {
				'submit form' : 'formSubmit',
				'blur @ui.defaultLocation' : 'geocodeDefaultLocation',
				'change @ui.clusterReportsInput' : 'updateClustering',
				'change @ui.baseLayer' : 'updateBaseLayer'
			},
			initialize: function(options) {
				this.postCollection = options.postCollection;

				// Clone model to avoid unsaved updates effecting rest of the UI
				this.state = _.clone(this.model);
				this.state.default_view = _.clone(this.state.default_view); // workaround shallow cloning
				this.state.default_view.fitDataOnMap = false;
			},
			onDomRefresh: function() {
				var that = this,
					customToolTip = $.Link({
						target: '-tooltip-<div class="default-zoom-slider-tooltip"></div>',
						format: {
							decimals: 0
						},
						method: function ( value ) {
							value = Math.round(value);
							$(this).html(
								'<span>' + value + '</span>' +
								'<span class="nub"></span>'
							);
						}
					});

				this.slider = this.ui.defaultZoomSlider.noUiSlider({
					start: [this.model.default_view.zoom],
					step: 1,
					connect: 'lower',
					range: {
						'min': 0,
						'max': 18
					},
					serialization: {
						lower: [
							customToolTip
						]
					}
				}).on('set', function () { that.updateZoom(); } );

				this.ui.baseLayer.val(this.model.default_view.baseLayer);
			},
			onShow : function ()
			{
				this.showMap();
			},
			showMap : function ()
			{
				var that = this;
				// This view is tightly coupled to MapView so it makes sense to create it here
				this.mapView = new MapView({
					clustering : this.state.clustering,
					defaultView : this.state.default_view,
					collection : this.postCollection,
					collapsed : 'disabled'
				});
				this.map.show(this.mapView);

				this.mapView.map
					.on('zoomend', function () {
						that.ui.defaultZoomSlider.val(that.mapView.map.getZoom(), { update: false, set: false });
					})
					.on('moveend', function () {
						var center = that.mapView.map.getCenter();
						that.state.default_view.lat = center.lat;
						that.state.default_view.lon = center.lng;
					});
			},
			onClose : function ()
			{
				// Destroy slider
				if (this.slider)
				{
					this.slider.each( function () { this.destroy(); });
				}
			},
			serializeData : function()
			{
				return {
					map : this.model,
					mapBaseLayers : mapBaseLayers
				};
			},
			formSubmit : function(e)
			{
				e.preventDefault();

				var group = 'map',
					hash = {},
					center = this.mapView.map.getCenter();

				// Manually constructing hash since we have to grab values from sliders and map
				hash.clustering = (this.$('.js-cluster-reports-input:checked').val() === '1');
				hash.default_view = {
					baseLayer : this.ui.baseLayer.val(),
					zoom : parseInt(this.ui.defaultZoomSlider.val(), 10),
					lat : center.lat,
					lon : center.lng
				};

				ddt.log('MapSettings', 'update', group, hash);
				config.set(group, hash);
			},

			geocodeDefaultLocation: function ()
			{
				var that = this,
					location = this.ui.defaultLocation.val();

				if (location)
				{
					ddt.log('MapSettings', 'location', location);
					openStreetMapGeocoder.geocode(location, function(result) {
						ddt.log('MapSettings', 'geocoder result', result);

						if (result.length > 0)
						{
							that.mapView.map.panTo([result[0].latitude, result[0].longitude]);
							that.state.default_view.lat = result[0].latitude;
							that.state.default_view.lon = result[0].longitude;
						}
					});
				}
			},

			updateClustering : function ()
			{
				// Update clustering in model
				this.state.clustering = (this.$('.js-cluster-reports-input:checked').val() === '1');

				// Re-render the mapView
				// @todo allow changing clustering without a full re-render
				this.map.close();
				this.showMap();
			},

			updateZoom : function ()
			{
				// Update map zoom to match
				var zoom = this.ui.defaultZoomSlider.val();
				this.mapView.map.setZoom(zoom);
				this.state.default_view.zoom = zoom;
			},

			updateBaseLayer : function()
			{
				// Update base layer in model
				this.state.default_view.baseLayer = this.ui.baseLayer.val();

				// Re-render the mapView
				// @todo allow changing base layer without a full re-render
				this.mapView.map.off('zoomend');
				this.map.close();
				this.showMap();
			}

		});
	});
