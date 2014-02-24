/**
 * User List Item View
 *
 * @module     UserListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App','handlebars', 'marionette', 'alertify', 'text!templates/TagListItem.html'],
	function(App,Handlebars, Marionette, alertify, template)
	{
		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-post',

			// Value to track if checkbox for this post has been selected
			selected : false,
			events: {
				'click .js-tag-delete': 'deleteTag',
				'click .js-tag-edit' : 'showEditTag',
				'change .js-select-tag-input' : 'updatedSelected'
			},

			initialize: function()
			{
				// Refresh this view when there is a change in this model
				this.listenTo(this.model,'change', this.render);
			},

			modelEvent: {
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

			select : function ()
			{
				this.selected = true;
				this.$('.js-select-tag-input').prop('checked',true);
				this.trigger('select');
			},

			unselect : function ()
			{
				this.selected = false;
				this.$('.js-select-tag-input').prop('checked',false);
				this.trigger('select');
			},

			updatedSelected : function (e)
			{
				var $el = this.$(e.currentTarget);
				this.selected = $el.is(':checked');
				this.trigger(this.selected ? 'select' : 'unselect');
			}
		});
	});
