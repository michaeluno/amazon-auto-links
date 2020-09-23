<?php
/**
 * Minifies PHP files into a single file.
 *
 */

/* Set necessary paths */
$sTargetBaseDir		= dirname( dirname( dirname( __FILE__ ) ) );
$aTargetDirs        = array(
    $sTargetBaseDir . '/asset/js/',
    $sTargetBaseDir . '/include/core/',
    $sTargetBaseDir . '/template/',
);

/* If accessed from a browser, exit. */
$bIsCLI				= php_sapi_name() == 'cli';
$sCarriageReturn	= $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) { 
    exit( 'Please run the script with a console program.' );
}

/* Include necessary files */
require( dirname( __DIR__ ) . '/vendor/autoload.php' );
require( dirname( __FILE__ ) . '/class/vendor/autoload.php' );

/* Create a minified version of the framework. */
echo 'Started...' . $sCarriageReturn;
$_oGenerator = new \PHPClassMapGenerator\PHPClassMapGenerator(
    $sTargetBaseDir,
    $aTargetDirs,
    '',
    array(
        'do_in_constructor'  => false,
        'output_buffer'      => true,
        'structure'          => 'PATH',
        'search'            => [
            'allowed_extensions'     => [ 'css' ],
            'exclude_dir_paths'      => [],
            'exclude_dir_names'      => [ '_del', '_bak', 'apf', 'library' ],
            'exclude_file_names'     => [ '.min.', ],
            'is_recursive'           => true,
            'ignore_note_file_names' => [ 'ignore-css-min.txt' ],
        ],
    )
);


$_aFileInfoStruct = [
    'name'     => '@name',
    'version'  => '@version',
    'Name'     => 'Template Name:',
    'Version'     => 'Version:',
];
foreach( $_oGenerator->get() as $_sFilePath ) {

    $_aFileInfo      = $_oGenerator->getFileHeaderComment( $_sFilePath, $_aFileInfoStruct );
    $_sTitle         = trim( "{$_aFileInfo[ 'name' ]} {$_aFileInfo[ 'version' ]} {$_aFileInfo[ 'Name' ]} {$_aFileInfo[ 'Version' ]}" );
    $_sHeader        = $_sTitle ? "/* {$_sTitle} */" . PHP_EOL : '';
    $_oMinifier      = Asika\Minifier\MinifierFactory::create('css' );
    $_oMinifier->addFile( $_sFilePath );
    $_sContent       = $_sHeader . $_oMinifier->minify();
    $_sDirPath       = dirname( $_sFilePath );
    $_sBaseNameWOExt = pathinfo( $_sFilePath, PATHINFO_FILENAME );
    $_sMinScriptPath = $_sDirPath . '/' . $_sBaseNameWOExt . '.min.css';
    file_put_contents( $_sMinScriptPath, $_sContent );
    echo 'Writing to ' . $_sMinScriptPath . $sCarriageReturn;
}
echo 'Done!' . $sCarriageReturn;