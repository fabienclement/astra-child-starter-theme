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
	// jQuery vient de WordPress : webpack ne l'empaquette pas.
	externals: {
		jquery: 'jQuery',
	},
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
					// Tarteaucitron reste hors du paquet : il déduit son dossier
					// de document.currentScript, puis y cherche sa langue, son
					// catalogue de services et sa feuille de styles. Le script
					// minifié charge les autres fichiers en .min.
					...[
						'tarteaucitron.min.js',
						'tarteaucitron.services.min.js',
						'css/tarteaucitron.min.css',
						'lang/tarteaucitron.fr.min.js',
					].map((file) => ({
						from: path.resolve(
							process.cwd(),
							'node_modules/tarteaucitronjs',
							file
						),
						to: path.resolve(
							process.cwd(),
							'build/tarteaucitron',
							file
						),
					})),
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
