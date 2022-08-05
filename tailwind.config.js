module.exports = {
  mode: 'jit',
  content: ['./resources/**/*{js,vue,blade.php}'],
  darkMode: 'class',
  plugins: [require('@tailwindcss/aspect-ratio')],
  important: '.nova-file-manager',
}
