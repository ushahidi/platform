/**
 * Related Posts View
 *
 * @module     RelatedPostsView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars', 'views/PostListItemView', 'text!templates/RelatedPosts.html', 'text!templates/RelatedPostItem.html'],
	function( App, Marionette, Handlebars, PostListItemView, template, itemTemplate)
	{
		var itemTemplateCpl = Handlebars.compile(itemTemplate);
		return Marionette.CompositeView.extend( {
			template: Handlebars.compile(template),
			itemView: PostListItemView,
			itemViewContainer: '.related-posts-body',
			itemViewOptions: {
				template : itemTemplateCpl,
				className: 'related-post-module-wrapper'
			}
		});
	});
