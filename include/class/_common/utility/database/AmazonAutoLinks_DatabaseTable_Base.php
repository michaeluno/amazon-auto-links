<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Provides shared methods for the plugin database table classes.
 * 
 * @since       3
 */
abstract class AmazonAutoLinks_DatabaseTable_Base {
    
    /**
     * Stores the subject table name.
     * 
     * @access      public      Let it access from outside.
     */
    public $sTableName = '';
    
    /**
     * Stores the table suffix. 
     * This also serves as the option key name.
     */
    public $sTableSuffix = '';
    
    /**
     * Stores the table version.
     */
    public $sVersion = '';
    
    /**
     * Sets up properties.
     */
    public function __construct( $sTableNameSuffix, $sVersion='', $bAcrossNetwork=true ) {
        $this->sTableNameSuffix = $sTableNameSuffix;
        $this->sTableName       = $bAcrossNetwork
            ? $GLOBALS[ 'wpdb' ]->base_prefix . $sTableNameSuffix
            : $GLOBALS[ 'wpdb' ]->prefix . $sTableNameSuffix;
        $this->sVersion   = $sVersion;
    }
    
    /**
     * Installs a table. 
     * 
     * @todo        Add a force option to overide an existing table.
     */
    public function install() {
        
        // If already exists, return.
        if ( $GLOBALS[ 'wpdb' ]->get_var( "SHOW TABLES LIKE '" .  $this->sTableName . "'" ) == $this->sTableName ) { 
            return;
        }
        
        // The method should be overridden in the extended clasz.
        $_sSQLQuery = $this->getCreationQuery();
        if ( ! $_sSQLQuery ) {
            return;
        }
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $_sSQLQuery );
        
