# Changing UI styles: introduction to the pattern library

The Platform Client uses the Pattern Library to standardize the styles and have them available in a single place that can be edited by frontend developers without advanced knowledge of AngularJS.

The Pattern Library runs as a dependency of the web client, it's included in the package.json file and is just a [regular npm library](https://www.npmjs.com/package/ushahidi-platform-pattern-library). Please note that unless you specifically need to change the layout beyond what's possible in the client's codebase, or want to change styles \(such as colors, fonts, etc\) the pattern library will **not** need to be setup as a stand alone project.

## Use cases for editing \(or not\) the pattern library

### Use case: adding a new view to the client

To add new views in the Platform Client, we recommend you first check if you can compose those views with pre-existing patterns from our pattern library.

You can see the available patterns here:

{% embed url="http://preview.ushahidi.com/platform-pattern-library/master/" caption="" %}

### Use case: changing the color palette

To change the color palette, you will need to modify the pattern library's color variables. The good thing is that all colors are defined here, and you can easily change them and see the results in the patterns before moving forward with applying the changes to the client.

{% embed url="http://preview.ushahidi.com/platform-pattern-library/master/assets/html/1\_basics/" caption="" %}

### Changing other styles like icons, fonts, or adding layouts

When you need completely new layouts with new styles, you will need to add or modify the pattern library.

We recommend you review the different structures in the pattern library before starting:

{% embed url="http://preview.ushahidi.com/platform-pattern-library/master/index.html" caption="" %}

## Setting up the pattern library for development

Find the setup guide in

{% page-ref page="../../development-and-code/setup\_alternatives/setting-up-the-pattern-library-for-development.md" %}

### Working with the pattern library

Continue reading in this chapter to get a full introduction to the pattern library.

