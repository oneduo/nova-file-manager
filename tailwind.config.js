module.exports = {
  mode: 'jit',
  content: ['./resources/**/*{js,vue,blade.php}'],
  darkMode: 'class', // or 'media' or 'class'
  theme: {
    extend: {
      minHeight: {
        '[65]': '65vh',
      },
    },
  },
  plugins: [require('@tailwindcss/aspect-ratio')],
  important: '.nova-file-manager',
}