        if ( $this->sVersion ) {
            update_option(
                $this->sTableNameSuffix  . '_version',  // key 
                $this->sVersion     // data
            );
        }
        
    }
    
    /**
     * Override this method in the extended class.
     * 
     * @return      string
     * @since       3
     */
    public function getCreationQuery() {
        return "";
    }
    
    /**
     * @return      string
     * @since       3.4.0
     */
    protected function _getCharactersetCollation() {
                
        $_sDefaultCharset = $GLOBALS[ 'wpdb' ]->charset
            ? 'DEFAULT CHARACTER SET ' . $GLOBALS[ 'wpdb' ]->charset . ' '
            : '';
        $_sCollation      = $GLOBALS[ 'wpdb' ]->collate
            ? 'COLLATE ' . $GLOBALS[ 'wpdb' ]->collate
            : '';
        return trim( $_sDefaultCharset . $_sCollation );            
        
    }    
    
    /**
     * Inserts a row.
     * 
     * <h3>Example</h3>
     * `
     *  $_oTable->insert(
     *      array( 
     *          'column1' => 'value1', 
     *          'column2' => 123 
     *      ), 
     *      array( 
     *          '%s', 
     *          '%d' 
     *      ) 
     *  );
     * `
     * @see     https://codex.wordpress.org/Class_Reference/wpdb#INSERT_rows
     */
    public function insert( $aRow, $asFormat=null ) {        
        return $GLOBALS[ 'wpdb' ]->insert( 
            $this->sTableName, 
            $aRow,
            null === $asFormat  // row format
                ? $this->_getPlaceHolders( $aRow )
                : $asFormat
        );    
    }
    
    /**
     * $wpdb->delete( 
     *  'table', 
     *   array( 'ID' => 1 ), array( '%d' ) );
     * 
     * @param       array           $aWhere     An array which defines the where SQL query part. 
     * If empty, it attempts to delete all rows.
     * @param       array|string    $asFormat
     * @return      void
     */
    public function delete( $aWhere=array(), $asFormat=null ) {
        
        if ( empty( $aWhere ) ) {
            $GLOBALS[ 'wpdb' ]->query( 
                "TRUNCATE TABLE `{$this->sTableName}`" 
            );            
            return;
        }
        
        $GLOBALS[ 'wpdb' ]->delete( 
            $this->sTableName, 
            $aWhere,
            null === $asFormat  // row format
                ? $this->_getPlaceHolders( $aWhere )
                : $asFormat
        );
    }
    
    /**
     * 
     * @see     https://codex.wordpress.org/Class_Reference/wpdb#REPLACE_row
     */
    public function replace( $aRow, $asFormat=null ) {        
        $aRow = $this->_getSanitizedRow( $aRow );
        return $GLOBALS[ 'wpdb' ]->replace( 
            $this->sTableName, 
            $aRow,
            null === $asFormat  // row format
                ? $this->_getPlaceHolders( $aRow )
                : $asFormat
        );    
    }    
       
    /**
     * 
     * @see     https://codex.wordpress.org/Class_Reference/wpdb#REPLACE_row
     */
    public function update( $aRow, $aWhere=array(), $asFormat=null, $asWhereFormat=null ) {
        $aRow = $this->_getSanitizedRow( $aRow );
        return $GLOBALS[ 'wpdb' ]->update( 
            $this->sTableName, 
            $aRow, // the new data to update
            $aWhere, // e.g. array( 'id' => ... ) which updates the row whose id is ...
            null === $asFormat  // row format
                ? $this->_getPlaceHolders( $aRow )
                : $asFormat,
            null === $asWhereFormat
                ? $this->_getPlaceHolders( $aWhere )
                : $asWhereFormat
        );    
    }           
        /**
         * @return      array
         */
        private function _getSanitizedRow( array $aRow ) {
            foreach( $aRow as $_sColumnName => $_mValue ) {
                $aRow[ $_sColumnName ] = maybe_serialize( $_mValue );
            }
            return $aRow;
        }
        
        /**
         * @return      array       The placefolders such as %s, %d, %f for the format parameter of the above methods.
         * @see         https://codex.wordpress.org/Class_Reference/wpdb#Placeholders
         */
        private function _getPlaceHolders( $aRow ) {
            $_aPlaceHolders = array();
            foreach ( $aRow as $_sColumn => $_mData ) {
                $_aPlaceHolders[] = $this->_getPlaceHolder( $_mData );
            }
            return $_aPlaceHolders;
        }
            private function _getPlaceHolder( $mData ) {                
                switch ( gettype( $mData ) ) {
                    case 'integer':
                        return '%d';
                    case 'float':
                    case 'double':
                        return '%f';
                    case 'boolean':
                    case 'string':
                    default: 
                        return '%s';
                }
            }
    
    /**
     * Retrieves a value.
     * @remark      Returns `null` if no item is found.
     */
    public function getVariable( $sSQLQuery, $iColumnOffset=0, $iRowOffset=0 ) {
        $_mResult = $GLOBALS[ 'wpdb' ]->get_var( 
            $sSQLQuery, 
            $iColumnOffset, 
            $iRowOffset
        );
        return maybe_unserialize( $_mResult );
    }
    /**
     * Selects a single row.
     */
    public function getRow( $sSQLQuery, $sFormat='ARRAY_A', $iRowOffset=0 ) {
        $_aRow = $GLOBALS[ 'wpdb' ]->get_row(
            $sSQLQuery, 
            $sFormat, 
            $iRowOffset
        );
        // A user can set own format with $sFormat so if it's not array, return as it is.
        if ( ! is_array( $_aRow ) ) {
            return $_aRow;
        }
        // When an array is returned, unserialize serialized arrays.
        foreach( $_aRow as $_sColumnName => $_mValue ) {
            $_aRow[ $_sColumnName ] = maybe_unserialize( $_mValue );
        }
        return $_aRow;
    }
    /**
     * Selects multiple rows.
     */
    public function getRows( $sSQLQuery, $sFormat='ARRAY_A' ) {
        $_aRows = $GLOBALS[ 'wpdb' ]->get_results(
            $sSQLQuery,
            $sFormat
        );
        foreach( $_aRows as &$_aRow ) {
            foreach( $_aRow as $_sColumnName => $_mValue ) {
                $_aRow[ $_sColumnName ] = maybe_unserialize( $_mValue );
            }            
        }
        return $_aRows;
    }
    
    /**
     * Drops a table.
     */
    public function uninstall() {
        $GLOBALS[ 'wpdb' ]->query( 
            "DROP  TABLE IF EXISTS " . $this->sTableName
        );
    }
    
}