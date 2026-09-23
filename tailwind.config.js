/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx,json}",
  ],
  theme: {
    extend: {
      colors: {
        edu: {
          blue: '#1e40af',
          sky: '#0284c7',
          amber: '#d97706',
          emerald: '#059669',
          purple: '#7c3aed',
          rose: '#e11d48'
        },
        brand: {
          purple: '#853AFF',
          'purple-dark': '#6c3fb8',
          'purple-light': '#f5f0ff'
        }
      },
      boxShadow: {
        '2xs': '0 1px 2px 0 rgba(0, 0, 0, 0.04)',
        'xs': '0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px -1px rgba(0, 0, 0, 0.06)',
        'soft': '0 2px 8px -1px rgba(0, 0, 0, 0.08), 0 1px 3px -1px rgba(0, 0, 0, 0.04)',
        'float': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.06)',
      },
      fontFamily: {
        sans: ['Roboto', 'system-ui', '-apple-system', 'sans-serif'],
      },
      fontSize: {
        '2xs': ['0.75rem', { lineHeight: '1rem' }],       // ~12.5px
        'xs': ['0.8125rem', { lineHeight: '1.2rem' }],    // ~13.5px (gốc 12px)
        'sm': ['0.9375rem', { lineHeight: '1.4rem' }],    // ~15.5px (gốc 14px)
        'base': ['1.0625rem', { lineHeight: '1.6rem' }],   // ~17.5px (gốc 16px)
        'lg': ['1.1875rem', { lineHeight: '1.75rem' }],   // ~19.5px (gốc 18px)
        'xl': ['1.3125rem', { lineHeight: '1.875rem' }],  // ~21.5px (gốc 20px)
        '2xl': ['1.625rem', { lineHeight: '2.125rem' }],  // ~26.5px (gốc 24px)
        '3xl': ['2rem', { lineHeight: '2.5rem' }],
      }
    },
  },
  plugins: [],
}
