module.exports = function(grunt) {
  "use strict";

  grunt.loadNpmTasks('mantri');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    banner: '/*!\n' +
            ' * <%= pkg.title %> v<%= pkg.version %>\n' +
            ' *\n' +
            ' * Copyright (c) 2013 Frank Förster (http://frankfoerster.com)\n' +
            ' * Licensed under the MIT License\n' +
            ' */\n',

    mantriDeps: {
      options: {
        root: './'
      },
      target: {
        src: 'src',
        dest: './deps.js'
      }
    },

    mantriBuild: {
      options: {
        debug: false
      },
      target: {
        dest: './build/<%= pkg.name %>.min.js'
      }
    },

    watch: {
      default: {
        files: ['src/**/*.js'],
        tasks: ['mantriDeps', 'mantriBuild']
      }
    }

  });

  grunt.registerTask('default', ['mantriDeps', 'mantriBuild']);

};
