module.exports = function(grunt) {
	var uipath = 'modules/UshahidiUI/';

	grunt.initConfig(
	{
		pkg : grunt.file.readJSON('package.json'),

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

		uglify :
		{
			'minify-require-js' : {
				src : uipath + 'media/js/libs/require.js',
				dest : uipath + 'media/js/libs/require.min.js'
			}
		},

		jshint :
		{
			files : ['Gruntfile.js', uipath + 'media/js/app/**/*.js', '!' + uipath + 'media/js/app/**/*min.js'],
			options : {
				jshintrc : '.jshintrc'
			}
		},

		compass :
		{
			dev :
			{
				options :
				{
					config : uipath + 'config-dev.rb',
					basePath: 'modules/UshahidiUI'
				}
			},

			prod :
			{
				options :
				{
					config : uipath + 'config.rb',
					basePath: 'modules/UshahidiUI'
				}
			}
		},

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

		cmq :
		{
			files: {
				'media/css' : ['media/css/style.css']
			}
		}
	});

	require('load-grunt-tasks')(grunt);

	grunt.registerTask('test', ['jshint']);
	grunt.registerTask('build:js', ['requirejs', 'uglify']);
	grunt.registerTask('build:css', ['compass', 'cmq']);
	grunt.registerTask('build', ['build:js', 'build:css', 'imagemin']);
	grunt.registerTask('default', ['build']);

};
