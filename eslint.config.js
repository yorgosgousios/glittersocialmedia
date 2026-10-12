const js = require('@eslint/js');
const globals = require('globals');

module.exports = [
	{ ignores: ['theme/assets/js/vendor/**'] },
	js.configs.recommended,
	{
		files: ['theme/assets/js/**/*.js'],
		languageOptions: {
			ecmaVersion: 2022,
			sourceType: 'script',
			globals: globals.browser,
		},
	},
];
