# Baun Asset Plugin

A Baun plugin that adds gulp asset manifest helper

- [Laravel Repository on Packagist](https://packagist.org/packages/torann/baun-asset-manifest)
- [Laravel Repository on GitHub](https://github.com/torann/baun-asset-manifest)

## Installation

From the command line run:

```
$ composer require torann/baun-asset-manifest
```

Once installed you need to register the plugin with the application. Open up `config/plugins.php` append the plugin namespace.

```php
<?php

return [

    ...

    'Torann\AssetManifest\Manifest',

];
```

#### Asset directory

Add the asset directory name, relative to the public directory, in you `config/app.php` file.

```php
<?php

return [

    ...

    // Assets folder name
    'assets_dir' => 'assets',
    
];
```

## Assets using Gulp

Example gulp file that creates the manifest JSON file when `gulp --env=production` is ran.

```js
var gulp       = require('gulp'),
    gulpif     = require('gulp-if'),
    less       = require('gulp-less'),
    concat     = require('gulp-concat'),
    minifyCSS  = require('gulp-minify-css'),
    uglify     = require('gulp-uglify'),
    watch      = require('gulp-watch'),
    rename     = require('gulp-rename'),
    argv       = require('yargs').argv,
    prefix     = require('gulp-autoprefixer'),
    rev        = require('gulp-rev');

// Options
var options = {
    target: argv.target || 'public/assets',
    env: argv.env || 'local'
};

// Is a production build
var IS_PROD_BUILD = (options.env === 'production');

// Error catcher
function swallowError (err) {
    console.error(err);
    throw err;
}

// Compile Less and save to stylesheets directory
gulp.task('less', function () {

    var destDir = options.target + '/css/',
        destFile = 'app.css';

    return gulp.src('resources/assets/less/app.less')
        .pipe(less())
        .on('error', swallowError)
        .pipe(prefix('last 2 versions', '> 1%', 'Explorer 7', 'Android 2'))
        .pipe(gulpif(IS_PROD_BUILD, minifyCSS()))
        .pipe(rename(destFile))
        .pipe(gulp.dest(destDir));
});

// Publish JavaScript
gulp.task('scripts', function () {

    var destDir = options.target + '/js/',
        destFile = 'app.js';

    return gulp.src([
            'resources/assets/js/main.js'
        ])
        .on('error', swallowError)
        .pipe(concat(destFile))
        .pipe(gulpif(IS_PROD_BUILD, uglify()))
        .pipe(gulp.dest(destDir));
});

// What tasks does running gulp trigger?
gulp.task('default', ['build']);

gulp.task('watch', ['build'], function() {
    gulp.watch('resources/assets/less/**/*.less', ['less']);
    gulp.watch('resources/assets/js/**/*.js', ['scripts']);
});

gulp.task('build', ['less', 'scripts'], function () {
    // Create manifest of assets
    if (IS_PROD_BUILD) {
        return gulp.src(options.target + '/**/*.{css,js}')
            .pipe(gulp.dest(options.target))
            .pipe(rev())
            .pipe(gulp.dest(options.target))
            .pipe(rev.manifest())
            .pipe(gulp.dest('./'));
    }
});
```

## Usage

Inside a template you can simple use `{{ asset('css/app.css') }}` to get the asset from the manifest file.

```
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
```