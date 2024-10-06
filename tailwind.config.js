module.exports = {
	mode: 'jit',
	darkMode: 'class',
	content: ['./app/app/views/**/*.php', './node_modules/preline/dist/*.js'],
	plugins: [require('preline/plugin')],
}
