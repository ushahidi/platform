/**
 * Post List Item
 *
 * @module     PostItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['handlebars', 'underscore', 'views/PostItemView', 'text!templates/PostListItem.html'],
	function(Handlebars, _, PostItemView, template)
	{
		return PostItemView.extend(
		{
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-post',
			// Value to track if checkbox for this post has been selected
			selected : false,
			events : {
				'change .js-select-post-input' : 'updatedSelected',
			},
			/**
			 * Select this post (for bulk actions)
			 */
			select : function ()
			{
				this.selected = true;
				this.$('.js-select-post-input').prop('checked', true);
				this.trigger('select');
			},
			/**
			 * Unselect this post (for bulk actions)
			 */
			unselect : function ()
			{
				this.selected = false;
				this.$('.js-select-post-input').prop('checked', false);
				this.trigger('unselect');
			},
			/**
			 * Updated 'selected' value from DOM
			 */
			updatedSelected : function (e)
			{
				var $el = this.$(e.currentTarget);
				this.selected = $el.is(':checked');
				this.trigger(this.selected ? 'select' : 'unselect');
			},
			// Override serializeData to include value of 'selected'
			serializeData: function()
			{
				var data = _.extend(
					PostItemView.prototype.serializeData.call(this),
					{
						selected : this.selected
					}
				);
				return data;
			},
		});
	});
