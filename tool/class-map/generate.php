<?php
/**
 * Class Map Generator Script
 * @version 1.0.0
 */
/* Configuration */
$sTargetBaseDir		= dirname( dirname( dirname( __FILE__ ) ) );
$sResultFilePath	= $sTargetBaseDir . '/include/core/class-map.php';

/* If accessed from a browser, exit. */
$bIsCLI				= php_sapi_name() == 'cli';
$sCarriageReturn	= $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) { exit; }

/* Include necessary files */
require( dirname( __DIR__ ) . '/vendor/autoload.php' );

/* Check the permission to write. */
if ( ! file_exists( $sResultFilePath ) ) {
	file_put_contents( $sResultFilePath, '', FILE_APPEND | LOCK_EX );
}
if ( 
	( file_exists( $sResultFilePath ) && ! is_writable( $sResultFilePath ) )
	|| ! is_writable( dirname( $sResultFilePath ) ) 	
) {
	exit( sprintf( 'The permission denied. Make sure if the folder, %1$s, allows to modify/create a file.', dirname( $sResultFilePath ) ) );
}

/* Create a minified version of the framework. */
echo 'Started...' . $sCarriageReturn;

$_oGenerator = new \PHPClassMapGenerator\PHPClassMapGenerator(
    $sTargetBaseDir,
    array( $sTargetBaseDir . '/include/core/' ),
    $sResultFilePath,
    array(
        'output_buffer'     => true,
        'output_var_name'	=>	'return',
        'base_dir_var'  	=>	'AmazonAutoLinks_Registry::$sDirPath',
        'header_type'		=>	'CONSTANTS',
        'header_class_name'	=>	'AmazonAutoLinks_Registry',
        'header_class_path'	=>	$sTargetBaseDir . '/amazon-auto-links.php',
        'search'            => array(
//            'allowed_extensions'     => [ 'php' ],
//            'exclude_dir_paths'      => [],
            'exclude_dir_names'      => array( '_del', '_bak', 'apf', 'library', '_notes', 'node_modules' ),
//            'exclude_file_names'     => [ '.min.', ],
//            'is_recursive'           => true,
//            'ignore_note_file_names' => [ 'ignore-class-map.txt' ],
        ),
    )
);

echo 'Done!' . $sCarriageReturn;