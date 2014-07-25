/**
 * Message Model
 *
 * @module     MessageModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'modules/config', 'backbone-model-factory'],
	function(Backbone, config) {
		var MessageModel = Backbone.ModelFactory(
		{
			urlRoot: config.get('apiurl') + 'messages',
			toString : function ()
			{
				return this.get('message');
			},
			// Overriding the parse method to handle nested JSON values
			parse : function (data)
			{
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
