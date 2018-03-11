var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    // .enableSassLoader(function(sassOptions) {}, {
    //     resolveUrlLoader: false
    // })
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    // uncomment to define the assets of the project
    .addEntry('app', './assets/js/app.js')
    // .addStyleEntry('app', './assets/css/app.scss')
    // uncomment if you use Sass/SCSS files
    .enableSassLoader()
    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

;

module.exports = Encore.getWebpackConfig();
