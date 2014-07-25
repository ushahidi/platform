/**
 * User List Item View
 *
 * @module     UserListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'alertify', 'hbs!templates/tags/TagListItem'],
	function(App, Marionette, alertify, template)
	{
		return Marionette.ItemView.extend(
		{
			template: template,
			tagName: 'li',
			className: 'list-view-tag',

			// Value to track if checkbox for this post has been selected
			selected : false,
			events: {
				'click .js-tag-delete': 'deleteTag',
				'click .js-tag-edit' : 'showEditTag',
				'change .js-select-input' : 'updatedSelected',
			},

			modelEvents: {
				'sync': 'render'
			},

			deleteTag: function(e)
			{
				var that = this;
				e.preventDefault();
				alertify.confirm('Are you sure you want to delete this tag ?', function(e)
				{
					if (e)
					{
						that.model.destroy({
							// Wait till server responds before destroying model
							wait: true
						}).done(function()
						{
							alertify.success('Tag has been deleted');
							// Trigger a fetch. This is to remove the model from the listing and load another
							App.Collections.Tags.fetch();
						}).fail(function ()
						{
							alertify.error('Unable to delete tag, please try again');
						});
					}
					else
					{
						alertify.log('Delete cancelled');
					}
				});
			},

			showEditTag : function (e)
			{
				e.preventDefault();
				App.vent.trigger('tag:edit', this.model);
			},

			/**
			 * Select this item (for bulk actions)
			 */
			select : function ()
			{
				this.selected = true;
				this.$('.js-select-input').prop('checked', true);
				this.trigger('select');
			},

			/**
			 * Unselect this item (for bulk actions)
			 */
			unselect : function ()
			{
				this.selected = false;
				this.$('.js-select-input').prop('checked', false);
				this.trigger('unselect');
			},

			updatedSelected : function (e)
			{
				var $el = this.$(e.currentTarget);
				this.selected = $el.is(':checked');
				this.trigger(this.selected ? 'select' : 'unselect');

				$el.parent()
					.toggleClass('selected-button', this.selected);
			}
		});
	});
