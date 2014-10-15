/**
 * Attribute List
 *
 * @module     FormListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'jquery', 'alertify',
		'form-manager/editor/AttributeListItem',
		'hbs!form-manager/editor/Group',
		'views/EmptyView',
		'models/FormAttributeModel',
		'jqueryui/sortable'
	],
	function(App, Marionette, _, $, alertify,
		AttributeListItem,
		template,
		EmptyView,
		FormAttributeModel
	)
	{
		return Marionette.CompositeView.extend(
		{
			tagName: 'li',
			template: template,

			childView: AttributeListItem,
			childViewContainer: '.list-view-attribute-list',

			emptyView: EmptyView,
			emptyViewOptions:
			{
				emptyMessage: 'This group is empty. Drag a field here to populate the form.',
			},

			events : {
				'click .js-edit-form-group' : 'showFormGroupEdit',
				'click .js-delete-form-group' : 'deleteGroup',
			},

			initialize : function (options)
			{
				this.on('sortable:update', this.handleSortableUpdate, this);

				this.form_group_id = options.form_group_id;
			},

			onRender : function ()
			{
				var that = this;

				this.$(this.childViewContainer).sortable({
					cancel: '.list-view-empty',
					update: function( event, ui ) {
						//ddt.log('FormEditor', 'stop ui', that.form_group_id, ui.item[0], ui.sender);
						that.trigger('sortable:update', ui);
					},
					receive: function(event, ui) {
						//ddt.log('FormEditor', 'receive ui', that.form_group_id, ui.item[0], ui.sender);
						that.trigger('sortable:receive', ui);
					},
					// Replace item being dragged with just its title
					helper : function(event, el) {
						var title = $('.field-preview > label', el).text(),
							helper = $('<li class="list-view-attribute"></li>')
								.append('<label></label>')
								.text(title);
						return helper;
					},
					placeholder : 'list-view-attribute placeholder',
					connectWith: '.form-attributes .list-view-attribute-list'
				});
			},

			onDestroy : function ()
			{
				try {
					this.$(this.itemViewContainer).sortable('destroy');
				} catch (err) {
					ddt.trace('FormEditor', err.message);
				}
			},

			handleSortableUpdate : function (ui)
			{
				var $el = ui.item;

				// New attribute added
				if ($el.data('is-new'))
				{
					this.addAttribute($el);
				}

				// Reorder attributes
				this.reorderAttributes();
			},

			addAttribute : function ($el)
			{
				var index = $el.index(),
					model = new FormAttributeModel({
						input : $el.data('input').toLowerCase(), // @todo fix this at the server end.. match client input names
						type : $el.data('type'),
						priority : index,
						form_group_id : this.form_group_id
					});

				model.set('label', 'New ' + $el.data('label'));

				this.collection.add(model, { at : $el.index() });

				ddt.log('FormEditor', 'new model', $el, model, this.collection);

				// remove original element from DOM
				$el.remove();
			},

			reorderAttributes : function (/*event, ui*/)
			{
				var models_saved = [];

				ddt.log('FormEditor', 'reorder group', this.form_group_id);
				this.children.each(function (view)
				{
					var position = view.$el.index(),
						oldPosition = view.model.get('priority');

					if (parseInt(oldPosition, 10) !== position)
					{
						view.model.set({'priority': position});
					}
				});

				// Re-sort the collection, but don't trigger events because the DOM
				// should already be in order
				this.collection.sort({silent: true});

				// save every model
				this.collection.map(function(model) {
					models_saved.push(model.save());
				});

				// display a success/failure message after the models are saved
				$.when.apply($, models_saved).done(function() {
					var args = Array.prototype.slice.call(arguments),
						failures = _.filter(args, function(a) {
							return (a[1] !== 'success');
						});

					if (failures.length) {
						alertify.error('Unable to save some fields.<br>Please try again.');
					} else {
						alertify.success('Form saved');
					}
				});
			},

			showFormGroupEdit : function(e)
			{
				e.preventDefault();
				App.vent.trigger('formgroup:edit', this.model);
			},

			deleteGroup: function(e)
			{
				var that = this;
				e.preventDefault();
				alertify.confirm('Are you sure you want to delete?', function(e)
				{
					if (e)
					{
						that.model
							.destroy({
								// Wait till server responds before destroying model
								wait: true
							})
							.done(function()
							{
								alertify.success('Group has been deleted');
							})
							.fail(function ()
							{
								alertify.error('Unable to delete group, please try again');
							});
					}
					else
					{
						alertify.log('Delete cancelled');
					}
				});
			}

		});
	});
