import wordpress from '@wordpress/eslint-plugin';
import eslintConfigPrettier from 'eslint-config-prettier/flat';

export default [
	{
		ignores: ['eslint.config.mjs', 'webpack.config.js', 'build/**'],
	},
	...wordpress.configs.recommended,
	eslintConfigPrettier,
	{
		rules: {
			'prettier/prettier': 'off',
		},
	},
];
