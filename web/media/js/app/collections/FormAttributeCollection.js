/**
 * Form Attribute Collection Module
 *
 * @module     FormAttributeCollection
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([
		'underscore',
		'backbone',
		'modules/config',
		'mixin/ResultsCollection',
		'models/FormAttributeModel'
	],
	function(
		_,
		Backbone,
		config,
		ResultsCollection,
		FormAttributeModel
	) {
		var FormAttributeCollection = Backbone.Collection.extend(
			_.extend(
			{
				model: FormAttributeModel,
				url: config.get('apiuri') +'/attributes',
				comparator: 'priority'
			},
			// Mixins must always be added last!
			ResultsCollection
		));

		return FormAttributeCollection;
	});
