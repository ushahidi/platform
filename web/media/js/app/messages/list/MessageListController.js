/**
 * Messages List Controller
 *
 * @module     MessagesListController
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'messages/list/MessageListView', 'collections/MessageCollection'],
	function(App, MessageListView, MessageCollection)
{
	var MessageListController = {
		listMessages : function(view)
		{
			App.vent.trigger('page:change', view ? 'messages/' + view : 'messages');

			App.Collections.Messages = new MessageCollection();
			var messages = App.Collections.Messages,
				replies,
				promise;

			switch (view)
			{
				// Filter by type. Will also default to incoming + received status
				case 'email':
					promise = messages.fetch({data : {type : 'email'}});
					break;
				case 'sms':
					promise = messages.fetch({data : {type : 'sms'}});
					break;
				case 'twitter':
					promise = messages.fetch({data : {type : 'twitter'}});
					break;
				// Filter by archived status. Will also default to incoming only
				case 'archived':
					promise = messages.fetch({data : {status : 'archived'}});
					break;
				// Show all statuses. Will still default to incoming only
				case 'all':
					promise = messages.fetch({data : {status : 'all'}});
					break;
				// Grab default: incoming + received + all types
				default:
					promise = messages.fetch();
					break;
			}

			promise.done(function(){
				messages.each(function(model)
				{

					replies = new MessageCollection();
					replies.fetch({data : {contact : model.get('contact').id}})
						.done(function(){
							model.replies = replies;
							App.layout.mainRegion.show(new MessageListView({
								collection : messages
							}));
					});
				});
			});
		}
	};

	return MessageListController;
});