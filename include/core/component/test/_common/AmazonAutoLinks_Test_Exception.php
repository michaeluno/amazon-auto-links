<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * A custom exception class for tests.
 *
 */
class AmazonAutoLinks_Test_Exception extends Exception {

    /**
     * How many traces to skip.
     * @var int
     */
    public $iSkip;

    /**
     * @var array
     */
    public $aMessages = array();


    /**
     * AmazonAutoLinks_Test_Exception constructor.
     * @param string $sMessage
     * @param int $iCode
     * @param int $iSkip
     * @param throwable $oPrevious
     */
    public function __construct( $sMessage='', $iCode=0, $iSkip=1, $oPrevious=null) {
        $this->iSkip = $iSkip;
        parent::__construct( $sMessage, $iCode, $oPrevious );
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
            return array_slice( $this->getTrace(), $this->iSkip );
        }


}