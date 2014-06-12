/**
 * Attribute List Item View
 *
 * @module     AttributeListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['handlebars', 'views/settings/AttributeListItem', 'text!templates/settings/AvailableAttributeListItem.html', 'jqueryui/draggable'],
	function(Handlebars, AttributeListItem, template)
	{
		return AttributeListItem.extend(
		{
			template: Handlebars.compile(template),
			initialize: function (options)
			{
				AttributeListItem.prototype.initialize.call(this, options);
				this.sortableList = options.sortableList;
			},

			onDomRefresh: function ()
			{
				this.$el.draggable({
					connectToSortable : this.sortableList.$el,
					helper: 'clone',
					revert: 'invalid'
				});
			},

			onClose : function ()
			{
				this.$el.draggable('destroy');
			}

		});
	});
