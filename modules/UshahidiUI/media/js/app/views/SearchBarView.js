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
				'submit form': 'SearchPosts'
			},

			SearchPosts: function(e)
			{
				//var that = this;

				e.preventDefault();
				var keyword = this.$('#q').val();
				App.Collections.Posts.setFilterParams({
					q : keyword
				});

			},

		});
	});
