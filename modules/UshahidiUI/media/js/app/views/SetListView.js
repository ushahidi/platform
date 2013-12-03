/**
 * Sets List View
 *
 * @module     SetLitView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars','underscore', 'views/SetListItemView',
	'text!templates/SetList.html', 'text!templates/partials/pagination.html'],
	function(App, Marionette, Handlebars, _, SetListItemView,
		template, paginationTemplate)
	{
		Handlebars.registerPartial('pagination', paginationTemplate);

		return Marionette.CompositeView.extend(
		{
			// Template HTML String
			template: Handlebars.compile(template),

			partialTemplates :
			{
				pagination : Handlebars.compile(paginationTemplate)
			},

			initialize: function()
			{
			},

			itemView: SetListItemView,
			itemViewOptions: {},

			itemViewContainer: '.sets-grid',

			events:
			{
				'click .js-page-first' : 'showFirstPage',
				'click .js-page-next' : 'showNextPage',
				'click .js-page-prev' : 'showPreviousPage',
				'click .js-page-last' : 'showLastPage',
				'click .js-page-change' : 'showPage',
			},

			collectionEvents :
			{
				reset : 'updatePagination',
				add : 'updatePagination'
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state
				});

				return data;
			},

			showNextPage : function (e)
			{
				e.preventDefault();
				// Already at last page, skip
				if (this.collection.state.lastPage <= this.collection.state.currentPage)
				{
					return;
				}

				this.collection.getNextPage();
				this.updatePagination();
			},
			showPreviousPage : function (e)
			{
				e.preventDefault();
				// Already at last page, skip
				if (this.collection.state.firstPage >= this.collection.state.currentPage)
				{
					return;
				}

				this.collection.getPreviousPage();
				this.updatePagination();
			},
			showFirstPage : function (e)
			{
				e.preventDefault();
				// Already at last page, skip
				if (this.collection.state.firstPage >= this.collection.state.currentPage)
				{
					return;
				}

				this.collection.getFirstPage();
				this.updatePagination();
			},
			showLastPage : function (e)
			{
				e.preventDefault();
				// Already at last page, skip
				if (this.collection.state.lastPage <= this.collection.state.currentPage)
				{
					return;
				}

				this.collection.getLastPage();
				this.updatePagination();
			},
			showPage : function (e)
			{
				var $el = this.$(e.currentTarget),
						num = 0;

				e.preventDefault();

				_.each(
					$el.attr('class').split(' '),
					function (v) {
						if (v.indexOf('page-') === 0)
						{
							num = v.replace('page-', '');
						}
					}
				);
				this.collection.getPage(num -1);
				this.updatePagination();
			},

			updatePagination: function ()
			{
				this.$('.pagination').replaceWith(
					this.partialTemplates.pagination({
						pagination: this.collection.state
					})
				);
			}

		});

	});