module.exports = function( grunt ) {

	'use strict';

	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		concat: {
			css: {
		    	src: [ 
		    		'node_modules/leaflet/dist/leaflet.css ',
		    		'node_modules/leaflet.markercluster/dist/MarkerCluster.css',
		    		'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css'
		    	],
		    	dest: 'assets/css/bundle.css'
		  	},
		  	js: {
		    	src: [ 
		    		'node_modules/leaflet/dist/leaflet.js',
		    		'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js',
		    		'node_modules/leaflet-makimarkers/Leaflet.MakiMarkers.js'
		    	],
		    	dest: 'assets/js/bundle.js'
		  	}
		},

		copy: {
			main: {
				expand: true, 
				flatten: true,
				src: [ 'node_modules/leaflet/dist/images/*' ],
				dest: 'assets/css/images/',
			},
		},

		addtextdomain: {
			options: {
				textdomain: 'map-explorer',
			},
			update_all_domains: {
				options: {
					updateDomains: true
				},
				src: [ '*.php', '**/*.php', '!\.git/**/*', '!bin/**/*', '!node_modules/**/*', '!tests/**/*' ]
			}
		},

		wp_readme_to_markdown: {
			your_target: {
				files: {
					'README.md': 'readme.txt'
				}
			},
		},

		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: [ '\.git/*', 'bin/*', 'node_modules/*', 'tests/*' ],
					mainFile: 'map-explorer.php',
					potFilename: 'map-explorer.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},
	} );

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify');
	grunt.loadNpmTasks( 'grunt-contrib-copy');
	//grunt.loadNpmTasks( 'grunt-contrib-qunit');
	//grunt.loadNpmTasks( 'grunt-contrib-jshint');

	grunt.registerTask( 'default', [ 'i18n','readme' ] );
	grunt.registerTask( 'i18n', ['addtextdomain', 'makepot'] );
	grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );

	grunt.util.linefeed = '\n';

};
