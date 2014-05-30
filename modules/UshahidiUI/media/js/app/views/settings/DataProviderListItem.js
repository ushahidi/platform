/**
 * Tag List Item View
 *
 * @module     UserListItemView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App','handlebars', 'marionette', 'alertify', 'text!templates/settings/DataProviderListItem.html'],
	function(App,Handlebars, Marionette, alertify, template)
	{
		var updateConfig = function (configModel, providerModel)
		{
			configModel.get('providers')[providerModel.id] = providerModel.get('enabled');
			configModel.save().done(function (/* model, response, options*/)
				{
					alertify.success(providerModel.get('name') + (providerModel.get('enabled') ? ' enabled!' : ' disabled!'));
				})
			.fail(function (response /*, xhr, options*/)
				{
					alertify.error('Unable to update provider, please try again.');
					if (response.errors) {
						ddt.log('debug', response.errors);
					}
				});
		};

		//ItemView provides some default rendering logic
		return Marionette.ItemView.extend(
		{
			//Template HTML string
			template: Handlebars.compile(template),
			tagName: 'li',
			className: 'list-view-data-provider  data-provider-card__list-item',

			// Value to track if checkbox for this post has been selected
			selected : false,
			events: {
				'click .js-provider-status' : 'toggleStatus'
			},

			modelEvents: {
				'change': 'render'
			},

			initialize : function (options)
			{
				this.configModel = options.configModel;
			},

			toggleStatus : function (e)
			{
				e.preventDefault();

				var $el = this.$('.data-provider-card');

				$el.toggleClass('disabled');

				if ( $el.hasClass('disabled') ) {
					this.model.set('enabled', false);
					this.$('.js-provider-status').text('Enable');
				}
				else {
					this.model.set('enabled', true);
					this.$('.js-provider-status').text('Disable');
				}

				updateConfig(this.configModel, this.model);
			}
		});
	});
