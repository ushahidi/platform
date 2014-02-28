module.exports = function(grunt) {

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
				files :
				{
					'media/css/test/style.css' : 'media/css/global.css'
				}
			}
		},

		imagemin :
		{
			all :
			{
				files : [
				{
					expand : true,
					cwd : 'media/images',
					src : ['*.{png,jpg, jpeg}'],
					dest : 'media/images'
				}]
			}
		},

		requirejs :
		{
			mainJS :
			{
				options :
				{
					baseUrl : 'media/js/app',
					wrap : false,
					name : '../libs/almond',
					preserveLicenseComments : false,
					optimize : 'uglify',
					mainConfigFile : 'media/js/app/config/Init.js',
					include : ['config/Init'],
					out : 'media/js/app/config/Init.min.js'
				}
			}
		},

		jshint :
		{
			files : ['Gruntfile.js', 'media/js/app/**/*.js', '!media/js/app/**/*min.js'],
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
					config : 'config-dev.rb'
				}
			},

			prod :
			{
				options :
				{
					config : 'config.rb' // compass config file is located in project root
				}
			}
		},

		watch :
		{
			sass :
			{
				files : ['media/scss/**/*.scss'],
				tasks : ['compass']
			}
		}
	});

	require('load-grunt-tasks')(grunt);
	
	grunt.registerTask('test', ['jshint']);
	grunt.registerTask('build', ['requirejs', 'imagemin', 'compass']);
	grunt.registerTask('default', ['jshint', 'requirejs', 'compass']);

};
