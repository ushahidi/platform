/**
 * Map Settings
 *
 * @module     Data Visualizations View
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([
		'marionette',
		'App',
		'modules/config',
		'hbs!templates/data-visualizations/DataVisualizationsView',
		'underscore',
		'highstock'
	], function (
		Marionette,
		App,
		config,
		template,
		_
	) {
		return Marionette.ItemView.extend(
		{
			template: template,
			initialize: function() {
				var that = this;

				this.published = [];
				this.pending = [];
				this.draft = [];

				App.oauth.ajax({
					url : config.get('apiurl') + 'stats/posts',
					success: function (data) {
						_.each(data, function(counts, day) {
							_.each(counts, function(total, type) {
								that[type].push([day * 1000, total]);
							});
						});
						that.onDomRefresh();
						ddt.log('Viz', 'raw posts data', data, that.published, that.pending, that.draft);
					}
				});
			},
			onDomRefresh : function()
			{
				// Cannot use `this.$` because it is loaded before highcharts is available,
				// which will prevent `$.highcharts` from being defined.
				this.$('#viz-posts-over-time').highcharts('StockChart', {
					colors: [
						// colors taken from the palette on this site: http://www.electionguide.org/map/
						'#E9322D', '#46A546', '#2C81BA' , '#EC7063', '#FBD8DB', '#666'
					],
					rangeSelector: {
					// defaults to most recent time for filter (so, 3M=3months from last date)
						enabled: true,
						buttons: [{
							type: 'month',
							count: 1,
							text: '1M'
						}, {
							type: 'month',
							count: 3,
							text: '3M'
						}, {
							type: 'year',
							count: 1,
							text: '1Y'
						}, {
							type: 'all',
							text: 'All'
						}],
						buttonSpacing: 2,
						buttonTheme: {
							stroke: 2,
							r: 5,
							style: {
								color: 'E9322D'
							},
							states: {
								hover: {
									fill: '#FBD8DB'
								},
								select: {
									fill: '#E9322D',
									style: {
										color: 'white'
									}
								}
							}
						},
						inputBoxBorderColor: '#EC7063'
					},
					credits: {
						enabled: false
					},
					title: {
						text: 'Ushahidi Report Counts',
						style: {
							'color': '#E9322D'
						}
					},
					xAxis: {
						type: 'datetime'
						//reversed: _('axisOpposite')
					},
					yAxis: {
						title: {
							align: 'middle',
							text: 'Number of Reports',
							style: {
								'color': '#EC7063'
							}
						},
						opposite: 'axisOpposite'
					},
					legend: {
						enabled: true
					},
					navigator: {
						series: {
							color: '#FBD8DB'
						}
					},
					scrollbar: {
						barBackgroundColor: '#EC7063',
						barBorderRadius: 5,
						buttonBackgroundColor: '#EC7063',
						buttonBorderRadius: 5
					},
					tooltip: {
							pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
							valueDecimals: 0
					},
					series: [{
						name: 'Published',
						data: this.published
					}, {
						name: 'Pending',
						data: this.pending
					}, {
						name: 'Draft',
						data: this.draft
					}]
				});
			}
		});
	});
