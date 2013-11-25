/**
 * Oauth setup
 *
 * @module     App.oauth
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'jso2', 'jquery', 'underscore'],
	function(Backbone, Jso2, $, _)
	{
		
		Jso2.enablejQuery($);
		
		var oauth = new Jso2('ushahidi', {
			// @todo change client_id and ensure it always exists
			client_id: window.config.oauth.client,
			authorization: window.config.baseurl + 'oauth/authorize',
			redirect_uri: window.config.baseurl,
			scopes: {
				request: ['posts', 'forms', 'api', 'tags', 'sets', 'users'],
				require: ['posts', 'forms', 'api', 'tags', 'sets', 'users']
			}
		});
		
		oauth.callback();
		
		Backbone.ajax = _.bind(oauth.ajax, oauth);
		
		return oauth;
	});