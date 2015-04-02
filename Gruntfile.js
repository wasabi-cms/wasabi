module.exports = function(grunt) {
  "use strict";

  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-requirejs');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    banner: '/*!\n' +
      ' * <%= pkg.title %> v<%= pkg.version %>\n' +
      ' */\n',

    less: {
      compile: {
        options: {
          sourceMap: true
        },
        files: {
          'webroot/css/all.css': 'Assets/less/all.less',
          'webroot/core/css/styles.css': 'Plugin/Core/Assets/less/styles.less',
          'webroot/core/css/debug.css': 'Plugin/Core/Assets/less/debug.less',
          'webroot/core/css/install.css': 'Plugin/Core/Assets/less/install.less',
          'webroot/cms/css/cms.css': 'Plugin/Cms/Assets/less/cms.less',
          'webroot/cms/css/live-edit.css': 'Plugin/Cms/Assets/less/live-edit.less'
        }
      },
      minify: {
        options: {
          cleancss: true,
          report: 'gzip'
        },
        files: {
          'webroot/css/all.min.css': 'webroot/css/all.css',
          'webroot/core/css/styles.min.css': 'webroot/core/css/styles.css',
          'webroot/core/css/debug.min.css': 'webroot/core/css/debug.css',
          'webroot/core/css/install.min.css': 'webroot/core/css/install.css',
          'webroot/cms/css/cms.min.css': 'webroot/cms/css/cms.css',
          'webroot/cms/css/live-edit.min.css': 'webroot/cms/css/live-edit.css'
        }
      }
    },

    requirejs: {
      compile: {
        options: {
          baseUrl: 'Plugin/Core/webroot/js/src',
//          mainConfigFile: 'webroot/js/src/app.js',
//          out: 'webroot/js/app.min.js',
          dir: 'webroot/js',
          findNestedDependencies: true,
          stubModules: ['text', 'hbs'],
          modules: [
            {
              name: 'common',
//              include: [
//                'Plugin/Core/webroot/js/src/common'
//              ]
            }//,
//            {
//              name: 'core',
//              include: ['core'],
//              exclude: ['common']
//            },
//            {
//              'name': '../../../../Cms/webroot/js/src/cms',
//              include: ['Plugin/Cms/webroot/js/src/cms'],
//              exclude: ['common']
//            }
          ],
          optimize: 'uglify2',
          uglify2: {
            compress:{
              global_defs: {
                DEBUG: false
              }
            }
          }
        }
      }
    },

    watch: {
      default: {
        files: ['Assets/less/*.less'],
        tasks: ['less']
      }
    }

  });

  grunt.registerTask('default', ['less'/*, 'requirejs'*/]);

};
