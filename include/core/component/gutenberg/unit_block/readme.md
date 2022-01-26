# Unit Gutenberg Block

## Requirements
Run `npm install` on the plugin root directory where `package.json` resides.

### Node modules
 - @babel/core
 - @wordpress/babel-preset-default
 - babel-loader
 - css-loader
 - mini-css-extract-plugin
 - postcss-loader
 - postcss-preset-env
 - sass
 - sass-loader
 - webpack
 - webpack-cli

## Development Guideline

### Build 
To compile script files, run `webpack` in the terminal from this directory where `readme.me` is placed.

Or run `wp-scripts build`.

### Watch
To watch file changes and automatically start compiling while developing, run `webpack --watch`.  

Or run `wp-scripts watch`.
