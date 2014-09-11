/**
 * Map Settings
 *
 * @module     Data Visualizations View
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'marionette', 'jquery', 'underscore', 'highstock',
		'hbs!templates/data-visualizations/DataVisualizationsView'
		],
	function( Marionette, $, _,
		template
		)
	{
		return Marionette.CompositeView.extend(
		{
			template: template,
			modelName: 'tags'
		});

	});