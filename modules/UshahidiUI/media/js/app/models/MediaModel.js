/**
 * Media Model
 *
 * @module     MediaModel
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['backbone', 'App', 'util/App.oauth', 'dropzone'],
	function(Backbone, App, OAuth, Dropzone)
	{
		// we do not want dropzone to auto-discover, because the upload path is
		// never stored in the DOM.
		Dropzone.autoDiscover = false;

		var MediaModel = Backbone.Model.extend(
		{
			urlRoot: App.config.baseurl + App.config.apiuri + '/media',
			dropzone : function ($el) {
				$el.dropzone({
					url: this.urlRoot,
					headers: OAuth.getAuthHeaders()
					});
			},
			save : function() {
				console.log('got a media model save', arguments, this.attributes);
			},
			schema : function ()
			{
				var schema = {
					url: {
						type: 'Text',
						title: 'URL',
					},
					caption: {
						type: 'Text',
						title: 'Caption',
					},
					mime: {
						type: 'Text',
						title: 'File Type',
					},
					// original_file_url: {
					// 	type: 'Text',
					// 	title: 'Original URL',
					// },
					// original_width: {
					// 	type: 'Text',
					// 	title: 'URL',
					// 	title: 'Original Width',
					// },
					// original_height: {
					// 	type: 'Text',
					// 	title: 'Original Height',
					// },
					medium_file_url: {
						type: 'Text',
						title: 'Medium URL',
					},
					medium_width: {
						type: 'Text',
						title: 'Medium Width',
					},
					medium_height: {
						type: 'Text',
						title: 'Medium Height',
					},
					thumbnail_file_url: {
						type: 'Text',
						title: 'Thumbnail URL',
					},
					thumbnail_width: {
						type: 'Text',
						title: 'Thumbnail Width',
					},
					thumbnail_height: {
						type: 'Text',
						title: 'Thumbnail Height',
					},
				};

				return schema;
			},
			validation : function ()
			{
				var rules = {
					url : {
						pattern : 'url',
						maxLength : 150,
						required : true
					},
					caption : {
						maxLength : 150,
						required: false
					},
					mime : {
						maxLength : 150,
						required: false
					},
					medium_file_url : {
						pattern : 'url',
						required: false
					},
					medium_width : {
						min: 1,
						max: 65535,
						required: false
					},
					medium_height : {
						min: 1,
						max: 65535,
						required: false
					},
					thumbnail_file_url : {
						pattern : 'url',
						required: false
					},
					thumbnail_width : {
						min: 1,
						max: 65535,
						required: false
					},
					thumbnail_height : {
						min: 1,
						max: 65535,
						required: false
					},
				};

				return rules;
			},
		});
		return MediaModel;
	});

