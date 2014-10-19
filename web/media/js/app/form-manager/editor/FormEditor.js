/**
 * Forms Editor
 *
 * @module     FormsEditor
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'underscore', 'jquery', 'alertify', 'util/notify',
		'hbs!form-manager/editor/FormEditor',
		'form-manager/editor/GroupList',
		'forms/UshahidiForms',
		'jqueryui/draggable'],
	function(App, Marionette, _, $, alertify, notify,
		template,
		GroupList,
		BackboneForm
	)
	{
		return Marionette.LayoutView.extend(
		{
			template: template,
			form: null,
			availableFields : [],

			regions : {
				formAttributes : '.form-attributes',
				attributeEditor : '.js-edit-form'
			},

			events : {
				'click .js-edit-custom-form' : 'showCustomFormEdit',
				'click .js-add-group' : 'showGroupCreate',
				'click .js-edit-attr' : 'toggleEditor',
				'click .js-add-attr' : 'toggleEditor',
				'click .js-custom-form-undelete' : 'enableForm'
			},

			ui : {
				'availableAttributes' : '.available-attributes li'
			},

			modelEvents : {
				// @todo Avoid full render overhead by just updating part of the view
				'sync' : 'render',
			},

			initialize : function(options)
			{
				this.availableAttributes = options.availableAttributes;

				this.groupCollection = this.model.getGroupCollection();
				App.vent.on('formeditor:reorder', this.handleReorderView);
			},

			serializeData : function()
			{
				return _.extend(this.model.toJSON(), {
					availableAttributes : this.availableAttributes,
					disabled : this.model.get('disabled')
				});
			},

			onRender : function()
			{
				// Create a group list, have to do this everytime since destroy'd views
				// aren't reusable
				var groupList = new GroupList({
					collection : this.groupCollection,
					form_id : this.model.id
				});

				// Trigger editor
				groupList.on('childview:childview:edit', function(groupView, attrView, attribute) {
					this.showAttributeEditor(attribute);
				}, this);

				// Show the group list
				this.formAttributes.show(groupList);

				// Make available attributes draggable
				this.ui.availableAttributes.draggable({
					helper: 'clone',
					revert: 'invalid',
					// Connect to the attribute-sortables
					connectToSortable: '.form-attributes .list-view-attribute-list'
				});

			},

			handleReorderView : function (view)
			{
				var models_saved = [];

				view.children.each(function (v)
				{
					var position    = v.$el.index(),
						oldPosition = v.model.get('priority');

					if (parseInt(oldPosition, 10) !== position)
					{
						v.model.set({ 'priority': position });
					}
				});

				// Re-sort the collection, but don't trigger events
				// because the DOM should already be in order
				view.collection.sort({ silent: true });

				// save every model
				view.collection.map(function(model) {
					models_saved.push(model.save());
				});

				// display a success/failure message after the models are saved
				notify.whenXHRDone(models_saved,
					'Form saved',
					'Unable to save the form.<br>Please try again.'
				);
			},

			onShow : function() {
				// Once everything is visible, trigger an event so maps can update themselves
				App.vent.trigger('location:refresh');
			},

			onDestroy : function ()
			{
				this.ui.availableAttributes.draggable('destroy');
			},

			toggleEditor : function(e, edit)
			{
				e && e.preventDefault();

				edit = !!edit;
				if (e && this.$(e.currentTarget).is(this.$('.js-edit-attr'))) {
					edit = true;
				}

				// Show 'edit' tab the first time an editor is displayed
				edit && this.$('.js-edit-attr').parent().removeClass('visually-hidden');

				// Toggle editor/availabel attributes
				this.$('.js-edit-attr').parent().toggleClass('active', edit);
				this.$('.js-add-attr').parent().toggleClass('active', !edit);
				this.$('.edit-attribute').toggleClass('active', edit);
				this.$('.available-attributes').toggleClass('active', !edit);
			},

			showCustomFormEdit : function(e)
			{
				e.preventDefault();
				App.vent.trigger('customform:edit', this.model);
			},
			showGroupCreate : function(e)
			{
				e.preventDefault();
				App.vent.trigger('formgroup:create', this.model.id, this.groupCollection);
			},

			showAttributeEditor: function(attribute)
			{
				ddt.log('FormEditor', 'showEditor', attribute);

				// Make sure editor is visible when showing the form
				this.toggleEditor(null, true);

				this.attributeForm = new BackboneForm({
					model: attribute,
					submitButton : 'Save'
				});

				// Change options array to comma-separated
				this.attributeForm.setValue('options', _.isArray(attribute.get('options')) ? attribute.get('options').join(',') : '');

				this.attributeForm.on('submit', this.saveAttribute, this);

				this.attributeEditor.show(this.attributeForm);
			},
			saveAttribute: function(e)
			{
				e.preventDefault();
				var data = this.attributeForm.getValue();

				ddt.log('FormEditor', 'form data', data);

				// Split options apart since server expects an array
				if (data.options)
				{
					data.options = data.options.split(',');
				}

				this.attributeForm.model.set(_.pick(data, 'label', 'options', 'default', 'format', 'required', 'cardinality'));
				this.attributeForm.model.save({
						wait: true
					})
					.done(function ()
					{
						alertify.success('Field saved');
						App.Collections.Forms.fetch();
					})
					.fail(function ()
					{
						alertify.error('Unable to save field, please try again');
					});
			},
			enableForm: function()
			{
				var that = this;
				$('.actions-bar-warning').slideUp(function() {
					$(this).remove();
					that.model.enable();
				});
			}
		});
	});
