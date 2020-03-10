# Setting up the Pattern Library for development

## Setting up the pattern library for development

The pattern library holds all styles for the platform-client and its here changes to the css is made.

**Note:** If you are not planning to change the css for the Platform-client, you **do not** need to follow this guide.

Clone the pattern library

```text
git clone https://github.com/ushahidi/platform-pattern-library.git;
```

Install the dependencies.

```text
cd platform-pattern-library;
npm install;
```

Run `gulp build` to generate the fonts and compile the sass the first time.

```text
gulp build
```

Start the pattern library in [http://localhost:8000](http://localhost:8000) by running:

```text
gulp
```

Once the pattern-library is running, the front-end guidelines and a guide on how to work with the pattern-library and its structure can be found on [http://localhost:8000/assets/html/front-end-guidelines/introduction.html](http://localhost:8000/assets/html/front-end-guidelines/introduction.html)

## Further reading

After setup, please read the [Platform Pattern Library](../../front-end-development/changing-ui-styles-introduction-to-the-pattern-library/) guidelines when working with the Platform Pattern Library.

