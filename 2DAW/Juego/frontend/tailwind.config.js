export default {
  darkMode: 'class',
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        // Semantic Palette
        primary: {
          DEFAULT: '#34D399', // Emerald 400 - Fresh, growth, nature
          dark: '#059669',    // Emerald 600
          light: '#6EE7B7',   // Emerald 300
          glow: 'rgba(52, 211, 153, 0.5)',
        },
        secondary: {
          DEFAULT: '#22D3EE', // Cyan 400 - Tech, holograms, data
          dark: '#0891B2',    // Cyan 600
          light: '#67E8F9',   // Cyan 300
          glow: 'rgba(34, 211, 238, 0.5)',
        },
        accent: {
          DEFAULT: '#FBBF24', // Amber 400 - Energy, gold, warmth
          dark: '#D97706',    // Amber 600
          light: '#FDE68A',   // Amber 200
          glow: 'rgba(251, 191, 36, 0.5)',
        },
        danger: {
          DEFAULT: '#FB7185', // Rose 400 - Soft alert
          dark: '#E11D48',    // Rose 600
          glow: 'rgba(251, 113, 133, 0.5)',
        },
        surface: {
          DEFAULT: '#0F172A', // Slate 900 - Deep space/night
          light: '#1E293B',   // Slate 800 - Panel
          lighter: '#334155', // Slate 700 - Hover/Border
          highlight: '#475569', // Slate 600
        }
      },
      fontFamily: {
        sans: ['Quicksand', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        mono: ['"Fira Code"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
      },
      animation: {
        'float': 'float 3s ease-in-out infinite',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'glow': 'glow 2s ease-in-out infinite alternate',
        'slide-up': 'slideUp 0.3s ease-out',
        'pop': 'pop 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275)',
        'shine': 'shine 3s infinite',
        'fade-in': 'fadeIn 0.3s ease-out',
        'scale-in': 'scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1)',
        'progress': 'progress 2s ease-in-out infinite',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-5px)' },
        },
        glow: {
          'from': { boxShadow: '0 0 5px currentColor' },
          'to': { boxShadow: '0 0 20px currentColor' },
        },
        slideUp: {
          'from': { opacity: '0', transform: 'translateY(10px)' },
          'to': { opacity: '1', transform: 'translateY(0)' },
        },
        pop: {
          '0%': { transform: 'scale(0.9)' },
          '100%': { transform: 'scale(1)' },
        },
        shine: {
          '100%': { left: '125%' },
        },
        fadeIn: {
          'from': { opacity: '0' },
          'to': { opacity: '1' },
        },
        scaleIn: {
          'from': { opacity: '0', transform: 'scale(0.95)' },
          'to': { opacity: '1', transform: 'scale(1)' },
        },
        progress: {
          '0%': { transform: 'scaleX(0)', opacity: '0.5' },
          '50%': { transform: 'scaleX(0.5)', opacity: '1' },
          '100%': { transform: 'scaleX(1)', opacity: '0' },
        }
      },
      boxShadow: {
        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
        'neon-primary': '0 0 10px rgba(52, 211, 153, 0.5), 0 0 20px rgba(52, 211, 153, 0.3)',
        'neon-secondary': '0 0 10px rgba(34, 211, 238, 0.5), 0 0 20px rgba(34, 211, 238, 0.3)',
      }
    },
  },
  plugins: [],
}
