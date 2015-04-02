module.exports = function(grunt) {
  "use strict";

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-svg2web');

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    svg2web: {
      dist: {
        files: {
          "wasabi": ["wasabi.svg"]
        }
      }
    },

    watch: {
      default: {
        files: ["wasabi.svg"],
        tasks: ['svg2web']
      }
    }

  });

  grunt.registerTask('default', ['svg2web']);

};
