module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        requirejs: {
            mainJS: {
                options: {
                    baseUrl: "media/js/app",
                    wrap: false,
                    name: "../libs/almond",
                    preserveLicenseComments: false,
                    optimize: "uglify",
                    mainConfigFile: "media/js/app/config/Init.js",
                    include: ["config/Init"],
                    out: "media/js/app/config/Init.min.js"
                }
            },
            mainCSS: {
                options: {
                    optimizeCss: "standard",
                    cssIn: "./media/css/app.css",
                    out: "./media/css/app.min.css"
                }
            }
        },
        jshint: {
            files: ['Gruntfile.js', 'media/js/app/**/*.js', '!media/js/app/**/*min.js'],
            options: {
                globals: {
                    jQuery: true,
                    console: false,
                    module: true,
                    document: true
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.registerTask('test', ['jshint']);
    grunt.registerTask('build', ['requirejs:mainJS', 'requirejs:mainCSS']);
    grunt.registerTask('default', ['test', 'build']);

};