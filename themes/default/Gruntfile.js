module.exports = function(grunt) {

    grunt.initConfig({
        //pkg: grunt.file.readJSON('package.json'),
        requirejs: {
            mainJS: {
                options: {
                    baseUrl: "js/",
                    paths: {
                        "app": "app/config/Init"
                    },
                    wrap: true,
                    name: "libs/almond",
                    preserveLicenseComments: false,
                    optimize: "uglify",
                    mainConfigFile: "js/app/config/Init.js",
                    include: ["app"],
                    out: "js/app/config/Init.min.js"
                }
            },
            mainCSS: {
                options: {
                    optimizeCss: "standard",
                    cssIn: "./css/app.css",
                    out: "./css/app.min.css"
                }
            }
        },
        jshint: {
            files: ['Gruntfile.js', 'js/app/**/*.js', '!js/app/**/*min.js'],
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