/**
 * Any target you want performed during build process should be named as:
 * build-{plugin_name}-anything
 */
module.exports = function(grunt, initConfig) {

	var files = {};
	var dir = String(__dirname).replace(initConfig.rootPath + '/', '');
	console.log(dir);
	files['webroot/css/croogo-bootstrap.css'] = dir + '/webroot/less/admin.less';
	files['webroot/css/croogo-bootstrap-responsive.css'] = dir + '/webroot/less/admin-responsive.less';

	return {
		less: {
			'build-croogo-bootstrap': {
				options: {
					compress: false,
					paths: [
						'webroot/vendors',
						dir + '/webroot/less'
					],
				},
				files: files
			}
		},
		concat: {
			'build-croogo-bootstrap': {
				src: [
					'webroot/vendors/bootstrap/js/bootstrap-transition.js',
					'webroot/vendors/bootstrap/js/bootstrap-alert.js',
					'webroot/vendors/bootstrap/js/bootstrap-button.js',
					'webroot/vendors/bootstrap/js/bootstrap-carousel.js',
					'webroot/vendors/bootstrap/js/bootstrap-collapse.js',
					'webroot/vendors/bootstrap/js/bootstrap-dropdown.js',
					'webroot/vendors/bootstrap/js/bootstrap-modal.js',
					'webroot/vendors/bootstrap/js/bootstrap-tooltip.js',
					'webroot/vendors/bootstrap/js/bootstrap-popover.js',
					'webroot/vendors/bootstrap/js/bootstrap-scrollspy.js',
					'webroot/vendors/bootstrap/js/bootstrap-tab.js',
					'webroot/vendors/bootstrap/js/bootstrap-typeahead.js',
					'webroot/vendors/bootstrap/js/bootstrap-affix.js'
				],
				dest: 'webroot/js/croogo-bootstrap.js'
			}
		}
	}
};
