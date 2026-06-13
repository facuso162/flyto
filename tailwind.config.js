/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './public/**/*.php',
    './app/**/*.php'
  ],
  theme: {
    extend: {
      colors: {
        flyto: {
          ink: '#1c1c1a',
          navy: '#1e2d4a',
          sand: '#f2f1ef',
          muted: '#6b6b60',
          gold: '#c9a96e',
          mist: '#e8e4da'
        }
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        display: ['"Playfair Display"', 'Georgia', 'serif'],
        mono: ['"DM Mono"', 'ui-monospace', 'monospace']
      },
      boxShadow: {
        flyto: '0 1px 1.5px rgba(0, 0, 0, 0.1), 0 1px 1px rgba(0, 0, 0, 0.1)'
      }
    }
  },
  plugins: []
};
