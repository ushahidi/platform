/**
 * Modal Controller
 *
 * Handles showing/hiding views in the modal region
 *
 * @module     ModalController
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'backbone', 'marionette',
	'views/modals/CreatePostView', 'views/modals/EditPostView', 'views/modals/AddToSetView', 'views/modals/CreateSetView'],
	function(App, Backbone, Marionette,
		PostCreateView, PostEditView, AddToSetView, CreateSetView)
	{
		return Backbone.Marionette.Controller.extend(
		{
			initialize : function(options)
			{
				// Store modal region we're controlling
				this.modal = options.modal;
				
				App.vent.on('post:create', this.postCreate, this);
				App.vent.on('post:edit', this.postEdit, this);
				App.vent.on('post:set', this.addToSet, this);
				App.vent.on('set:create', this.setCreate, this);
			},
			postCreate : function ()
			{
				this.modal.show(new PostCreateView());
			},
			postEdit : function (post)
			{
				this.modal.show(new PostEditView({
					model : post
				}));
			},
			addToSet : function (post)
			{
				this.modal.show(new AddToSetView({
					model : post
				}));
			},
			setCreate : function (post)
			{
				this.modal.show(new CreateSetView({
					model : post
				}));
			}
		});
	});
