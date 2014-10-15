/**
 * Message List View
 *
 * @module     MessageListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'underscore', 'jquery', 'drop',
		'messages/list/MessageListItemView',
		'views/EmptyView',
		'hbs!messages/list/MessageList'
	],
	function( Marionette, _, $, Drop,
		MessageListItemView,
		EmptyView,
		template
	)
	{
		return Marionette.CompositeView.extend(
		{
			template: template,
			modelName: 'messages',
			childView: MessageListItemView,

			emptyViewOptions:
			{
				emptyMessage: 'No messages.'
			},

			emptyView: EmptyView,

			childViewContainer: '.list-view-message-list',

			events:
			{
				'change #filter-source' : 'updateMessageSource',
				'click .js-submit-search' : 'updateSearchTerm',
				'click .js-message-filter-box' : 'filterByBoxType',
				'click .js-message-reply' : 'toggleReply',
				'click .excerpt, .show-rest-of-message' : 'showRestOfMessage',
				'click .card-actions-list__item a' : 'toggleActiveClass',
				'click .js-location-autofill' : 'autofillLocation',
				'click .js-more-info-autofill' : 'autofillMoreInfo'
			},

			behaviors: {
				PageableView: {
					modelName: 'messages',
				}
			},

			onDomRefresh: function()
			{
				var that = this;

				this.filterDrop = new Drop({
					target: this.$('.js-filter-tags-list-drop')[0],
					content: this.$('.js-filter-tags-list-drop-content')[0],
					classes: 'drop-theme-arrows',
					position: 'bottom center',
					openOn: 'click',
					remove: true
				});

				this.filterDrop.on('open', function()
				{
					var $dropContent = $(this.content);
					$dropContent.off('.filter-drop')
						.on('click.filter-drop', '.js-message-filter-box', function(e)
						{
							that.filterDrop.close();
							that.filterByBoxType.call(that, e);
						})
						;
				});
			},

			serializeData : function ()
			{
				var data = { items: this.collection.toJSON() };
				data = _.extend(data, {
					pagination: this.collection.state,
					pageSizes: this.collection.pageSizes,
					sortKeys: this.collection.sortKeys,
					sourceTypes: this.collection.sourceTypes,
					boxTypes: this.collection.boxTypes,
					modelName : this.modelName
				});

				return data;
			},
			updateMessageSource : function (e)
			{
				e.preventDefault();

				var source = this.$('#filter-source').val();
				this.collection.setFilterParams({
						type : source
					});
			},
			updateSearchTerm : function(e)
			{
				e.preventDefault();

				var search = this.$('.js-message-search-input').val();
				this.collection.setFilterParams({
						q : search
					});
			},
			filterByBoxType : function(e)
			{
				e.preventDefault();

				var $el = $(e.currentTarget),
					box = $el.attr('data-box-name'),
					params = this.collection.setFilterParams({
						box : box
					});

				$el.closest('.js-filter-tags-list')
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
				this.$(e.currentTarget).closest('.message-card-actions-wrapper')
					.nextAll('.js-message-card-panel-reply')
					.slideToggle(200);
			},
			showRestOfMessage : function(e)
			{
				e.preventDefault();

				this.$(e.currentTarget).closest('.card__excerpt-wrapper').not('.js-selected .card__excerpt-wrapper').toggleClass('show');

				var target = this.$(e.currentTarget).closest('.card__excerpt-wrapper');

				if (target.hasClass('show')) {
					target.find('.js-elipsis').hide();
					target.find('.js-rest-of-message').delay(100).fadeIn(200);
					target.parent('.card').prevAll('.js-card-panel-map').slideToggle(200);
				}
				else {
					target.find('.js-elipsis').delay(300).show(0); //.show(0) is required for delay to work
					target.find('.js-rest-of-message').fadeOut(200);
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
				this.$('.textarea').val('Thank you for the message. What is the closest town or city for your last message?');
			},
			autofillMoreInfo : function(e)
			{
				e.preventDefault();

				this.$(e.currentTarget);
				this.$('.textarea').val('Thank you for the message. More information is needed, please provide details.');
			}
		});
	});
