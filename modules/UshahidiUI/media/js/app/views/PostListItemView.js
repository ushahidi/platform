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
		//ItemView provides some default rendering logic
		return PostItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-post',
			initialize : function ()
			{
				this.selected = false;
			},
			events : {
				'change .js-select-post-input' : 'updatedSelected',
			},
			select : function ()
			{
				this.selected = true;
				this.$('.js-select-post-input').prop('checked', true);
				this.trigger('select');
			},
			unselect : function ()
			{
				this.selected = false;
				this.$('.js-select-post-input').prop('checked', false);
				this.trigger('unselect');
			},
			updatedSelected : function (e)
			{
				var $el = this.$(e.currentTarget);
				this.selected = $el.is(':checked');
				this.trigger(this.selected ? 'select' : 'unselect');
			},
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
