// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create web/build/app.js
    .addEntry('app', [
        './app/Resources/public/js/custom.js',
        ]
    )

    .createSharedEntry('vendor', [
        'jquery',
        './app/Resources/public/js/vendor/add-collection-widget.js',
        './app/Resources/public/js/bulma-slider-custom.js',
    ])

    // will create public/build/app.css
    .addStyleEntry('main', './app/Resources/public/css/main.scss')

    // .createSharedEntry('headjs', ['./node_modules/@fortawesome/fontawesome/index.js'])

    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    .enableVersioning()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();