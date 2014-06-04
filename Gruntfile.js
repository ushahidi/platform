module.exports = function(grunt) {

	require('load-grunt-tasks')(grunt);

	var uipath = 'modules/UshahidiUI/',
		releaseFiles = [
			{
				src: [
					'**',
					// Deployment Files
					'!application/cache/**',
					'!application/logs/**',
					'!application/config/environments/**',
					'!application/routes/**',
					// Include the default routes file
					'application/routes/default.php',
					'!application/media/uploads/**',
					'!.htaccess',
					// Build Files
					'!build/**',
					// Dev Files
					'!application/tests/**',
					'!phpspec/**',
					'!spec/**',
					'!.travis.yml',
					'!.vagrant/**',
					'!composer.*',
					'!phpspec.yml.dist',
					'!package.json',
					'!.jshintrc',
					// IDE Files
					'!modules/UshahidiUI/.sass-cache/**',
					'!node_modules/**',
					'!modules/UshahidiUI/node_modules/**',
					'!.floo*',
					'!.project',
					'!.settings/**',
					'!.sublime-project*',
					'!.arc**',
					'!.DS_Store**',
					// SCM Files
					'!**/.git/**',
					'!**/.git*',
				],
				dest: 'Lamu/'
			},
		];

	// Set default filename for compress tasks
	grunt.option('filename', 'Lamu');

	grunt.initConfig(
	{
		pkg : grunt.file.readJSON('package.json'),

		// Auto browser prefixing on css
		autoprefixer :
		{
			options :
			{
				browsers : ['last 2 versions']
			},
			prod :
			{
				src : uipath + 'media/css/style.css'
			},
			dev :
				{
				src : uipath + 'media/css/test/style.css'
				}
		},

		// Minify images
		imagemin :
		{
			all :
			{
				files : [
				{
					expand : true,
					cwd : uipath + 'media/images',
					src : ['*.{png,jpg, jpeg}'],
					dest : uipath + 'media/images'
				}]
			}
		},

		// Combine and optimize JS
		requirejs :
		{
			mainJS :
			{
				options :
				{
					baseUrl : uipath + 'media/js/app',
					wrap : false,
					preserveLicenseComments : false,
					optimize : 'uglify',
					mainConfigFile : uipath + 'media/js/app/config/Init.js',
					name : 'config/Init',
					include : ['config/Init'],
					out : uipath + 'media/js/app/config/Init.min.js',
				}
			}
		},

		// Use uglify JS to minify require.js
		uglify :
		{
			'minify-require-js' : {
				src : uipath + 'media/bower_components/requirejs/require.js',
				dest : uipath + 'media/bower_components/requirejs/require.min.js'
			}
		},

		// JSHint checking of the JS app files
		jshint :
		{
			files : ['Gruntfile.js', uipath + 'media/js/app/**/*.js', '!' + uipath + 'media/js/app/**/*min.js'],
			options : {
				jshintrc : '.jshintrc'
			}
		},

		// Compass CSS build
		compass :
		{
			dev :
			{
				options :
				{
					config : uipath + 'config-dev.rb',
					basePath: 'modules/UshahidiUI',
					bundleExec: true
				}
			},

			prod :
			{
				options :
				{
					config : uipath + 'config.rb',
					basePath: 'modules/UshahidiUI',
					bundleExec: true
				}
			}
		},

		// Run PHPSpec tests
		phpspec:
		{
			core :
			{
				specs: 'spec/'
			},
			options :
			{
				prefix: 'bin/'
			}
		},

		// Wache SASS, CSS, JS, Specs
		watch :
		{
			sass :
			{
				files : [uipath + 'media/scss/**/*.scss'],
				tasks : ['compass:dev', 'compass:prod', 'cmq']
			},

			css :
			{
				files : [uipath + 'media/css/style.css', uipath + 'media/css/test/style.css'],
				options :
				{
					livereload : true
				}
			},

			js :
			{
				files : [uipath + 'media/js/**/*.js', uipath + 'media/js/**/templates/**/*.html'],
				options :
				{
					livereload : true
				}
			},

			specs :
			{
				files : ['spec/**/*.php'],
				tasks : ['phpspec']
			}
		},

		// Combine Media Queries
		cmq :
		{
			files: {
				'media/css' : ['media/css/style.css']
			}
		},

		// Create zip/tgz archives for release
		compress: {
			zip: {
				options: {
					archive: 'build/<%= grunt.option("filename") %>.zip'
				},
				files: releaseFiles
			},
			tar: {
				options: {
					archive: 'build/<%= grunt.option("filename") %>.tar.gz',
					mode: 'tgz'
				},
				files: releaseFiles
			}
		},

		// Clean release files
		clean : {
			release : ['build/**'],
			css : [uipath + 'media/css/style.css', uipath + 'media/css/test/style.css'],
			js : [uipath + 'media/js/app/config/Init.min.js', uipath + 'media/js/libs/require.min.js']
		}
	});

	grunt.registerTask('release', 'Create a release', function (version) {
		var done = this.async(),
			inquirer = require('inquirer');

		inquirer.prompt([{
			type: 'confirm',
			name: 'confirm',
			message : 'Releases must be built on a clean checkout without dev dependencies installed. Continue?',
			default : 'N',
		}], function( answers )
		{
			if (! answers.confirm) { done(); return; }

			if (! version) {
				grunt.warn('Version number must be specified, like release:v3.2.4');
			}

			grunt.option('filename', 'Lamu-'+version);
			grunt.task.run('build', 'compress');

			done();
		});
	});

	grunt.registerTask('test', ['jshint', 'phpspec']);
	grunt.registerTask('build:js', ['requirejs', 'uglify']);
	grunt.registerTask('build:css', ['compass', 'cmq']);
	grunt.registerTask('build', ['build:js', 'build:css', 'imagemin']);
	grunt.registerTask('default', ['build']);

};
