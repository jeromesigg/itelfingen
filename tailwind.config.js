import flowbite from 'flowbite/plugin'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./app/Http/Controllers/*.php",
        "./resources/**/*.{js,vue,ts}",
        "./node_modules/flowbite/**/*.js"
    ],
}
