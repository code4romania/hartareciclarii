const colors = require('tailwindcss/colors');

export default {
    darkMode: 'class',
    theme: {
        extend: {
          colors:{
              "primary": {
                  "50": "#D6F2FF",
                  "100": "#ADE5FF",
                  "200": "#5CCBFF",
                  "300": "#0AB1FF",
                  "400": "#007DB8",
                  "500": "#004667",
                  "600": "#003852",
                  "700": "#002A3D",
                  "800": "#001C29",
                  "900": "#000E14",
                  "950": "#00070A"
              }
          }
        }
    },
    content: [
        './app/**/*.php',
        './resources/js/**/*.{vue,js,ts,jsx,tsx}',
        './resources/views/**/*.blade.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    plugins: [
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
