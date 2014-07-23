/**
 * Create Post
 *
 * @module     CreatePostView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define([ 'App', 'marionette', 'underscore', 'alertify', 'hbs!templates/modals/CreatePost',
	'dropzone',
	'util/App.oauth',
	'models/MediaModel',
	'collections/MediaCollection',
	'backbone-validation', 'forms/UshahidiForms', 'hbs!templates/partials/tag-with-icon', 'select2'],
	function( App, Marionette, _, alertify, template,
		Dropzone,
		OAuth,
		MediaModel,
		MediaCollection,
		BackboneValidation, BackboneForm, tagWithIcon)
	{

		var postMedia = new MediaCollection(),
			// dropzone calls the API, which needs authentication, so we capture
			// the current oauth headers and send them with the POST request.
			authSend = function (file, xhr) {
				var headers = OAuth.getAuthHeaders(),
					header;
				for (header in headers) {
					xhr.setRequestHeader(header, headers[header]);
				}
			},
			mediaUploadConfig = {
				url: MediaModel.prototype.urlRoot,
				sending: authSend,
				addRemoveLinks: true,
				dictRemoveFileConfirmation: 'Are you sure you want to delete this file?'
			},
			formatTagSelectChoice = function (tag)
			{
				if (! tag.id)
				{
					return tag.text;
				}

				var model = App.Collections.Tags.get(tag.id);

				if (! model)
				{
					return tag.text;
				}

				return tagWithIcon(model.toJSON());
			};

		// prevent dropzone from attempting to attach automatically, we want to
		// create it manually when we render the view.
		Dropzone.autoDiscover = false;

		return Marionette.ItemView.extend( {
			template: template,
			initialize : function ()
			{
				// Set up the form
				this.form = new BackboneForm({
					model: this.model,
					idPrefix : 'post-',
					className : 'create-post-form',
					fieldsets : _.result(this.model, 'fieldsets')
					});
				BackboneValidation.bind(this, {
					valid: function(/* view, attr */)
					{
						// Do nothing, displaying errors is handled by backbone-forms
					},
					invalid: function(/* view, attr, error */)
					{
						// Do nothing, displaying errors is handled by backbone-forms
					}
				});

				// Trigger event when modal is fully opened, used to refresh map size
				this.on('modal:open', function ()
				{
					this.form.trigger('dom:refresh');
				});

				// Will be replaced with a Dropzone
				this.zone = {};
			},
			events: {
				'submit form' : 'formSubmitted',
				'click .js-switch-fieldset' : 'switchFieldSet',
				'click .js-back-button' : 'goBack'
			},
			onShow: function()
			{
				if (!App.feature('media_uploads')) {
					return;
				}

				// todo:
				// - need to associate media with posts via hidden form fields (js)
				// - need to read media ids in post creation (php)
				// - clean this up! use a composite view, maybe?

				var that = this,
					mediaLoaded = postMedia.fetch({ data: { orphans: true }});

				// Create a new dropzone...
				this.zone = this.$('.post-media-wrapper .dropzone').dropzone(mediaUploadConfig).get(0).dropzone;

				// ... after an upload, add new media into the collection
				this.zone.on('success', function (file, res) {
					var media = new MediaModel(res);

					// add the new media to the collection
					postMedia.add(media);

					// set the new, anonymous file name
					// TODO: this doesn't modify the DOM, needs more work
					// file.name = _.last(media.attributes.original_file_url.split('/'));
				});

				// ... after confirmation, delete the media records
				this.zone.on('removedfile', function(file) {
					var media = postMedia.get(file.mediaId);
					if (media) {
						media.destroy();
						alertify.success('Media file deleted.');
					}
				});

				// ... and load all the orphaned media
				// the user can choose to delete media, etc at this point.
				mediaLoaded.done(function() {
					postMedia.forEach(function(media) {
						var mockFile = {
								mediaId: media.attributes.id,
								name: _.last(media.attributes.original_file_url.split('/')),
								size: media.attributes.original_file_size,
								type: media.attributes.mime
							},
							mockThumb = media.attributes.thumbnail_file_url;

						// Call the default addedfile event handler
						that.zone.emit('addedfile', mockFile);

						// And optionally show the thumbnail of the file:
						that.zone.emit('thumbnail', mockFile, mockThumb);
					});
				});
			},
			onDomRefresh : function()
			{
				// Render the form and add it to the view
				this.form.render();

				// Set form id, backbone-forms doesn't do it.
				this.form.$el.attr('id', 'create-post-form');

				this.$('.post-form-wrapper').append(this.form.el);

				this.$('#post-tags').select2({
					allowClear: true,
					formatResult: formatTagSelectChoice,
					formatSelection: formatTagSelectChoice,
					escapeMarkup: function(m) { return m; }
				});
			},
			formSubmitted : function (e)
			{
				var that = this,
					errors,
					request;

				e.preventDefault();

				errors = this.form.commit({ validate: true });

				if (! errors)
				{
					request = this.model.save();
					if (request)
					{
						request
							.done(function (model /*, response, options*/)
								{
									alertify.success('Post saved.');
									App.appRouter.navigate('posts/' + model.id, { trigger : true });
									that.trigger('close');
								})
							.fail(function (response /*, xhr, options*/)
								{
									alertify.error('Unable to save post, please try again.');
									// validation error
									if (response.errors)
									{
										// @todo Display this error somehow
										console.log(response.errors);
									}
								});
					}
					else
					{
						alertify.error('Unable to save post, please try again.');
						console.log(this.model.validationError);
					}
				}
			},
			switchFieldSet : function (e)
			{
				var $el = this.$(e.currentTarget);
				// Add active class to fieldset
				this.$('fieldset').removeClass('active');
				this.$('#fieldset-' + $el.attr('fieldset')).addClass('active');
				// Add active class to nav
				this.$('.form-options-nav dd').removeClass('active');
				$el.parent().addClass('active');

				e.preventDefault();
			},
			goBack : function(e) {
				e.preventDefault();
				App.vent.trigger('post:create');
			},
			onClose : function ()
			{
				BackboneValidation.unbind(this);

				this.$('#post-tags').select2('destroy');
			},
			serializeData: function()
			{
				var data = _.extend(this.model.toJSON(), {
					isPublished : this.model.isPublished(),
					tags : this.model.getTags(),
					user : this.model.user ? this.model.user.toJSON() : null,
					fieldsets : _.result(this.model, 'fieldsets'),
					enable_media_uploads : App.feature('media_uploads')
				});
				return data;
			}
		});
	});
