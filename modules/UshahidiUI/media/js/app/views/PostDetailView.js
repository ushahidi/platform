/**
 * Post Detail
 *
 * @module     PostDetailView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'handlebars', 'views/PostItemView', 'text!templates/PostDetail.html'],
	function(App, Handlebars, PostItemView, template)
	{
		//CollectionView provides some default rendering logic
		return PostItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),

			modelEvents: {
				'sync': 'render',
				'destroy' : 'handleDeleted'
			},

			handleDeleted : function()
			{
				// Redirect user to previous page (probably post list)
				// @todo does this always make sense?
				window.history.back();
			}

		});
	});
