/**
 * Form Collection Module
 *
 * @module     FormCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([
		'underscore',
		'backbone',
		'modules/config',
		'mixin/ResultsCollection',
		'models/FormModel'
	],
	function(
		_,
		Backbone,
		config,
		ResultsCollection,
		FormModel
	) {
		// Creates a new Backbone Collection class object
		var FormCollection = Backbone.Collection.extend(
			_.extend(
			{
				model : FormModel,
				url: config.get('apiurl') +'forms'
			},
			// Mixins must always be added last!
			ResultsCollection
		));

		return FormCollection;
	});
