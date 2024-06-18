module.exports = {
    content: [
    './module/**/*.phtml',
    './public/**/*.html',
    ],
    theme: {
        extend: {},
    },
    darkMode: 'selector',

    plugins: [
    require('daisyui'),
    ],

    daisyui: {
        themes: [
            "light",   // Default theme
            "dark",
            "cupcake",
            "bumblebee",
            "emerald",
            "corporate",
            "synthwave",
            "retro",
            "cyberpunk",
            "valentine",
            "halloween",
            "garden",
            "forest",
            "aqua",
            "lofi",
            "pastel",
            "fantasy",
            "wireframe",
            "black",
            "luxury",
            "dracula",
            "cmyk",
            "autumn",
            "business",
            "acid",
            "lemonade",
            "night",
            "coffee",
            "winter"
        ],
        darkTheme: "winter"
    },
};
