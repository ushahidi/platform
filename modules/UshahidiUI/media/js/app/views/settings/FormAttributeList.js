/**
 * Attribute List
 *
 * @module     FormListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'handlebars', 'underscore', 'jquery', 'views/settings/AttributeListItem', 'views/EmptyView', 'models/FormAttributeModel', 'jqueryui/sortable'],
	function( Marionette, Handlebars, _, $, AttributeListItem, EmptyView, FormAttributeModel)
	{
		return Marionette.CollectionView.extend(
		{
			tagName: 'ul',

			itemView: AttributeListItem,

			itemViewOptions:
			{
				emptyMessage: 'No forms found.',
			},

			emptyView: EmptyView,

			initialize : function (options)
			{
				this.on('sortable:stop', this.handleSortableStop, this);

				this.form_group_id = options.form_group_id;
			},

			onDomRefresh : function ()
			{
				var that = this;

				ddt.log('FormEditor', 'DOM Refresh');

				this.$el.sortable({
					stop: function( event, ui ) {
						//ddt.log('FormEditor', 'stop event', event);
						ddt.log('FormEditor', 'stop ui', ui);
						that.trigger('sortable:stop', event, ui);
					}
				});
			},

			onClose : function ()
			{
				this.$el.sortable('destroy');
			},

			handleSortableStop : function (event, ui)
			{
				var $el = $(ui.item[0]),
					// Search existing child views to see if this is a new attribute or not
					view = this.children.find(function (view) {
						return $el.is(view.el);
					});

				// New attribute added
				if (! view)
				{
					this.addAttribute($el);
				}
				// Else existing attribute was reordered
				ddt.log('FormEditor', 'update element', $el);

				// Reorder attributes
				this.reorderAttributes();
			},

			addAttribute : function ($el)
			{
				var index = $el.index(),
					model = new FormAttributeModel({
						input : $el.data('attribute-input').toLowerCase(), // @todo fix this at the server end.. match client input names
						type : $el.data('attribute-type'),
						priority : index,
						form_group_id : this.form_group_id
					});

				model.set('label', 'New ' + $el.data('attribute-label'));

				this.collection.add(model, { at : $el.index() });

				ddt.log('FormEditor', 'new model', $el, model, this.collection);

				// remove original element from DOM
				$el.remove();

				// Re-render the view because otherwise our new item ends up at the bottom.
				// Working around these issues until we update to Marionette 2.0
				// https://github.com/marionettejs/backbone.marionette/issues/1078
				// https://github.com/marionettejs/backbone.babysitter/issues/11
				this.render();
			},

			reorderAttributes : function (/*event, ui*/)
			{
				this.children.each(function (view)
				{
					var position = view.$el.index(),
						oldPosition = view.model.get('priority');

					if (parseInt(oldPosition, 10) !== position)
					{
						view.model.set({'priority': position});
					}
				});

				this.collection.sort();
				this.collection.each(function (model) {
					// Skip new models when saving a reorder
					if (model.isNew())
					{
						return;
					}

					model.save()
						.done(function()
						{
							// alertify.success('Order saved');
						})
						.fail(function ()
						{
							// alertify.error('Unable to delete field, please try again');
						});
				});

			}

		});
	});
