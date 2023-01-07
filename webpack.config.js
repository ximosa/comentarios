/**
 * External dependencies
 */
const webpack = require( 'webpack' );
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

module.exports = {
	...defaultConfig,
	devtool: 'source-map',
	// Override externals so dependencies can be packaged with the assets
	// because the minimum WordPress version is still 4.9.
	externals: {
		jquery: 'jQuery',
		$: 'jQuery',
	},
	resolve: {
		...defaultConfig.resolve,
		modules: [
			`${ __dirname }/assets/js/src`,
			'node_modules',
		],
	},
	entry: {
		// JS.
		app: './assets/js/src/frontend',
		admin: './assets/js/src/admin/index.js',
		notices: './assets/js/src/admin/notices.js', // Separately to be enqueued on all pages.
	},
	output: {
		filename: 'assets/js/build/[name].min.js',
		path: __dirname,
	},
	plugins: [
		new webpack.ProvidePlugin( {
			Promise: 'es6-promise-promise',
			$: 'jquery',
		} ),
		new MiniCSSExtractPlugin( {
			esModule: false, 
			filename: 'assets/css/build/[name].min.css',
		} ),
	],
};
