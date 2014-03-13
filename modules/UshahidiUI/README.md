# Working on UI

Best practices for working on the UI and such.

## Installing Build Tools

You will need RubyGems and NPM installed. Front end code is built with SASS, Compass, and Grunt.

```sh
gem install sass compass compass-csslint
npm install -g grunt-cli
```

Now install client side dependencies:

```sh
npm install
```

Now you should be ready to monitor for changes and rebuild CSS as necessary:

```sh
grunt watch
```

