/**
 * Post Value
 *
 * @module     Value
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'hbs!templates/values/Value'],
	function(Marionette, template) {
		return Marionette.ItemView.extend(
		{
			tagName: 'dd',
			className: 'post-value',
			template : template,
			initialize : function (options)
			{
				this.label = options.label || options.attribute.label;
				this.key = options.key;
				this.value = options.value;
				this.value_id = options.value_id;
				this.attribute = options.attribute;
			},
			serializeData : function ()
			{
				var data = {
					attribute : this.attribute,
					label : this.label,
					key : this.key,
					value : this.value,
					value_id : this.value_id
				};
				return data;
			}
		});
	});
