/**
 * Default Form Attributes
 *
 * @module     defaultFormAttrs
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([], function() {
	return [
		{
			label: 'Text',
			input: 'Text',
			type: 'varchar',
			icon: 'fa-font'
		},
		{
			label: 'TextArea',
			input: 'TextArea',
			type: 'text',
			icon: 'fa-paragraph'
		},
		{
			label: 'Number (Decimal)',
			input: 'Number',
			type: 'decimal',
			icon: 'fa-fax'
		},
		{
			label: 'Number (Integer)',
			input: 'Number',
			type: 'int',
			icon: 'fa-fax'
		},
		{
			label: 'Select',
			input: 'Select',
			type: 'varchar', // what about numeric selections?
			options: [],
			icon: 'fa-bars'
		},
		{
			label: 'Radio',
			input: 'Radio',
			type: 'varchar', // not totally sure about this
			options: [],
			icon: 'fa-dot-circle-o'
		},
		{
			label: 'Checkbox',
			input: 'Checkbox',
			type: 'varchar', // not totally sure about this
			icon: 'fa-check'
		},
		{
			label: 'Checkboxes',
			input: 'Checkboxes',
			type: 'varchar', // not totally sure about this
			icon: 'fa-check'
		},
		{
			label: 'Date',
			input: 'Date',
			type: 'datetime',
			icon: 'fa-calendar'
		},
		{
			label: 'DateTime',
			input: 'DateTime',
			type: 'datetime',
			icon: 'fa-clock-o'
		},
		{
			label: 'Location', // Defined in UshahidiForms
			input: 'Location',
			type: 'point',
			icon: 'fa-map-marker'
		}
	];
});
