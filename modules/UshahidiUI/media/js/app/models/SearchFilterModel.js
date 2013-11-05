/**
 * Search Filter Model
 *
 * @module     SearchFilterModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone'],
	function($, Backbone) {
		// Creates a new Backbone Model class object
		var SearchFilterModel = Backbone.Model.extend(
		{
			// Default values for all of the Model attributes
			defaults :
			{
					'keywords': null,
					'locations': null,
					'category': null
			}
		});
	
		return SearchFilterModel;
	});