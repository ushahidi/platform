/**
 * Form Group Collection Module
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
		'models/FormGroupModel'
	],
	function(
		_,
		Backbone,
		config,
		ResultsCollection,
		FormGroupModel
	) {
		var FormGroupCollection = Backbone.Collection.extend(
			_.extend(
			{
				model: FormGroupModel,
				url: function() {
					return config.get('apiurl') + 'forms/' + this.form_id + '/groups';
				},
				comparator: 'priority',
				initialize : function (models, options)
				{
					this.form_id = options.form_id;
				}
			},
			// Mixins must always be added last!
			ResultsCollection
		));

		return FormGroupCollection;
	});
