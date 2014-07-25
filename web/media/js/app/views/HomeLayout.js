/**
 * Home Layout
 *
 * @module     HomeLayout
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'modules/config',
'hbs!templates/HomeLayout', 'views/SearchBarView', 'views/MapView', 'views/posts/PostListView'],
	function(App, Marionette, _, config, template, SearchBarView, MapView, PostListView)
	{
		return Marionette.Layout.extend(
		{
			className: 'layout-home',
			template : template,
			regions : {
				mapRegion : '#map-region',
				searchRegion : '#search-bar',
				contentRegion : '#post-list-view'
			},
			views : {
				map : true,
				search : true,
				list : true
			},
			initialize : function()
			{
				// Resize map after searchRegion renders
				this.searchRegion.on('show', this.updateMap, this);
			},
			/**
			 * Set which views should be shown
			 * @param {Object} views Views to render. Set key to true to render.
			 *                       Possible keys are map, search, list.
			 */
			setViews : function(views)
			{
				_.extend(this.views, views);
				ddt.log('HomeLayout', 'set views', views);
				return this;
			},
			/**
			 * Render the regions specified through setViews().
			 * This should be called after the layout is rendered in the DOM.
			 */
			showRegions : function()
			{
				ddt.log('HomeLayout', 'showRegions');
				if (this.mapRegion.currentView instanceof MapView === false && this.views.map)
				{
					ddt.log('HomeLayout', 'showMap');
					this.mapRegion.show(new MapView({
						collection : this.collection,
						clustering : config.get('map').clustering,
						defaultView : config.get('map').default_view,
						fullSizeMap : (! this.views.list)
					}));
				}
				else if(! this.views.map)
				{
					ddt.log('HomeLayout', 'closingMap');
					this.mapRegion.close();
				}
				// Map already visible
				else
				{
					this.mapRegion.currentView.fullSizeMap = (! this.views.list);
					this.mapRegion.currentView.resizeMap();
				}

				if (this.contentRegion.currentView instanceof PostListView === false && this.views.list)
				{
					ddt.log('HomeLayout', 'showList');
					this.contentRegion.show(new PostListView({
						collection: this.collection
					}));
				}
				else if(! this.views.list)
				{
					ddt.log('HomeLayout', 'closeList');
					this.contentRegion.close();
				}

				if (this.searchRegion.currentView instanceof SearchBarView === false && this.views.search)
				{
					ddt.log('HomeLayout', 'showSearch');
					this.searchRegion.show(new SearchBarView({
						collection : App.Collections.Posts,
						tags : App.Collections.Tags
					}));
				}
				else if(! this.views.search)
				{
					ddt.log('HomeLayout', 'closeSearch');
					this.searchRegion.close();
				}
				return this;
			},
			onClose : function()
			{
				ddt.log('HomeLayout', 'onClose');
			},
			onShow : function()
			{
				ddt.log('HomeLayout', 'onShow');
			},
			updateMap : function ()
			{
				if (this.mapRegion.currentView instanceof MapView)
				{
					this.mapRegion.currentView.resizeMap();
				}
			}

		});
	});
