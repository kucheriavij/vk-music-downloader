var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/app', './assets/css/app.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // uncomment if you use less files
    // .enableLessLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
    .enableBuildNotifications()
;

module.exports = Encore.getWebpackConfig();
