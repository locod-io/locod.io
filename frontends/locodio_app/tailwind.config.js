/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./index.html",
        "./src/**/*.{vue,js,ts,jsx,tsx,css}",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Roboto', 'Helvetica', 'sans-serif'],
                serif: ['Merriweather', 'Times', 'serif'],
                mono: ['Fira Code'],
            },
            backgroundColor: {
                'gray-900': '#1D1E27',
            },
        },
    },
    plugins: [],
    darkMode: 'class',
}

// theme: {
//   fontFamily: {
//     'sans': ['ui-sans-serif', 'system-ui', ...],
//     'serif': ['ui-serif', 'Georgia', ...],
//     'mono': ['ui-monospace', 'SFMono-Regular', ...],
//     'display': ['Oswald', ...],
//     'body': ['"Open Sans"', ...],
//   }
// }
// font-family: 'Inter', sans-serif;
// font-family: 'Neuton', serif;

