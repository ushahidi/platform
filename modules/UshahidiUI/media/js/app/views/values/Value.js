/**
 * Post Value
 *
 * @module     Value
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'handlebars', 'text!templates/values/Value.html'],
	function(Marionette, Handlebars, template) {
		return Marionette.ItemView.extend(
		{
			className: 'post-value',
			template : Handlebars.compile(template),
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