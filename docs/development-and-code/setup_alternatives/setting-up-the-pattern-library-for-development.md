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

### But how do I test my new styles in the Platform?

The pattern-library is installed as a npm-package in the Platform Client. For development, it is not suitable to publish a package for each change, and luckily, there is hack you can do to test the styles locally instead.

1. Make sure the Pattern-library is built through running `gulp build` in the terminal
2. Copy all contents in the "assets" folder
3. Go to your Platform-client folder
4. Navigate to node\_modules/ushahidi-platform-pattern-library folder
5. Remove the contents in the "assets" folder
6. Paste the contents you copied from the assets-folder in the pattern-library 
7. Done! Now you should be able to see the changes in the Platform Client

## Further reading

After setup, please read the [Platform Pattern Library](../../front-end-development/changing-ui-styles-introduction-to-the-pattern-library/) guidelines when working with the Platform Pattern Library.

