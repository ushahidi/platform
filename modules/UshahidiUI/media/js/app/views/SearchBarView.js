/**
 * Search bar
 *
 * @module     SearchBarView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'handlebars', 'App', 'text!templates/SearchBar.html', 'geocoder', 'geopoint'],
	function(Marionette, Handlebars, App, template, GeocoderJS, GeoPoint)
	{
		var openStreetMapGeocoder = GeocoderJS.createGeocoder('openstreetmap');

		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			collectionEvents : {
				'sync': 'render',
			},
			events:{
				'submit form': 'SearchPosts',
			},
			ui : {
				'tag' : '.js-search-tag',
				'keyword' : '.js-search-keyword',
				'location' : '.js-search-location',
				'set' : '.js-search-set',
				'time' : '.js-search-time'
			},

			serializeData: function()
			{
				var data = {
					tags : this.collection.toJSON()
				};

				return data;
			},

			SearchPosts: function(e)
			{
				e.preventDefault();
				var keyword = this.ui.keyword.val(),
					tag = this.ui.tag.val(),
					location = this.ui.location.val();

				if (location)
				{
					openStreetMapGeocoder.geocode(location, function(result) {
						ddt.log('SearchBar', 'geocoder result', result);
						var
							bbox = null,
							resultPoint,
							bounds;

						if (result.length > 0)
						{
							resultPoint = new GeoPoint(result[0].latitude, result[0].longitude);
							bounds = resultPoint.boundingCoordinates(25, false, true); // Get 50km bounding box
							bbox = [bounds[0].longitude(), bounds[0].latitude(), bounds[1].longitude(), bounds[1].latitude()].join(',');
						}

						App.Collections.Posts.setFilterParams({
							q : keyword,
							tags : tag,
							bbox: bbox
						});
					});
				}
				else
				{
					App.Collections.Posts.setFilterParams({
						q : keyword,
						tags : tag
					});
				}
			}
		});
	});
