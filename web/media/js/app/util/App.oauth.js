/**
 * Oauth setup
 *
 * @module     App.oauth
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'jquery', 'underscore', 'alertify', 'ddt', 'util/App.storage', 'jquery.cookie'],
	function(Backbone, $, _, alertify, ddt, Storage)
	{
		// Boolean refresh: is this a refresh token?
		function getUserToken(refresh)
		{
			var cookie = $.cookie(refresh ? 'authrefresh' : 'authtoken'),
				token;
			if (cookie) {
				// Kohana signs cookies using "signature~value" format.
				token = cookie.match(/^.+~(.+)$/);
				if (token && token[1]) {
					ddt.log('OAuth', refresh ? 'refresh' : 'user', 'token', token[1]);
					return token[1];
				}
			}
		}

		// String token: new Bearer token
		// Boolean refresh: is this a refresh token?
		function setUserToken(token, refresh)
		{
			// Kohana signs cookies using "signature~value" format.
			var cookie = 'jsmodified~' + token;
			$.cookie(refresh ? 'authrefresh' : 'authtoken', cookie);
			ddt.log('OAuth', 'set user token', token, Boolean(refresh));
		}

		// Function callback: called with the token once fetched
		function getAnonymousToken(callback)
		{
			var token_params = {
					client_id: window.config.oauth.client,
					client_secret: window.config.oauth.client_secret,
					redirect_uri: window.config.baseurl,
					scope: required_scopes.join(' '),
					grant_type: 'client_credentials'
				};

			if (!this.request) {
				this.request = $.ajax({
					url: window.config.baseurl + 'oauth/token',
					type: 'POST',
					data: token_params,
					dataType: 'json',
					success: function(data)
					{
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

			return this.request.done(function()
			{
				// got a token, continue processing
				callback.call(this, anonymous_token);
			});
		}

		// String token: actual token that failed
		// Object settings: AJAX settings to be retried
		function refreshRequest(token, settings)
		{
			if (token === anonymous_token || !refresh_token) {
				// Anonymous tokens cannot be refreshed, just get a new one.
				clearToken(token);
				return UshahidiAuth.ajax(settings);
			}

			if (!this.request) {
				ddt.log('OAuth', 'Access token may have expired, attempting refresh');
				var token_params = {
						client_id: window.config.oauth.client,
						client_secret: window.config.oauth.client_secret,
						grant_type: 'refresh_token',
						refresh_token: refresh_token
					};

				this.request = $.ajax({
						url: window.config.baseurl + 'oauth/token',
						type: 'POST',
						data: token_params,
						dataType: 'json'
					})
					.done(function(data)
					{
						ddt.log('OAuth', 'got refresh token', data);
						setUserToken(user_token = data.access_token);
						if (data.refresh_token) {
							setUserToken(refresh_token = data.refresh_token, true);
						}
					})
					.fail(function()
					{
						alertify.error('Session expired, please log in again');
						clearToken(token);
					})
					.always(function()
					{
						this.request = null;
					});
			} else {
				ddt.log('OAuth', 'still refreshing token');
			}

			this.request.always(function()
			{
				UshahidiAuth.ajax(settings);
			});
		}

		// String token: actual token to be cleared
		function clearToken(token)
		{
			if (anonymous_token === token) {
				anonymous_token = null;
				anonymous_storage.clear();
			}
			if (user_token === token) {
				user_token = null;
				$.removeCookie('authtoken');
				$.removeCookie('authrefresh');
				var App = require('App');
				App.vent.trigger('config:change');
			}
		}

		// Object settings: AJAX settings that caused the error
		function handleTokenError(settings)
		{
			return function(xhr) {
				var token = user_token || anonymous_token,
					error,
					idx;
				if (xhr.status === 401 && xhr.responseJSON && $.isArray(xhr.responseJSON.errors)) {
					// Kohana returns HTTP exceptions as an array of errors
					for (idx in xhr.responseJSON.errors) {
						error = xhr.responseJSON.errors[idx];
						if (error && error.message.match(ACCESS_TOKEN_INVALID)) {
							ddt.log('OAuth', 'Clearing invalid token', token);
							if (!settings.is_a_retry) {
								// Attempt to refresh the token and try the request again, while also
								// preventing the request from falling into an infinite loop.
								settings.is_a_retry = true;
								refreshRequest(token, settings);
							} else {
								ddt.log('OAuth', 'Aborted AJAX request after two token failures');
								alertify.error('Session expired, please log in again');
								clearToken(token);
							}
						}
					}
				}
			};
		}

		var ACCESS_TOKEN_INVALID = /access token provided is expired, revoked, malformed, or invalid/,
			anonymous_storage = new Storage('Ushahidi', 'anonymous_access_token'),
			anonymous_token = anonymous_storage.get(),
			user_token = getUserToken(),
			refresh_token = getUserToken(true),
			required_scopes = ['posts', 'media', 'forms', 'api', 'tags', 'sets', 'users', 'stats'],
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
					var defer = $.Deferred();

					this.getToken(function(token) {
						if (!token) {
							defer.fail('Failed to get OAuth token, unable to make authenticated ajax call');
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
						return $.ajax(settings)
							.fail(handleTokenError(settings))
							.done(defer.resolve)
							.fail(defer.reject);
					});

					return defer.promise();
				}
			};

		UshahidiAuth.initialize();

		return UshahidiAuth;
	});
