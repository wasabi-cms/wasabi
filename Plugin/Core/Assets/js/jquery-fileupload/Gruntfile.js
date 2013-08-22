module.exports = function(grunt) {
  "use strict";

  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-concat');

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    banner: '/*!\n' +
            ' * <%= pkg.title %> v<%= pkg.version %>\n' +
            ' * https://github.com/blueimp/jQuery-File-Upload\n' +
            ' *\n' +
            ' * Copyright (c) 2010, Sebastian Tschan\n' +
            ' * Licensed under the MIT License\n' +
            ' */\n',

    concat: {
      options: {
        banner: '<%= banner %>',
        separator: ';'
      },
      build: {
        src: [
          'src/load-image.js',
          'src/load-image-meta.js',
          'src/canvas-to-blob.js',
          'src/jquery.iframe-transport.js',
          'src/jquery.fileupload.js',
          'src/jquery.fileupload-process.js',
          'src/jquery.fileupload-image.js',
          'src/jquery.fileupload-audio.js',
          'src/jquery.fileupload-video.js',
          'src/jquery.fileupload-validate.js'
        ],
        dest: 'build/<%= pkg.name %>.js'
      }
    },

    uglify: {
      options: {
        banner: '<%= banner %>',
        mangle: true,
        wrap: false
      },
      build: {
        files: {
          'build/<%= pkg.name %>.min.js': ['<%= concat.build.dest %>']
        }
      }
    },

    watch: {
      default: {
        files: ['src/**/*.js'],
        tasks: ['concat', 'uglify']
      }
    }

  });

  grunt.registerTask('default', ['concat', 'uglify']);

};
