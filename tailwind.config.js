const colors = require('tailwindcss/colors');

export default {
    darkMode: 'class',
    theme: {
        extend: {
            //
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
