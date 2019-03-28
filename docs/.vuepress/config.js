module.exports = {
    base: '/FatturaElettronica/',
    title: 'Fattura Elettronica',
    description: 'Fattura Elettronica',
    themeConfig: {
        navbar: true,
        nav: [
            { text: 'Guida', 'link': '/'},
            { text: 'Github', 'link': 'https://github.com/Weble/FatturaElettronica'}
        ],
        sidebar: [
            ['/', 'Introduzione'],
            ['/structure/', 'Struttura'],
            ['/parsing/', 'Lettura'],
            ['/writing/', 'Scrittura'],
            ['/manipulating/', 'Manipolazione'],
            ['/validating/', 'Validazione'],
        ]
    }
}