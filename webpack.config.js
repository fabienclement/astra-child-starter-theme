// WordPress webpack config.
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

// Plugins.
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

// Utilities.
const path = require('path');
const isProd = process.env.NODE_ENV === 'production';

// Modifier cette URL pour chaque projet.
const PROXY_URL = 'https://site.local';

module.exports = {
	...defaultConfig,
	devtool: isProd ? false : 'source-map',
	...{
		entry: {
			'js/main': path.resolve(process.cwd(), 'src/js', 'main.js'),
			'css/main': path.resolve(process.cwd(), 'src/scss', 'main.scss'),
		},
		plugins: [
			...defaultConfig.plugins,
			new RemoveEmptyScriptsPlugin({
				stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
			}),
			new CopyWebpackPlugin({
				patterns: [
					{
						from: path.resolve(process.cwd(), 'assets'),
						to: path.resolve(process.cwd(), 'build/assets'),
						noErrorOnMissing: true,
					},
				],
			}),
			...(isProd
				? []
				: [
						new BrowserSyncPlugin({
							host: 'localhost',
							proxy: PROXY_URL,
							files: ['build/**/*', '**/*.php'],
							notify: false,
							https: false,
						}),
				  ]),
		],
	},
};
