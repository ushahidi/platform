/**
 * Oauth setup
 *
 * @module     App.oauth
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'jso2/jso2', 'jquery', 'underscore'],
	function(Backbone, Jso2, $, _)
	{
		Jso2.enablejQuery($);

		var oauth = new Jso2('ushahidi', {
			// @todo change client_id and ensure it always exists
			client_id: window.config.oauth.client,
			client_secret: window.config.oauth.client_secret,
			//authorization: window.config.baseurl + 'oauth/authorize',
			token: window.config.baseurl + 'oauth/token',
			redirect_uri: window.config.baseurl,
			scopes: {
				request: ['posts', 'forms', 'api', 'tags', 'sets', 'users', 'config'],
				require: ['posts', 'forms', 'api', 'tags', 'sets', 'users']
			},
			grant_type: 'client_credentials'
		});

		oauth.callback();

		// Ensure we have an access token before everything starts
		oauth.getToken(function() {
			// If we've got a token here, check if we're logged in etc.
		});

		Backbone.ajax = _.bind(oauth.ajax, oauth);

		return oauth;
	});