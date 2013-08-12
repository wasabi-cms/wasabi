module.exports = function(grunt) {
  "use strict";

  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-concat');

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    /* ========================================================================
     * Bootstrap: dropdown.js v3.0.0
     * http://twbs.github.com/bootstrap/javascript.html#dropdowns
     * ========================================================================
     * Copyright 2012 Twitter, Inc.
     *
     * Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     *
     * http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing, software
     * distributed under the License is distributed on an "AS IS" BASIS,
     * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
     * See the License for the specific language governing permissions and
     * limitations under the License.
     * ======================================================================== */

    banner: '/*!\n' +
            ' * Bootstrap: dropdown.js v<%= pkg.version %>\n' +
            ' * http://twbs.github.com/bootstrap/javascript.html#dropdowns\n' +
            ' *\n' +
            ' * Copyright 2012 Twitter, Inc.\n' +
            ' *\n' +
            ' * Licensed under the Apache License, Version 2.0 (the "License");\n' +
            ' * you may not use this file except in compliance with the License.\n' +
            ' * You may obtain a copy of the License at\n' +
            ' *\n' +
            ' * http://www.apache.org/licenses/LICENSE-2.0\n' +
            ' */\n',

    concat: {
      options: {
        banner: '<%= banner %>',
        separator: ';'
      },
      build: {
        src: ['src/**/*.js'],
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
