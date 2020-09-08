# Using the changed styles in platform-client

Make the changes you need in the css in the Pattern Library.

1. If you have made changes to the html or added new classes, you need to make those changes in the html in the **Platform Client** as well
2. Commit and push to your fork of the Pattern Library.
3. In the directory where you cloned the **platform-client** repository for local development, go to **package.json**.
4. Find the entry for **ushahidi-pattern-library**
5. Change to the url to the new repository + the new pattern library commit id  in this format: _"ushahidi-platform-pattern-library":"git://github.com/{your-own org}/ushahidi-platform-pattern-library-sivico.git\#{commit}"_
6. Do **npm install** in the platform-client
7. Go to **platform-client/node\_modules/ushahidi-pattern-library**
8. Run **npm install** there too \(this step is normally made by npm when using the Ushahidi-release so you need to do it yourself when using your own code\)
9. Now the Pattern Library is available in the platform-client development environment. To make the changes visible in your development environment, run **gulp build** in the platform-client again.
10. The css changes should be visible in the client now .

**Pattern Library: publishing a new version in your npm account and using it in the platform web client**

**Please note, the complete, most up to date information on how to publish and use npm is available in the npm documentation,** [**https://docs.npmjs.com**](https://docs.npmjs.com/) **.**

1. Go to **package.json** in your version of the Pattern Library
2. Change the name of the package to something else \(for example "YourProjectName-Pattern-Library"\)
3. Keep the version number unless you already made changes. If you made any changes, you should bump the version number.
4. Publish the package to npm through **npm publish**
5. In platform-client, go to the file: **package.json**
6. Find “ushahidi-pattern-library” and change to "YourProjectName-Pattern-Library": "v3.12.4" \(substitute version number to the one you published\)
7. Do **npm-install** and **gulp build** again
8. Each time the pattern-library is updated:
   1. Change the version-number in YourProjectName-Pattern-Librarys package.json
   2. Do **npm publish**
   3. Update to the new version-number in **package.json** in **platform-client**
9. Do **npm install** and **gulp build** in the platform-client again.

