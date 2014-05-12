/**
 * Message List View
 *
 * @module     MessageListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'handlebars','underscore', 'views/messages/MessageListItemView',
		'text!templates/messages/MessageList.html', 'text!templates/partials/pagination.html', 'text!templates/partials/list-info.html'],
	function( App, Marionette, Handlebars, _, MessageListItemView,
		template, paginationTemplate, listInfoTemplate)
	{
		Handlebars.registerPartial('pagination', paginationTemplate);
		Handlebars.registerPartial('list-info', listInfoTemplate);

		return Marionette.CompositeView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			// Lets just store the partial templates somewhere useful
			partialTemplates :
			{
				pagination : Handlebars.compile(paginationTemplate),
				listInfo : Handlebars.compile(listInfoTemplate)
			},
			initialize: function()
			{
			},

			itemView: MessageListItemView,
			itemViewOptions: {},

			itemViewContainer: '.list-view-message-list',

			events:
			{
				'click .js-list-view-select' : 'showHideBulkActions',
				'click .js-page-first' : 'showFirstPage',
				'click .js-page-next' : 'showNextPage',
				'click .js-page-prev' : 'showPreviousPage',
				'click .js-page-last' : 'showLastPage',
				'click .js-page-change' : 'showPage',
				'change #filter-source' : 'updateMessageSource',
				'change #filter-sort' : 'updatePostsSort',
				'click .js-submit-search' : 'updateSearchTerm',
				'click .js-message-filter-box' : 'filterByBoxType',
				'click .js-message-reply' : 'toggleReply',
				'click .js-message-activity' : 'toggleMessageActivity',
				'click .excerpt, .show-rest-of-message' : 'showRestOfMessage',
				'click .card-actions-list__item a' : 'toggleActiveClass',
				'click .js-location-autofill' : 'autofillLocation',
				'click .js-more-info-autofill' : 'autofillMoreInfo'
			},

			collectionEvents :
			{
				reset : 'updatePagination',
				add : 'updatePagination',
				remove : 'updatePagination',
				sync : 'updatePagination'
			},

			showHideBulkActions : function ()
			{
				var $checked = this.$('.js-list-view-select input[type="checkbox"]:checked');

				if ($checked.length > 0)
				{
					this.$('.js-list-view-bulk-actions').removeClass('visually-hidden');
					this.$('.js-list-view-bulk-actions').addClass('visible');
				}
				else
				{
					this.$('.js-list-view-bulk-actions').removeClass('visible');
					this.$('.js-list-view-bulk-actions').addClass('visually-hidden');
				}
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					sortKeys: this.collection.sortKeys,
					sourceTypes: this.collection.sourceTypes,
					boxTypes: this.collection.boxTypes
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
				this.$('.js-pagination').replaceWith(
					Handlebars.partials.pagination({
						pagination: this.collection.state
					})
				);
				this.$('.js-list-view-filter-info').replaceWith(
					Handlebars.partials.listinfo({
						pagination: this.collection.state,
						modelName: this.modelName
					})
				);

				// Update counter
				this.$('li.active span.count-number').text(this.collection.state.totalRecords);
			},
			updateMessageSource : function (e)
			{
				e.preventDefault();

				var source = this.$('#filter-source').val();
				App.Collections.Messages.setFilterParams({
						type : source
					});
			},
			updateSearchTerm : function(e)
			{
				e.preventDefault();

				var search = this.$('.js-message-search-input').val();
				App.Collections.Messages.setFilterParams({
						q : search
					});
			},
			updatePostsSort : function (e)
			{
				e.preventDefault();
				var orderby = this.$('#filter-sort').val();
				this.collection.setSorting(orderby);
				this.collection.getFirstPage();
			},
			filterByBoxType : function(e)
			{
				e.preventDefault();

				var $el = this.$(e.currentTarget),
					box = $el.attr('data-box-name'),
					params = App.Collections.Messages.setFilterParams({
						box : box
					});

				$el.closest('.js-filter-categories-list')
					.find('li')
						.removeClass('active')
						.find('.message-type > span').addClass('visually-hidden')
						.end()
					.filter('li[data-box-name="' + box + '"]')
						.addClass('active')
						.find('.message-type > span').removeClass('visually-hidden');

				this.$('.js-message-search-input').val(params.q);
			},
			toggleReply : function(e)
			{
				e.preventDefault();
				this.$(e.currentTarget).closest('.card-actions-wrapper').nextAll('.js-card-panel-reply').slideToggle(200);
			},
			toggleMessageActivity : function(e)
			{
				e.preventDefault();
				this.$(e.currentTarget).closest('.card-actions-wrapper').nextAll('.js-card-panel-activity').slideToggle(200);
			},
			showRestOfMessage : function(e)
			{
				e.preventDefault();

				this.$(e.currentTarget).closest('.card__excerpt-wrapper').not('.js-selected .card__excerpt-wrapper').toggleClass('show');

				var target = this.$(e.currentTarget).closest('.card__excerpt-wrapper');

				if (target.hasClass('show')) {
					target.find('.elipsis').hide();
					target.find('.rest-of-message').delay(100).fadeIn(200);
					target.parent('.card').prevAll('.js-card-panel-map').slideToggle(200);
				}
				else {
					target.find('.elipsis').delay(300).show(0); //.show(0) is required for delay to work
					target.find('.rest-of-message').fadeOut(200);
					target.parent('.card').prevAll('.js-card-panel-map').slideToggle(200);
				}
			},
			toggleActiveClass : function(e)
			{
				e.preventDefault();

				this.$(e.currentTarget).toggleClass('active');
			},
			autofillLocation : function(e)
			{
				e.preventDefault();

				this.$(e.currentTarget);
				this.$('.textarea').val('Greensboro, NC');
			},
			autofillMoreInfo : function(e)
			{
				e.preventDefault();

				this.$(e.currentTarget);
				this.$('.textarea').val('Thank you for the message. More information is needed, please provide details.');
			}
		});
	});
