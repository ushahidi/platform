/**
 * Ushahidi Backbone Forms Setup
 *
 * @module     App
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'backbone-validation', 'backbone-forms', 'forms/templates/FormTemplates', 'forms/editors/Location', 'forms/editors/ReadOnlyText', 'bf/editors/list'],
	function(_, BackboneValidation, BackboneForm)
	{
		// Hack - Don't want modals within modals for lists of object
		BackboneForm.editors.List.Object = BackboneForm.editors.Object;

		_.extend(BackboneValidation.validators, {
			validateArray : function (value, attr, validators, model /*, computed*/)
			{
				if (typeof value !== 'object')
				{
					return this.format('{0} must be an object', this.formatLabel(attr, model));
				}

				// Reduces the array of values to an error message by
				// calling validators on each value and returning
				// the first error message, if any.
				return _.reduce(value, function (memo, v, k) {
					// Reduces the array of validators to an error message by
					// applying all the validators and returning the first error
					// message, if any.
					return _.reduce(validators, function(memo, vconf, validator)
					{
						var result = this[validator].call(this, v.value, attr+'.'+k, vconf, model);

						if(result === false || memo === false) {
							return false;
						}
						if (result && !memo) {
							return _.result(vconf, 'msg') || result;
						}
						return memo;
					}, '', this);
				}, '', this);
			}
		});

		return BackboneForm;
	});