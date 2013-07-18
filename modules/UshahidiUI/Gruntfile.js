module.exports = function(grunt) {

	grunt.initConfig(
	{
		pkg : grunt.file.readJSON('package.json'),

		requirejs :
		{
			mainJS :
			{
				options :
				{
					baseUrl : "media/js/app",
					wrap : false,
					name : "../libs/almond",
					preserveLicenseComments : false,
					optimize : "uglify",
					mainConfigFile : "media/js/app/config/Init.js",
					include : ["config/Init"],
					out : "media/js/app/config/Init.min.js"
				}
			}
		},

		jshint :
		{
			files : ['Gruntfile.js', 'media/js/app/**/*.js', '!media/js/app/**/*min.js'],
			options :
			{
				globals :
				{
					jQuery : true,
					console : false,
					module : true,
					document : true
				}
			}
		},

		compass :
		{
			dist :
			{
				options :
				{
					config : 'config.rb'
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

	grunt.loadNpmTasks('grunt-contrib-requirejs');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.registerTask('test', ['jshint']);
	grunt.registerTask('build', ['requirejs:mainJS', 'requirejs:mainCSS']);
	grunt.registerTask('default', ['test', 'build']);

};
