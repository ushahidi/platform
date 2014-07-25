/**
 * Search bar
 *
 * @module     SearchBarView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'underscore', 'jquery', 'App', 'hbs!templates/SearchBar', 'hbs!templates/partials/tag-with-icon', 'geocoder', 'geopoint', 'URI', 'datetimepicker', 'select2'],
	function(Marionette, _, $, App, template, tagWithIcon, GeocoderJS, GeoPoint, URI)
	{
		var openStreetMapGeocoder = GeocoderJS.createGeocoder('openstreetmap');

		return Marionette.ItemView.extend(
		{
			template : template,
			events: {
				'submit form': 'SearchPosts',
			},
			ui : {
				tag : '.js-search-tag',
				keyword : '.js-search-keyword',
				location : '.js-search-location',
				set : '.js-search-set',
				timeFrom : '.js-search-time-from',
				timeTo : '.js-search-time-to'
			},

			initialize : function (options)
			{
				_.bindAll(this, 'formatTagSelectChoice');

				this.tags = options.tags;

				this.listenTo(this.tags, 'sync', this.render);
				this.listenTo(this.collection, 'filter:change', this.render);
			},

			serializeData: function()
			{
				var data = {
					tags : this.tags.toJSON(),
					state : this.collection.getFilterParams()
				};
				return data;
			},

			formatTagSelectChoice: function (tag)
			{
				if (! tag.id)
				{
					return tag.text;
				}

				var model = this.tags.get(tag.id);

				if (! model)
				{
					return tag.text;
				}

				return tagWithIcon(model.toJSON());
			},

			onDomRefresh: function ()
			{
				this.ui.timeFrom.datetimepicker();
				this.ui.timeTo.datetimepicker();

				this.ui.tag.select2({
					allowClear: true,
					formatResult: this.formatTagSelectChoice,
					formatSelection: this.formatTagSelectChoice,
					escapeMarkup: function(m) { return m; }
				});
			},

			onClose : function ()
			{
				this.ui.tag.select2('destroy');
				this.ui.timeTo.datetimepicker('destroy');
				this.ui.timeFrom.datetimepicker('destroy');
			},

			SearchPosts: function(e)
			{
				e.preventDefault();
				var keyword = this.ui.keyword.val(),
					tag = this.ui.tag.val(),
					location = this.ui.location.val(),
					timeFrom = this.ui.timeFrom.val(),
					timeTo = this.ui.timeTo.val(),
					dfd = $.Deferred(),
					bbox = null;

				if (location)
				{
					openStreetMapGeocoder.geocode(location, function(result) {
						ddt.log('SearchBar', 'geocoder result', result);
						var
							resultPoint,
							bounds;

						if (result.length > 0)
						{
							resultPoint = new GeoPoint(result[0].latitude, result[0].longitude);
							bounds = resultPoint.boundingCoordinates(25, false, true); // Get 50km bounding box
							bbox = [bounds[0].longitude(), bounds[0].latitude(), bounds[1].longitude(), bounds[1].latitude()].join(',');
						}
						dfd.resolve();
					});
				}
				else
				{
					dfd.resolve();
				}

				// Set filter params once any preprocessing is done
				dfd.done(function () {
					var uri = new URI('posts');

					App.Collections.Posts.setFilterParams({
						q : keyword,
						tags : tag,
						created_after: timeFrom,
						created_before: timeTo,
						bbox: bbox,
					});

					uri
						.search(App.Collections.Posts.getFilterParams())
						.addSearch({ location: location });

					App.appRouter.navigate(uri.toString());
				});
			}
		});
	});
