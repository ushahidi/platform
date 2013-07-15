/* Backbone.validateAll.js - v0.1.0 - 2012-08-29
* http://www.gregfranko.com/Backbone.validateAll.js/
* Copyright (c) 2012 Greg Franko; Licensed MIT */

// Locally passes in the `window` object, the `document` object, and an `undefined` variable.  The `window` and `document` objects are passed in locally, to improve performance, since javascript first searches for a variable match within the local variables set before searching the global variables set.  All of the global variables are also passed in locally to be minifier friendly. `undefined` can be passed in locally, because it is not a reserved word in JavaScript.
(function (window, document, undefined) {
    // Checks to make sure Backbone, Backbone.Model and the private validate method are on the page
    if(window.Backbone && window.Backbone.Model && window.Backbone.Model.prototype._validate) {
        // Run validation against the next complete set of model attributes,
        // returning `true` if all is well. If a specific `error` callback has
        // been passed, call that instead of firing the general `"error"` event.
        window.Backbone.Model.prototype._validate = function(attrs, options) {
            if (options.silent || !this.validate) {
                return true;
            }
            if (options.validateAll !== false) {
                attrs = _.extend({}, this.attributes, attrs);
            }
            var error = this.validate(attrs, options);
            if (!error) {
                return true;
            }
            if (options && options.error) {
                options.error(this, error, options);
            } else {
                this.trigger('error', this, error, options);
            }
            return false;
        };
    }
}(window, document));