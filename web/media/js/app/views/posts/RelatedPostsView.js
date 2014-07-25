/**
 * Related Posts View
 *
 * @module     RelatedPostsView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'views/posts/PostListItemView', 'hbs!templates/posts/RelatedPosts', 'hbs!templates/posts/RelatedPostItem'],
	function( App, Marionette, _, PostListItemView, template, itemTemplate)
	{
		return Marionette.CompositeView.extend( {
			template: template,
			itemView: PostListItemView,
			itemViewContainer: '.related-posts-body',
			itemViewOptions: {
				template : itemTemplate,
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
