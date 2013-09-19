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
					sassDir : 'media/scss',
					cssDir : 'media/css/test',
					outputStyle : 'expanded'
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
				files : ['media/scss/*.scss'],
				tasks : ['compass']
			}
		}
	});

	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-contrib-requirejs');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.registerTask('test', ['csslint', 'jshint']);
	grunt.registerTask('build', ['requirejs', 'imagemin', 'compass']);
	grunt.registerTask('default', ['requirejs', 'imagemin', 'compass']);

};
