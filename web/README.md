# Working on UI

Best practices for working on the UI and such.

## Installing Build Tools

You will need RubyGems and NPM installed. Front end code is built with SASS, Compass, and Grunt, which you should probably install globally:

```sh
npm install -g grunt-cli
```

You will also need the the Gem [Bundler](http://bundler.io/):

```
gem install bundler
```

Now install client side gems and packages:

```sh
bundle install
npm install
```

Now you should be ready to monitor for changes and rebuild CSS as necessary:

```sh
grunt watch
```

