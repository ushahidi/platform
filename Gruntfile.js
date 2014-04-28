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
					name : '../libs/almond',
					preserveLicenseComments : false,
					optimize : 'uglify',
					mainConfigFile : uipath + 'media/js/app/config/Init.js',
					include : ['config/Init'],
					out : uipath + 'media/js/app/config/Init.min.js'
				}
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

		watch :
		{
			sass :
			{
				files : [uipath + 'media/scss/**/*.scss'],
				tasks : ['compass:dev', 'compass:prod']
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
			}
		}
	});

	require('load-grunt-tasks')(grunt);

	grunt.registerTask('test', ['jshint']);
	grunt.registerTask('build', ['requirejs', 'imagemin', 'compass']);
	grunt.registerTask('default', ['jshint', 'requirejs', 'compass']);

};
