---
description: >-
  //TODO: Add section for open-source users on how to add changed styles to
  their project.
---

# How to Apply to the Platform



### Make changes

After the [initial installation and setup](http://preview.ushahidi.com/platform-pattern-library/assets/html/front-end-guidelines/installation/) follow these instructions:

* Make sure gulp is running in your Terminal.
* Edit and save the appropriate Sass files.
* View [Basics](http://preview.ushahidi.com/platform-pattern-library/assets/html/1_basics/), [Fragments](http://preview.ushahidi.com/platform-pattern-library/assets/html/2_fragments/), [Modules](http://preview.ushahidi.com/platform-pattern-library/assets/html/3_modules/), [Blocks](http://preview.ushahidi.com/platform-pattern-library/assets/html/4_blocks/) or [Layouts](http://preview.ushahidi.com/platform-pattern-library/assets/html/5_layouts/) to see your styles in action.
* Commit your changes.

### Push changes to NPM

* _Assuming you have already committed your changes_
* Bump and tag a new version using npm version prerelease for a test release or npm version release for a production release
* Push the new version to npm using npm publish
* Finally, push the new tag to git using git push --tags origin

Note: The "version": "\#\#\#" in pattern library package.json should stay in sync with the client package.json.

### Test your changes on a local client

* From within your platform-client installation run npm install ../platform-pattern-library/
* Run gulp in your platform-client install
* Check the results in the browser

