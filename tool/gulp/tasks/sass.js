const { src, dest } = require( 'gulp' );
const sass          = require( 'gulp-sass' )( require( 'sass' ) );
const autoprefixer  = require( 'gulp-autoprefixer' );
const rename        = require( 'gulp-rename' );
const sourcemaps    = require( 'gulp-sourcemaps' );
const using         = require( 'gulp-using' );
const cache         = require( 'gulp-cached' );

module.exports = class GulpTaskSass {
  static src = '';
  static callback( cb ) {

    // Normal CSS
    console.log( 'gulp sass called' );
    src( GulpTaskSass.src )
      .pipe( sass().on( 'error', sass.logError ) )
      .pipe( using( { prefix: 'Processing SASS, using' } ) )

      // for different browsers
      .pipe( autoprefixer( 'last 10 versions' ) )

      // source map
      .pipe( sourcemaps.write( { includeContent: false } ) )
      .pipe( sourcemaps.init( { loadMaps: true } ) )
      .pipe( sourcemaps.write( '.' ) )

      .pipe( dest( function( file ) {
          return file.base;
      } ) );

    // Minified CSS
    src( GulpTaskSass.src )
      .pipe( using( { prefix: 'Minifying CSS, using' } ) )
      .pipe( sass().on( 'error', sass.logError ) )
      .pipe( sass( { outputStyle: 'compressed' } ) )
      .pipe( autoprefixer( 'last 10 versions' ) )
      .pipe( rename( { extname: '.min.css' } ) )
      .pipe( dest(function (file) {
          return file.base;
      } ) );

    cb();
  }
};