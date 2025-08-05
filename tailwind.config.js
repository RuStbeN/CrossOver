/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/View/Components/**/*.php",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#fff8f5',
          100: '#fff0e5',
          200: '#ffd9bf',
          300: '#ffb58a',
          400: '#ff8a54',
          500: '#ff6b35',
          600: '#e65020',
          700: '#bf3a15',
          800: '#992b12',
          900: '#7a220e',
        },
        dark: {
          50: '#f6f6f6',
          100: '#e7e7e7',
          200: '#d1d1d1',
          300: '#b0b0b0',
          400: '#888888',
          500: '#6d6d6d',
          600: '#5d5d5d',
          700: '#4f4f4f',
          800: '#454545',
          900: '#292929',
        },
        blue: {
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
        },
      },
      fontFamily: {
        poppins: ['Poppins', 'sans-serif'],
      },
      animation: {
        // Animaciones para el login (elementos decorativos)
        'login-float': 'login-float 6s ease-in-out infinite',
        'login-float-reverse': 'login-float-reverse 5s ease-in-out infinite',
        'login-float-slow': 'login-float-slow 8s ease-in-out infinite',
        'login-pulse': 'login-pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'login-rotate': 'login-rotate 20s linear infinite',
        'login-drift': 'login-drift 12s ease-in-out infinite',
        
        // Animaciones para partículas del app (fondo general)
        'particle-float': 'particle-float 8s ease-in-out infinite',
        'particle-drift': 'particle-drift 10s ease-in-out infinite',
        'pulse-slow': 'pulse-slow 6s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      keyframes: {
        // Keyframes específicos para LOGIN
        'login-float': {
          '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
          '50%': { transform: 'translateY(-20px) rotate(2deg)' },
        },
        'login-float-reverse': {
          '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
          '50%': { transform: 'translateY(15px) rotate(-2deg)' },
        },
        'login-float-slow': {
          '0%, 100%': { transform: 'translateY(0) translateX(0) scale(1) rotate(0deg)' },
          '33%': { transform: 'translateY(-30px) translateX(20px) scale(1.05) rotate(1deg)' },
          '66%': { transform: 'translateY(20px) translateX(-15px) scale(0.95) rotate(-1deg)' },
        },
        'login-pulse': {
          '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
          '50%': { opacity: '0.8', transform: 'scale(1.1)' },
        },
        'login-rotate': {
          '0%': { transform: 'rotate(0deg)' },
          '100%': { transform: 'rotate(360deg)' },
        },
        'login-drift': {
          '0%, 100%': { transform: 'translateX(0) translateY(0)' },
          '25%': { transform: 'translateX(20px) translateY(-10px)' },
          '50%': { transform: 'translateX(-15px) translateY(20px)' },
          '75%': { transform: 'translateX(10px) translateY(-15px)' },
        },
        
        // Keyframes para PARTÍCULAS del app
        'particle-float': {
          '0%, 100%': { transform: 'translateY(0) translateX(0)' },
          '50%': { transform: 'translateY(-80px) translateX(40px)' },
        },
        'particle-drift': {
          '0%, 100%': { transform: 'translateY(0) translateX(0) rotate(0deg)' },
          '33%': { transform: 'translateY(-60px) translateX(30px) rotate(120deg)' },
          '66%': { transform: 'translateY(40px) translateX(-20px) rotate(240deg)' },
        },
        'pulse-slow': {
          '0%, 100%': { opacity: '0.7' },
          '50%': { opacity: '0.3' },
        }
      }
    }
  },
  plugins: [],
}