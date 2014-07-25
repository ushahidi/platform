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

			var messages = new MessageCollection();

			switch (view)
			{
				// Filter by type. Will also default to incoming + received status
				case 'email':
					messages.fetch({data : {type : 'email'}});
					break;
				case 'sms':
					messages.fetch({data : {type : 'sms'}});
					break;
				case 'twitter':
					messages.fetch({data : {type : 'twitter'}});
					break;
				// Filter by archived status. Will also default to incoming only
				case 'archived':
					messages.fetch({data : {status : 'archived'}});
					break;
				// Show all statuses. Will still default to incoming only
				case 'all':
					messages.fetch({data : {status : 'all'}});
					break;
				// Grab default: incoming + received + all types
				default:
					messages.fetch();
					break;
			}

			App.layout.mainRegion.show(new MessageListView({
				collection : messages
			}));
		}
	};

	return MessageListController;
});