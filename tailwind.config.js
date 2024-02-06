const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./views/**/*.twig'],
  theme: {
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      white: colors.white,
      black: colors.black,
      gray: colors.gray,
      primary: colors.rose,
      danger: colors.red,
    },
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
