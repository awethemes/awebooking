'use strict';

const path = require('path');
const Encore = require('@symfony/webpack-encore');
const DependencyExtractionWebpackPlugin = require(
	'@wordpress/dependency-extraction-webpack-plugin'
);

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
	Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
	.setOutputPath('./build')
	.setPublicPath('/')
	.addAliases({
		'~': path.resolve(__dirname, 'resources/scripts')
	})

	/**
	 * ENTRY CONFIG
	 *
	 * Each entry will result in one JavaScript file (e.g. app.js)
	 * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
	 */
	.addEntry('dashboard', './resources/scripts/dashboard.tsx')

	// When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
	// .splitEntryChunks()

	// will require an extra script tag for runtime.js
	// but, you probably want this, unless you're building a single-page app
	// .enableSingleRuntimeChunk()
	.disableSingleRuntimeChunk()

	/**
	 * FEATURE CONFIG
	 *
	 * Enable & configure other features below. For a full
	 * list of features, see:
	 * https://symfony.com/doc/current/frontend.html#adding-more-features
	 */
	.cleanupOutputBeforeBuild()
	.enableBuildNotifications(true, (options) => {
		options.onlyOnError = true;
	})
	.enableSourceMaps(!Encore.isProduction())
	.enableVersioning(false)

	.enableSassLoader()
	.enablePostCssLoader()
	.enableReactPreset()
	.enableTypeScriptLoader()

	.addPlugin(new DependencyExtractionWebpackPlugin())
	.autoProvidejQuery();

module.exports = Encore.getWebpackConfig();
