/*
 * grunt-contrib-less
 * http://gruntjs.com/
 *
 * Copyright (c) 2013 Tyler Kellen, contributors
 * Licensed under the MIT license.
 */

'use strict';

module.exports = function(grunt) {
    // Project configuration.
    grunt.initConfig({
        less: {
            compile: {
                options: {
                    paths: ['oc-admin/themes/modern/less'],
                    yuicompress: true
                },
                files: {
                    'oc-admin/themes/modern/css/main.css': 'oc-admin/themes/modern/less/main.less'
                }
            }
	    },
	    sass: {
	        dist: {
		        options: {
		            style:   'compressed',
		            compass: true
		        },
		        files: {
		            'oc-content/themes/bender/css/main.css': 'oc-content/themes/bender/sass/main.scss'
		        }
	        }
        }
    });

    // Actually load this plugin's task(s).
    grunt.loadTasks('tasks');

    grunt.loadNpmTasks('grunt-contrib-less');

    grunt.loadNpmTasks('grunt-contrib-sass');
};