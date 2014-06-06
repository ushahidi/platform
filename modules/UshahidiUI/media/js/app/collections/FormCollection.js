/**
 * Form Collection Module
 *
 * @module     FormCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'models/FormModel', 'modules/config'],
	function(Backbone, FormModel, config)
	{
		// Creates a new Backbone Collection class object
		var FormCollection = Backbone.Collection.extend(
		{
			model : FormModel,
			url: config.get('apiurl') +'/forms',
			// The Ushahidi API returns models under 'results'.
			parse: function(response)
			{
				return response.results;
			}
		});

		return FormCollection;
	});
