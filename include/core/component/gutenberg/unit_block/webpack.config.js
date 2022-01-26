const path = require("path");
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
// module.exports = defaultConfig;

const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  ...defaultConfig, // existing default config

  // entry: {
  //   index: path.resolve(__dirname, "src/index.js"),
  //   style: path.resolve(__dirname, "src/style.js"),
  // },

	entry: {
		index: path.resolve( process.cwd(), 'src', 'index.js' ),
		style: path.resolve( process.cwd(), 'src', 'style.scss' ),
		editor: path.resolve( process.cwd(), 'src', 'editor.scss' ),
	},
};