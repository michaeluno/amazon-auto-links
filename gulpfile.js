const gulp = require( 'gulp' );
const requireDir = require( 'require-dir' );
const tasks = requireDir('./tool/gulp/tasks/');

// command: gulp sass
tasks.sass.src = [ './**/*.scss', '!./**/component/gutenberg/**', '!node_modules/**', '!library/**', '!tool/**', '!test/**' ];
exports.sass   = tasks.sass.callback;

// command: gulp watch
exports.watch   = gulp.series( function( cb ) {
    gulp.watch( tasks.sass.src, tasks.sass.callback );
  }
);

// gulp
exports.default = exports.watch;