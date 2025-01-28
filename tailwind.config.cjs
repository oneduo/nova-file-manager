const toRgba = (hexCode, opacity = 50) => {
  let hex = hexCode.replace('#', '');

  if (hex.length === 3) {
    hex = `${hex[0]}${hex[0]}${hex[1]}${hex[1]}${hex[2]}${hex[2]}`;
  }

  const r = parseInt(hex.substring(0, 2), 16);
  const g = parseInt(hex.substring(2, 4), 16);
  const b = parseInt(hex.substring(4, 6), 16);

  return `rgba(${r},${g},${b},${opacity / 100})`;
};

const flattenColorPalette = (obj, sep='-') => Object.assign(
  {},
  ...function _flatten(o, p='') {
    return [].concat(...Object.keys(o)
      .map(k =>
        typeof o[k] === 'object' ?
          _flatten(o[k],k+sep) :
          ({[p+k]: o[k]})
      )
    );
  }(obj)
);

module.exports = {
  mode: 'jit',
  content: ['./resources/**/*{js,vue,blade.php,ts}'],
  darkMode: 'class',
  important: '.nova-file-manager',
  plugins: [
    function ({ addUtilities, theme }) {
      const utilities = {
        '.bg-stripes': {
          backgroundImage:
            'linear-gradient(135deg, var(--stripes-color) 12.50%, transparent 12.50%, transparent 50%, var(--stripes-color) 50%, var(--stripes-color) 62.50%, transparent 62.50%, transparent 100%)',
          backgroundSize: '7.07px 7.07px',
        },
      }

      const addColor = (name, color) =>
        (utilities[`.bg-stripes-${name}`] = { '--stripes-color': color })

      const colors = flattenColorPalette(theme('backgroundColor'))

      for (let name in colors) {
        try {
          const [r, g, b, a] = toRgba(colors[name])
          if (a !== undefined) {
            addColor(name, colors[name])
          } else {
            addColor(name, `rgba(${r}, ${g}, ${b}, 0.4)`)
          }
        } catch (_) {
          addColor(name, colors[name])
        }
      }

      addUtilities(utilities)
    },
  ],
  safelist: [
    'max-w-5xl',
    'max-w-6xl',
    'max-w-7xl',
    'max-w-full',
  ]
}
