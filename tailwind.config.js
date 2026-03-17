/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './views/**/*.php',
    './public/**/*.php',
    './public/js/**/*.js'
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'system-ui', 'sans-serif'],
        display: ['Outfit', 'Poppins', 'system-ui', 'sans-serif'],
      },
      colors: {
        primary: {
          50: '#fff7ed',
          100: '#ffedd5',
          200: '#fed7aa',
          300: '#fdba74',
          400: '#fb923c',
          500: '#f97316',
          600: '#ea580c',
          700: '#c2410c',
          800: '#9a3412',
          900: '#7c2d12',
        },
        surface: {
          50: '#fafafa',
          100: '#f5f5f5',
          200: '#e5e5e5',
          DEFAULT: '#ffffff',
          dark: '#171717',
        }
      },
      boxShadow: {
        'premium': '0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 4px 10px -5px rgba(0, 0, 0, 0.02)',
        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
      },
      backdropBlur: {
        xs: '2px',
      }
    }
  },
  plugins: []
};
