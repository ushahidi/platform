# Changelog

### v3.0.0-alpha.3

* Post search by keyword
* Unpublished, published and all posts workspaces
* Data sources, including SMS (SMSSync, Twilio, Nexmo) and Email data providers
* Messages UI, including archive messages and creating posts from messages
* Current User API (/users/me) for returning the currently logged in user
* Login, Register and Logout UI
* Anonymous access. View the site and public data without logging in
* Manage Users UI
* Site Settings UI
* Access checks in the UI, aka hiding actions you can't actually use
* SASS Color themes
* Access Control layer in the API
* Edit posts
* Sets detail view wired with real data (but hidden for release)

### v3.0.0-alpha.2

* UI - create post
* Detailed LICENSE.md
* Added Changelog.md

### v3.0.0-alpha.1

* Initial development release
* REST API
  * Posts endpoint
  * Forms, groups, and attributes endpoints
  * Media endpoint
  * Tags endpoint
  * Users endpoint
  * Posts revisions and translations support
  * GeoJSON support on Posts endpoint
  * Supports OAuth2 for authentication
* UI
  * Backbone Marionette based JS frontend
  * Post listing, detail and delete
  * Map views
* UI Wireframes (in app but not powered by real data)
  * Sets listing, detail, create
  * Login, Register
  * Edit post
* Unit tests
* Build process
* Vagrant setup
