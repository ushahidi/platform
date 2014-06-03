/**
 * Oauth setup
 *
 * @module     App.oauth
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'jquery', 'underscore', 'ddt', 'util/App.storage'],
	function(Backbone, $, _, ddt, Storage)
	{
		function getUserToken() {
			var cookie = $.cookie('authtoken'),
				token;
			if (cookie) {
				// Kohana signs cookies using "signature~value" format.
				token = cookie.match(/^.+~(.+)$/);
				if (token && token[1]) {
					ddt.log('OAuth', 'got user token', token[1]);
					return token[1];
				}
			}
		}

		function getAnonymousToken(callback)
		{
			var token_params = {
					client_id: window.config.oauth.client,
					client_secret: window.config.oauth.client_secret,
					redirect_uri: window.config.baseurl,
					scope: required_scopes.join(' '),
					grant_type: 'client_credentials'
				};

			if (!anonymous_storage.request) {
				anonymous_storage.request = $.ajax({
					url: window.config.baseurl + 'oauth/token',
					type: 'POST',
					data: token_params,
					dataType: 'json',
					success: function(data) {
						ddt.log('OAuth', 'got anonymous token', data.access_token);
					}
				})
				.done(function(data)
				{
					// this only needs to run once
					anonymous_token = data.access_token;
					anonymous_storage.set(anonymous_token);
				});
			} else {
				ddt.log('OAuth', 'still fetching anonymous token');
			}

			return anonymous_storage.request.done(function()
			{
				// got a token, continue processing
				callback.call(this, anonymous_token);
			});
		}

		function clearToken(token)
		{
			if (anonymous_token === token) {
				anonymous_token = null;
				anonymous_storage.clear();
			}
			if (user_token === token) {
				user_token = null;
			}
		}

		function handleTokenError(settings)
		{
			return function(xhr) {
				var token = user_token || anonymous_token,
					error,
					idx;
				if (xhr.status === 400 && xhr.responseJSON && $.isArray(xhr.responseJSON.errors)) {
					// Kohana returns HTTP exceptions as an array of errors
					for (idx in xhr.responseJSON.errors) {
						error = xhr.responseJSON.errors[idx];
						if (error && error.message === ACCESS_TOKEN_INVALID) {
							ddt.log('OAuth', 'Clearing invalid token', token);
							clearToken(token);
							// Attempt the AJAX request again without the bad token, while also
							// preventing the request from falling into an infinite loop.
							if (!settings.is_a_retry) {
								settings.is_a_retry = true;
								return UshahidiAuth.ajax(settings);
							} else {
								ddt.log('OAuth', 'Aborted AJAX request after two token failures');
							}
						}
					}
				}
			};
		}

		var ACCESS_TOKEN_INVALID = 'Access token is not valid',
			anonymous_storage = new Storage('Ushahidi', 'anonymous_access_token'),
			anonymous_token = anonymous_storage.get(),
			user_token = getUserToken(),
			required_scopes = ['posts', 'media', 'forms', 'api', 'tags', 'sets', 'users'],
			all_scopes = required_scopes.concat(['config', 'messages', 'dataproviders']),
			UshahidiAuth = {
				initialize : function()
				{
					_.bindAll(this, 'getAuthCodeParams', 'getToken', 'ajax');

					// Use authenticated AJAX calls
					Backbone.ajax = this.ajax;
				},
				getAuthCodeParams : function() {
					return {
						response_type: 'code',
						client_id: window.config.oauth.client,
						redirect_uri: window.config.baseurl + 'user/oauth',
						scope: all_scopes.join(' ')
					};
				},
				getClientType : function()
				{
					return user_token ? 'user' : 'anonymous';
				},
				getToken: function(callback)
				{
					var defer = $.Deferred();
					if (user_token) {
						defer.resolve(user_token);
					}
					else if (anonymous_token) {
						defer.resolve(anonymous_token);
					}
					else {
						getAnonymousToken(function(token) {
							defer.resolve(token);
						})
						.fail(function() {
							defer.reject();
						});
					}
					defer.always(callback);
					return defer.promise();
				},
				ajax: function(settings)
				{
					return this.getToken(function(token) {
						if (!token) {
							throw 'Failed to get OAuth token, unable to make authenticated ajax call';
						}
						if (!settings) {
							settings = {};
						}
						if (!settings.headers) {
							settings.headers = {};
						}
						settings.headers.Authorization = 'Bearer ' + token;
						ddt.log('OAuth', 'making AJAX request', settings);
						return $.ajax(settings).fail(handleTokenError(settings));
					});
				}
			};

		UshahidiAuth.initialize();

		return UshahidiAuth;
	});
