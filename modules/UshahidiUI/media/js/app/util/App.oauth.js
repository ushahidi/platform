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
		var ushahidi_auth = {
			initialize : function ()
			{
				var that = this,
					token;

				_.bindAll(this, 'setProvider', 'login', 'logout', 'ajax');

				Jso2.enablejQuery($);

				this.providers = {};
				this.provider = null;

				this.providers.client_credentials = new Jso2('ushahidi_client_credentials', {
					client_id: window.config.oauth.client,
					client_secret: window.config.oauth.client_secret,
					//authorization: window.config.baseurl + 'oauth/authorize',
					token: window.config.baseurl + 'oauth/token',
					redirect_uri: window.config.baseurl,
					scopes: {
						request: ['posts', 'forms', 'api', 'tags', 'sets', 'users', 'config', 'messages'],
						require: ['posts', 'forms', 'api', 'tags', 'sets', 'users']
					},
					grant_type: 'client_credentials'
				});

				this.providers.implicit = new Jso2('ushahidi_implicit', {
					client_id: window.config.oauth.client,
					//client_secret: window.config.oauth.client_secret,
					authorization: window.config.baseurl + 'oauth/authorize',
					//token: window.config.baseurl + 'oauth/token',
					redirect_uri: window.config.baseurl,
					scopes: {
						request: ['posts', 'forms', 'api', 'tags', 'sets', 'users', 'config', 'messages'],
						require: ['posts', 'forms', 'api', 'tags', 'sets', 'users']
					},
					grant_type: 'implicit'
				});

				// Do we already have a logged in token?
				token = Jso2.store.getTokens('ushahidi_implicit');
				if (token.length > 0)
				{
					that.setProvider('implicit');
				}
				// Default to client_credentials grant type
				else
				{
					this.setProvider('client_credentials');
				}

				// Check for callback from implicit flow
				this.providers.implicit.callback(false, function()
				{
					// Check if we have tokens
					var token = Jso2.store.getTokens('ushahidi_implicit');
					if (token.length > 0)
					{
						that.setProvider('implicit');
					}
				});

				// Override backbone AJAX with our AJAX switcher
				Backbone.ajax = this.ajax;
			},
			/**
			 * Set OAuth Provider
			 * @param {String} provider_name Provider name: client_credentials or implicit
			 */
			setProvider : function(provider_name)
			{
				var provider = this.provider = this.providers[provider_name];

				// Ensure we have an access token before everything starts
				return provider.getToken(function() {
					// If we've got a token here, check if we're logged in etc.
				});
			},
			/**
			 * Login: Trigger login via implicit flow
			 */
			login : function ()
			{
				return this.setProvider('implicit');
			},
			/**
			 * Logout: Switch back to anonymous (client credentials)
			 */
			logout : function ()
			{
				var xhr = this.setProvider('client_credentials');
				this.providers.implicit.wipeTokens();
				return xhr;
			},
			/**
			 * Call the appropriate ajax function based on provider
			 */
			ajax : function()
			{
				return this.provider.ajax.apply(this.provider, arguments);
			}
		};

		ushahidi_auth.initialize();

		return ushahidi_auth;
	});