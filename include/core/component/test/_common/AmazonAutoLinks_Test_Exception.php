<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * A custom exception class for tests.
 *
 */
class AmazonAutoLinks_Test_Exception extends Exception {

    /**
     * @var array
     */
    public $aSkip = array(
        'frame'     => 0,        // (integer) how many trace frames to skip
        'class'     => '',       // (string) a class name to skip
        'classes'   => array(),  // (array) class names to skip
        'function'  => '',       // (string) a function name to skip
        'functions' => array(),  // (array) function names to skip
        'file'      => '',       // (string) a file name to skip
        'files'     => array(),  // (array) absolute file paths to skip
    );

    /**
     * @var array
     */
    public $aMessages = array();

    /**
     * Stores arbitrary data.
     * @var array
     */
    public $aData = array();

    /**
     * Sets up properties.
     * @param string $sMessage
     * @param integer $iCode
     * @param array|integer  $aiSkip   If an integer is given, it represents how many trace frames to skip. If an array is given, it specifies the condition to skip.
     * @param throwable $oPrevious
     */
    public function __construct( $sMessage='', $iCode=0, $aiSkip=1, $oPrevious=null ) {
        
        $this->aSkip = $this->___getSkipFormatted( $aiSkip, $this->aSkip );
        parent::__construct( $sMessage, $iCode, $oPrevious );

    }
        /**
         * @param $aiSkip
         * @param $aStructure
         * @return array
         */
        private function ___getSkipFormatted( $aiSkip, $aStructure ) {
            $_aSkip                  = is_array( $aiSkip )
                ? $aiSkip + $aStructure
                : array( 'frame' => $aiSkip ) + $aStructure;
            $_aSkip[ 'classes' ][]   = $_aSkip[ 'class' ];
            $_aSkip[ 'functions' ][] = $_aSkip[ 'function' ];
            $_aSkip[ 'files' ][]     = $_aSkip[ 'file' ];
            $_aSkip[ 'classes' ]     = array_unique( array_filter( ( array ) $_aSkip[ 'classes' ] ) );
            $_aSkip[ 'functions' ]   = array_unique( array_filter( ( array ) $_aSkip[ 'functions' ] ) );
            $_aSkip[ 'files' ]       = array_unique( array_filter( ( array ) $_aSkip[ 'files' ] ) );
            return $_aSkip;
        }

    /**
     * @param $sKey
     * @param $mData
     */
    public function setData( $sKey, $mData ) {
        $this->aData[ $sKey ] = $mData;
    }

    /**
     * @param $sKey
     * @return mixed|null
     */
    public function getData( $sKey ) {
        return isset( $this->aData[ $sKey ] ) ? $this->aData[ $sKey ] : null;
    }

    /**
     * @param string $sMessage
     */
    public function addMessage( $sMessage ) {
        $this->aMessages[] = $sMessage;
    }

    /**
     * @param string|null $sGlue
     * @return string|array If `$sGlue` is set, string; otherwise an array.
     */
    public function getMessages( $sGlue=null ) {
        $_aMessages   = $this->aMessages;
        $_aMessages[] = $this->getMessage();
        return isset( $sGlue )
            ? implode( $sGlue, $_aMessages )
            : $_aMessages;
    }

    /**
     * @param  $sWhat
     * @return mixed
     */
    public function get( $sWhat ) {
        switch ( strtolower( $sWhat ) ) {
            case 'message':
                return $this->getMessages( PHP_EOL );
            case 'file':
                return $this->___getFile();
            case 'line':
                return $this->___getLine();
            case 'trace':
                return $this->___getTrace();
        }
        return null;
    }

        /**
         * @return mixed|null   The file path of the file that caused a throw. null when not found.
         */
        private function ___getFile() {
            $_aFrames = $this->___getTrace();
            $_aFrame  = reset( $_aFrames ) + array( 'file' => null );
            return $_aFrame[ 'file' ];
        }

        /**
         * @return integer|null     null When not found.
         */
        private function ___getLine() {
            $_aFrames = $this->___getTrace();
            $_aFrame  = reset( $_aFrames ) + array( 'line' => null );
            return $_aFrame[ 'line' ];
        }

        /**
         * @return array
         */
        private function ___getTrace() {
            $_aFrames = array_slice( $this->getTrace(), $this->aSkip[ 'frame' ] );
            if ( ! count( array_merge( $this->aSkip[ 'classes' ], $this->aSkip[ 'functions' ], $this->aSkip[ 'files' ] ) ) ) {
                return $_aFrames;
            }
            foreach( $_aFrames as $_iIndex => $_aFrame ) {
                $_aFrame = $_aFrame + array( 'function' => null, 'class' => null, );
                if ( in_array( $_aFrame[ 'file' ], $this->aSkip[ 'files' ], true ) ) {
                    unset( $_aFrames[ $_iIndex ] );
                    continue;
                }
                if ( in_array( $_aFrame[ 'function' ], $this->aSkip[ 'functions' ], true ) ) {
                    unset( $_aFrames[ $_iIndex ] );
                    continue;
                }
                if ( in_array( $_aFrame[ 'class' ], $this->aSkip[ 'classes' ], true ) ) {
                    unset( $_aFrames[ $_iIndex ] );
                }
                break;
            }
            return $_aFrames;

        }

}