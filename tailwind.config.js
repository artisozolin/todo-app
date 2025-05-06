module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                todo: {
                    grey: '#FFFFC5',
                    lightGreen: '#D3FF98',
                    green: '#99FF85',
                    yellow: '#FFCC33',
                    lightPink: '#FFE2C5',
                    lightPurple: '#C5C5FF',
                },
            },
            fontFamily: {
                barlow: ['Barlow-Regular'],
                barlowLight: ['Barlow-Light'],
                barlowMedium: ['Barlow-Medium'],
            },
            width: {
                'xl': '36rem',
            }
        },
    },
    plugins: [],
}
