/**
 * Sets List View
 *
 * @module     SetLitView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore',
		'sets/SetListItemView',
		'hbs!sets/SetList'
	],
	function(App, Marionette, _,
		SetListItemView,
		template
	)
	{

		return Marionette.CompositeView.extend(
		{
			template: template,
			modelName: 'sets',

			initialize: function()
			{
			},

			childView: SetListItemView,

			childViewContainer: '.sets-grid',

			behaviors: {
				PageableView: {
					modelName : 'sets'
				}
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					pageSizes: this.collection.pageSizes,
					sortKeys: this.collection.sortKeys,
					modelName : this.modelName
				});

				return data;
			},

		});

	});
