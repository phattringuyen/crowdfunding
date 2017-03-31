module.exports = function(grunt) {

  grunt.initConfig({
    compass: {
      options: {
        config: 'config.rb'
      },
      dist: {
        options: {
          environment: 'production',
          outputStyle: 'compressed',
        }
      },
      dev: {
        options: {
          environment: 'development',
          outputStyle: 'expanded' //nested, expanded, compact, compressed
        }
      },
    },
    coffee: {
      compile: {
        expand: true,
        flatten: true,
        cwd: 'assets/coffee',
        src: ['*.coffee'],
        dest: 'assets/js/',
        ext: '.js'
      },
    },
    watch: {
      css: {
        files: ['assets/sass/*.scss'],
        tasks: ['compass:dev']
      },
      scripts: {
        files: ['assets/coffee/*.coffee'],
        tasks: ['coffee', 'jshint']
      }
    },
    jshint: {
      all: ['Gruntfile.js', 'assets/javascripts/*.js']
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-coffee');
  grunt.loadNpmTasks('grunt-contrib-jshint');

};