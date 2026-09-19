/** Configuración para el binario independiente de Tailwind (tailwindcss.exe). */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Support/**/*.php',
    ],
    safelist: [
        'w-3', 'h-3', 'w-4', 'h-4', 'w-5', 'h-5', 'w-6', 'h-6',
        'w-7', 'h-7', 'w-8', 'h-8', 'w-9', 'h-9', 'w-10', 'h-10',
        'w-11', 'h-11', 'w-12', 'h-12',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc',
                    400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca',
                },
                cyanx: { 300: '#67e8f9', 400: '#22d3ee', 500: '#17b8cf', 600: '#0e7490' },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [require('@tailwindcss/forms')],
};
