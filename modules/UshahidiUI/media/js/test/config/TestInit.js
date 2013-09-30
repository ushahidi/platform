require.config({
    baseUrl:"./js/app",
    // 3rd party script alias names (Easier to type "jquery" than "libs/jquery, etc")
    // probably a good idea to keep version numbers in the file names for updates checking
    paths:{
        // Core Libraries
        "jquery":"./js/libs/jquery",
        "jqueryui":"./js/libs/jqueryui",
        "underscore":"./js/libs/lodash",
        "backbone":"./jslibs/backbone",
        "marionette":"./jslibs/backbone.marionette",
        "handlebars":"./jslibs/handlebars",

        // Plugins
        "backbone.validateAll":"./jslibs/plugins/Backbone.validateAll",
        "bootstrap":"./jslibs/plugins/bootstrap",
        "text":"./jslibs/plugins/text"
    },
    // Sets the configuration for your third party scripts that are not AMD compatible
    shim:{
        "bootstrap":["jquery"],
        "jqueryui":["jquery"],
        "backbone":{
            "deps":["underscore"],
            // Exports the global window.Backbone object
            "exports":"Backbone"
        },
        "marionette":{
            "deps":["underscore", "backbone", "jquery"],
            // Exports the global window.Marionette object
            "exports":"Marionette"
        },
        "handlebars":{
            "exports":"Handlebars"
        },
        // Backbone.validateAll plugin (https://github.com/gfranko/Backbone.validateAll)
        "backbone.validateAll":["backbone"],

        // Jasmine Unit Testing
        "jasmine":{
            // Exports the global 'window.jasmine' object
            "exports":"jasmine"
        },

        // Jasmine Unit Testing helper
        "jasmine-html":{
            "deps":["jasmine"],
            "exports":"jasmine"
        }
    }
});

// Include Desktop Specific JavaScript files here (or inside of your Desktop router)
require(["jquery", "backbone", "marionette", "jasmine-html", "jquerymobile", "bootstrap", "backbone.validateAll"],
    function ($, Backbone, Marionette, jasmine) {
        var specs = ['test/specs/spec'];

        $(function () {
            require(specs, function () {
                jasmine.getEnv().addReporter(new jasmine.TrivialReporter());
                jasmine.getEnv().execute();
            });
        });
    });