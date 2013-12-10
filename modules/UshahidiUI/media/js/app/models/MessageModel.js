/**
 * Message Model
 *
 * @module     MessageModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['jquery', 'backbone', 'App'],
	function($, Backbone, App) {
		var MessageModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + App.config.apiuri + '/messages',
			toString : function ()
			{
				return this.get('message');
			},
			// Overriding the parse method to handle nested JSON values
			parse : function (data)
			{
				if (data.data_feed !== null && data.data_feed.id !== null)
				{
					data.data_feed = data.data_feed.id;
				}

				if (data.post !== null && data.post.id !== null)
				{
					data.post = data.post.id;
				}

				if (data.parent !== null && data.parent.id !== null)
				{
					data.parent = data.parent.id;
				}

				/*if (data.contact !== null && data.contact.id !== null)
				{
					data.contact = data.contact.id;
				}*/

				return data;
			},
			isArchived : function()
			{
				return (this.get('status') === 'archived');
			},
			isIncoming : function()
			{
				return (this.get('direction') === 'incoming');
			},
		});

		return MessageModel;
	});