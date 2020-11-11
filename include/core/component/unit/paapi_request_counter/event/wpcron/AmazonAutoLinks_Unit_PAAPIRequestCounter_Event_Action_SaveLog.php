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
 * Stores log data stored as files into the database options table.
 * @package      Amazon Auto Links
 * @since        4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Action_SaveLog extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName     = 'aal_action_paapi_request_counter_save_log';
    protected $_iCallbackParameters = 1;

    /**
     * @return boolean
     * @since  4.4.0
     */
    protected function _shouldProceed( /* $aArguments */ ) {
        if ( $this->hasBeenCalled( get_class( $this ) . '::' . __METHOD__ . '_' . serialize( func_get_args() ) ) ) {
            return false;
        }
        return true;
    }

    /**
     * Scan files and populate data and remove files. Then, store the data in the options table.
     */
    protected function _doAction( /* $sLocale, */ ) {

        $_aParams       = func_get_args() + array( null );
        $_sLocale       = $_aParams[ 0 ];
        $_oDatabaseLog  = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Database( $_sLocale );
        $_aDatabaseLog  = $_oDatabaseLog->get( 0, PHP_INT_MAX );
        $_oFileLog      = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_File( $_sLocale );
        $_aFileLog      = $_oFileLog->get( 0, strtotime('yesterday midnight' ), $_aFilePaths );
        $_aNewLog       = $this->uniteArrays( $_aDatabaseLog, $_aFileLog );
        $_oDatabaseLog->set( $_aNewLog );
        
        $_oOption        = AmazonAutoLinks_Option::getInstance();
        $_iRetentionSpan = ( ( integer ) $_oOption->get( 'paapi_request_counts', 'retention_period', 'size' ) )
            * ( ( integer ) $_oOption->get( 'paapi_request_counts', 'retention_period', 'unit' ) );

        $_iNow           = time();
        $_oDatabaseLog->truncate( $_iNow - $_iRetentionSpan, $_iNow );
        $_oDatabaseLog->save();
        $this->___cleanFileLog( $_aFilePaths );

    }
        private function ___cleanFileLog( array $aFilePaths ) {

            // Handle files.
            $_aDeleted      = array();
            $_aUndeleted    = array();
            $_aDateDirPaths = array();
            foreach( $aFilePaths as $_sFilePath ) {
                $_sDirPath    = dirname( $_sFilePath );
                $_aDateDirPaths[ $_sDirPath ] = $_sDirPath; // to make it unique, set the path as the key
                $_bDeleted    = unlink( $_sFilePath );
                $_bDeleted ? array_push( $_aDeleted, $_sFilePath ) : array_push( $_aUndeleted, $_sFilePath );
            }

            // Handle date directories.
            $_aMonthDirPaths = array();
            foreach( $_aDateDirPaths as $_sThisDirPath ) {
                $_sMonthDirPath = dirname( $_sThisDirPath );
                $_aMonthDirPaths[ $_sMonthDirPath ] = $_sMonthDirPath;
                if ( $this->isDirectoryEmpty( $_sThisDirPath ) ) {
                    $_bDeleted = rmdir( $_sThisDirPath );
                    $_bDeleted ? array_push( $_aDeleted, $_sThisDirPath ) : array_push( $_aUndeleted, $_sThisDirPath );
                }
            }

            // Handle month directories.
            $_aYearDirPaths = array();
            foreach( $_aMonthDirPaths as $_sThisDirPath ) {
                $_sYearDirPath = dirname( $_sThisDirPath );
                $_aYearDirPaths[ $_sYearDirPath ] = $_sYearDirPath;
                if ( $this->isDirectoryEmpty( $_sThisDirPath ) ) {
                    $_bDeleted = rmdir( $_sThisDirPath );
                    $_bDeleted ? array_push( $_aDeleted, $_sThisDirPath ) : array_push( $_aUndeleted, $_sThisDirPath );
                }
            }

            // Handle year directories.
            foreach( $_aYearDirPaths as $_sThisYearPath ) {
                if ( $this->isDirectoryEmpty( $_sThisYearPath ) ) {
                    $_bDeleted = rmdir( $_sThisYearPath );
                    $_bDeleted ? array_push( $_aDeleted, $_sThisYearPath ) : array_push( $_aUndeleted, $_sThisYearPath );
                }
            }

        }

}