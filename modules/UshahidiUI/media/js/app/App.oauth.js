define(['backbone', 'jso2', 'jquery'],
	function(Backbone, jso2, $) {
		
		jso2.enablejQuery($);
		
		var oauth = new jso2('google', {
			// @todo change client_id and ensure it always exists
			client_id: "demoapp",
			authorization: "/oauth/authorize",
			redirect_uri: "/",
			scopes: {
				request: ["posts", "forms", "api"],
				require: ["posts"]
			}
		});
		
		oauth.callback();
		
		Backbone.ajax = _.bind(oauth.ajax, oauth);
		
		return oauth;
	}); 