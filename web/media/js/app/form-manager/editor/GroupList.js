/**
 * Group List
 *
 * @module     GroupListView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'marionette', 'jquery', 'alertify',
		'form-manager/editor/AttributeList',
		'views/EmptyView',
		'collections/FormAttributeCollection',
		'jqueryui/sortable'
	],
	function(App, Marionette, $, alertify,
		AttributeList,
		EmptyView,
		FormAttributeCollection
	)
	{
		return Marionette.CollectionView.extend(
		{
			tagName: 'ul',

			childView: AttributeList,
			childViewOptions: function(group)
			{
				return {
					collection: new FormAttributeCollection(group.get('attributes')),
					form_group_id : group.id
				};
			},

			emptyView: EmptyView,
			emptyViewOptions: {
				emptyMessage: 'This form is empty. Create a group to get started.'
			},

			collectionEvents: {
				// @todo Avoid full render overhead by just updating part of the view
				sync : 'render'
			},

			initialize : function (options)
			{
				this.on('childview:sortable:receive', this.handleSortableReceive, this);

				this.form_id = options.form_id;
			},

			handleSortableReceive : function (receiverView, ui)
			{
				ddt.log('FormEditor', 'handleSortableReceive', ui.item[0], ui.sender);

				var $el = ui.item,
					index = $el.index(),
					$sender = ui.sender,
					senderView,
					modelView,
					model,
					receiverGroup,
					senderGroup;

				// Skip new attributes (they're handled by the 'stop' event)
				if ($el.data('is-new'))
				{
					ddt.log('FormEditor', 'Skip receive process on new attribute');
					return;
				}

				// Find the sender view
				senderView = this.children.find(function (view)
					{
						return $sender.is(view.$childViewContainer);
					});

				// Sanity check: did we find the sender view?
				if (! senderView)
				{
					ddt.log('FormEditor', 'Couldn\'t find sender view', $sender);
					return;
				}

				// Find the model view ..
				modelView = senderView.children.find(function (view)
					{
						return $el.is(view.el);
					});
				// .. and from that the model
				model = modelView.model;

				ddt.log('FormEditor', 'adding model from group to group', model, senderView.form_group_id, receiverView.form_group_id);

				receiverGroup = this.collection.get(receiverView.form_group_id);
				senderGroup = this.collection.get(senderView.form_group_id);

				// Add the attribute to the receiver group
				App.oauth.ajax({
						url : receiverGroup.url() + '/attributes',
						type : 'POST',
						dataType : 'json',
						contentType: 'application/json',
						data : JSON.stringify({
							id : model.id,
							priority: index
						})
					})
					.done(function ()
					{
						// Remove from original group
						senderView.collection.remove(model);
						App.oauth.ajax({
							url : senderGroup.url() + '/attributes/' + model.id,
							dataType : 'json',
							type : 'DELETE'
						});

						// Add model to receiver collection
						receiverView.collection.add(model, { at : index });

						// Reorder attributes
						receiverView.reorderAttributes();
					})
					.fail(function ()
					{
						alertify.error('Unable to move field, please try again');
					});

				// remove original element from DOM
				$el.remove();
			}
		});
	});
