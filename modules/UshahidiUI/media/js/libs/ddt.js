/**
 * Copyright (c) 2013-2014, deviantART, Inc.
 * Licensed under 3-Clause BSD.
 * Refer to the LICENCES.txt file for details.
 * For latest version, see https://github.com/deviantART/ddt
 */
/* jshint eqeqeq:true, laxcomma:true, laxbreak:true */
(function(window) {

// define constants and private variables
var  REGEX_ALL_ALPHA = /^[a-zA-Z]+$/
    ,IN_IFRAME = window.parent !== window
    ,ddt = window.ddt // capture existing ddt object
    ,util = {}; // private utility methods

// if ddt was not predefined, create it now
if (typeof ddt !== 'object') {
    window.ddt = ddt = {};
}

// always start at version 1
ddt.version = 1;

// set any undefined configuration options to defaults
if (typeof ddt.config !== 'object') {
    ddt.config = {};
}
if (!ddt.config.server) {
    ddt.config.server = '/ddt/?channels=';
}
if (!ddt.config.cookie) {
    ddt.config.cookie = 'ddt_watch';
}
if (!ddt.config.domains) {
    ddt.config.domains = [window.location.host.split('.').slice(-2).join('.')];
}

// cookie helpers
util.cookie = {};

// gets the cookie for the current domain
// config: ddt.config.cookie
util.cookie.get =  function() {
    var  regex  = new RegExp('(?:^|; )' + encodeURIComponent(ddt.config.cookie) + '=([^;]+)')
        ,result = regex.exec(document.cookie);
    return result ? String(decodeURIComponent(result[1])).split(',') : [];
};

// sets the cookie for all domains by loading an image from each domain
// config: ddt.config.server, ddt.config.domains
util.cookie.set = function() {
    var channels = ddt.watching().join(',')
        ,img
        ,i;
    for (i = 0; i < ddt.config.domains.length; i++) {
        // UDP style, we do absolutely nothing to ensure a successful request
        img = new Image();
        img.src = '//' + ddt.config.domains[i] + ddt.config.server + channels;
    }
    util.sync(++ddt.version);
};

// deletes the cookie for the current domain
// config: ddt.config.cookie, ddt.config.domains
util.cookie.del = function() {
    var expires = new Date()
        ,domain = window.location.host
        ,i;
    // ensure there is a matching domain to delete
    for (i = 0; i < ddt.config.domains.length; i++) {
        // typically the configured domain will be less restrictive, which is
        // why we search for the configured domain inside the current domain.
        if (domain.indexOf(ddt.config.domains[i]) !== -1) {
            // expire 24 hours ago (in ms)
            expires.setTime(expires.getTime() - 86400000);
            document.cookie = i = encodeURIComponent(ddt.config.cookie) + '='
                + '; expires=' + expires.toUTCString()
                + '; path=/'
                + '; domain=.' + ddt.config.domains[i];
            return util.sync(++ddt.version);
        }
    }
};

// get a regex that will match all domain URLs
util.regex = function() {
    return new RegExp('^(https?:)?\\/\\/([^.]+\\.)?(' + ddt.config.domains.join('|').replace('.', '\\.') + ')\\b', 'i');
};

// sync helper, uses postMessage to sync changes to watched channels
// starts as a noop until requirements are checked
util.sync = function() {};

// console proxy generator
util.proxy = function(type) {
    if (!console || !(type in console)) {
        console.warn('[ddt] cannot proxy this method, it is not defined in console', type);
        return function() {};
    }
    return function(name, message /*, ... */) {
        var params;
        if (ddt.watching(name)) {
            params = Array.prototype.slice.call(arguments, 1);
            // reformat the message to include the package name
            params[0] = '[' + name + '] ' + message;
            console[type].apply(console, params);
        }
    };
};

// helper for supporting two styles of invocation:
// func('foo', 'bar', 'baz')
// func(['foo', 'bar', 'baz'])
util.args = function(args) {
    if (!args.length) {
        return false;
    }
    args = Array.prototype.slice.call(args, 0);
    if (args[0] instanceof Array) {
        return args[0];
    }
    return args;
};

// helper for warning about invalid package name
util.warning = function(method, channel) {
    return console.warn('[ddt] invalid channel name', channel, 'when calling', method);
};

// attempt to enable postMessage syncing
if (window.postMessage && typeof JSON === 'object' && typeof JSON.parse === 'function') {
    util.sync = function(version) {
        var  msg = JSON.stringify({ddt: true, version: version, channels: ddt.watching()})
            ,regex_domains = util.regex()
            ,origin = '*'
            ,frames = document.getElementsByTagName('iframe')
            ,frame
            ,i;

        for (i = 0; i < frames.length; i++) {
            frame = frames[i];
            // we only sync to frames that are part of DDT domains
            if (frame.src && regex_domains.test(frame.src)) {
                // and only to frames that have DDT loaded
                if (frame.contentWindow.ddt && frame.contentWindow.ddt.version < version) {
                    ddt.log('ddt', 'syncing channels down to', frame.src, 'v' + version);
                    frame.contentWindow.postMessage(msg, origin);
                }
            }
        }

        if (IN_IFRAME && window.parent.ddt && window.parent.ddt.version < version) {
            ddt.log('ddt', 'syncing channels up to', String(window.parent.name || window.parent.location), 'v' + version);
            window.parent.postMessage(msg, origin);
        }
    };

    window.onmessage = function(event) {
        var  msg = (event || {}).data
            ,regex_domains = util.regex();

        if (!msg || !event.origin || !regex_domains.test(event.origin)) {
            return; // not a valid DDT message
        }

        try {
            msg = JSON.parse(msg);
        } catch (err) { /* ignore */ }
        if (!msg || !msg.ddt || !msg.version || msg.version <= ddt.version) {
            return; // not avalid DDT message, or already in sync
        }

        ddt.version = msg.version;
        ddt.reset(msg.channels);

        // continue the sync
        util.sync(ddt.version);
        ddt.log('ddt', 'updated watch list for', String(window.name || window.location), 'to v' + msg.version, ddt.watching());
    };
} else {
    // no postMessage support in this browser, disable sync
    console.warn('[ddt] postMessage and/or JSON support not available, sync disabled');
}

// create DDT within a separate closure to ensure that the watched list is
// never directly manipulated by any utility method. if multiple DDT instances
// were allowed, this would be a globally available functional decorator.
(function() {

// private variables for DDT
var  watched = {};

// set up the ddt -> console proxy methods.
ddt.log   = util.proxy('log');
ddt.info  = util.proxy('info');
ddt.warn  = util.proxy('warn');
ddt.error = util.proxy('error');

// proxy trace as log + trace
ddt.trace = function(name /*, message, ... */) {
    if (ddt.watching(name)) {
        ddt.log.apply(ddt, arguments);
        console.trace();
    }
};

ddt.reset = function(names) {
    var i;
    names = util.args(arguments);
    watched = {}; // reset watched list
    if (!names) {
        return false;
    }
    for (i = 0; i < names.length; i++) {
        if (REGEX_ALL_ALPHA.test(names[i])) {
            watched[names[i].toLowerCase()] = true;
        } else {
            util.warning(names[i]);
        }
    }
    return true;
};

// start watching a channel
ddt.watch = function(names) {
    var  changed = false
        ,i;
    names = util.args(arguments);
    if (!names) {
        return false;
    }
    for (i = 0; i < names.length; i++) {
        if (REGEX_ALL_ALPHA.test(names[i])) {
            watched[names[i].toLowerCase()] = changed = true;
        } else {
            util.warning(names[i]);
        }
    }
    if (changed) {
        util.cookie.set();
        return true;
    }
    return false;
};

// stop watching a channel
ddt.unwatch = function(names) {
    var  changed = false
        ,name
        ,i;
    names = util.args(arguments);
    if (!names) {
        return false;
    }
    for (i = 0; i < names.length; i++) {
        if (REGEX_ALL_ALPHA.test(name)) {
            name = names[i].toLowerCase();
            if (name in watched) {
                delete watched[name];
                changed = true;
            }
        } else {
            util.warning(names[i]);
        }
    }
    if (changed) {
        util.cookie.set();
        return true;
    }
    return false;
};

// am i watching channel X?
// or what channels am i watching?
ddt.watching = function(name) {
    var watching = [];
    if (name) {
        return name.toLowerCase() in watched;
    }
    for (name in watched) {
        watching.push(name);
    }
    return watching;
};

})();

// load saved channels list, if it exists
if (ddt.reset(util.cookie.get()) && !IN_IFRAME) {
    console.log('[ddt] watching', ddt.watching());
}

})(window);
