/**
 * Related Posts View
 *
 * @module     RelatedPostsView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars', 'underscore', 'views/PostListItemView', 'text!templates/RelatedPosts.html', 'text!templates/RelatedPostItem.html'],
	function( App, Marionette, Handlebars, _, PostListItemView, template, itemTemplate)
	{
		var itemTemplateCpl = Handlebars.compile(itemTemplate);
		return Marionette.CompositeView.extend( {
			template: Handlebars.compile(template),
			itemView: PostListItemView,
			itemViewContainer: '.related-posts-body',
			itemViewOptions: {
				template : itemTemplateCpl,
				className: 'related-post-module-wrapper'
			},

			serializeData: function()
			{
				var tags,
					last_tag,
					data;

				tags = _.pluck(this.model.getTags(), 'tag');
				if (tags.length > 1)
				{
					last_tag = tags.slice(-1);
					tags = tags.slice(0, -1).join(', ') + ' or ' + last_tag;
				}
				else
				{
					tags = tags[0];
				}

				data = _.extend(this.model.toJSON(), {
					tags : tags
				});
				return data;
			}
		});
	});
