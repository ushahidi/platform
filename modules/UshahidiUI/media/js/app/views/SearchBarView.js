/**
 * Search bar
 *
 * @module     SearchBarView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'handlebars', 'App', 'text!templates/SearchBar.html'],
	function(Marionette, Handlebars, App, template)
	{
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),

			events:{
				'click button': 'SearchPosts'
			},

			SearchPosts: function(e)
			{
			 e.preventDefault();
			 var keyword = this.$('.search-field').val();
			 App.appRouter.trigger('posts?*querystring',keyword);
			},
		});
	});
