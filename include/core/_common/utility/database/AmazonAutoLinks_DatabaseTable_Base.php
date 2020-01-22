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
 * Provides shared methods for the plugin database table classes.
 * 
 * @since       3
 * @version     1.1.0
 */
abstract class AmazonAutoLinks_DatabaseTable_Base {
    
    public $aArguments = array(
        'name'              => '',      // the table name suffix
        'version'           => '1.0.0',
        'across_network'    => true,    // whether to share the table in across the multi-site network.

        // Arguments automatically set
        'table_name'        => '',      // determined in the formatting method
    );

    /**
     * Sets up properties.
     * @since   1.1.0
     */
    public function __construct() {
        $this->aArguments       = $this->_getArgumentsFormatted( $this->_getArguments() );
    }
        /**
         * Returns the class arguments.
         * @remark      This should be overridden in the extended class.
         * @since       1.1.0
         * @return      array
         */
        protected function _getArguments() {
            return $this->aArguments;
        }
        /**
         * Formats the arguments.
         * @since       1.1.0
         * @return      array
         */
        protected function _getArgumentsFormatted( array $aArguments ) {

            $aArguments[ 'table_name' ] = $aArguments[ 'across_network' ]
                ? $GLOBALS[ 'wpdb' ]->base_prefix . $aArguments[ 'name' ]
                : $GLOBALS[ 'wpdb' ]->prefix . $aArguments[ 'name' ];
            return $aArguments + array(
                'name' => __CLASS__,
            );

        }

    /**
     * @return string
     * @since  3.10.0
     */
    public function getVersion() {
        return get_option(
            $this->aArguments[ 'name' ]  . '_version',
            '0'
        );
    }

    /**
     * Upgrade the table.
     * @return      array       Strings containing the results of the various update queries.
     * @since       1.1.0
     */
    public function upgrade() {

        $_sExistingVersion = $this->getVersion();

        // If the existent version is above or equal to the set version in the argument, do not upgrade.
        if ( version_compare( $_sExistingVersion, $this->aArguments[ 'version' ], '>=' ) ) {
            return array();
        }

        return $this->install( true );

    }

    /**
     * Installs a table.
     *
     * @since       1.1.0
     * @return      array       Strings containing the results of the various update queries.
     */
    public function install( $bForce=false ) {

        // If already exists, return.
        if ( ! $bForce && $this->___hasTable() ) {
            return array();
        }
        return $this->___install();

    }
        /**
         * Checks whether the table exists or not.
         * @return      boolean
         * @since       1.1.0
         */
        private function ___hasTable() {
            $_sExistingTableName = $GLOBALS[ 'wpdb' ]->get_var(
                "SHOW TABLES LIKE '"
                .  $this->aArguments[ 'table_name' ]
                . "'"
            );
            return $_sExistingTableName === $this->aArguments[ 'table_name' ];
        }
        /**
         * Installs the database table.
         * @return      array       Strings containing the results of the various update queries.
         * @since       1.1.0
         */
        private function ___install() {

            // The method should be overridden in the extended class.
            $_sSQLQuery = $this->getCreationQuery();
            if ( ! $_sSQLQuery ) {
                return;
            }

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $_aResult = dbDelta( $_sSQLQuery );

            if ( $this->aArguments[ 'version' ] ) {
                update_option(
                    $this->aArguments[ 'name' ]  . '_version',  // key
                    $this->aArguments[ 'version' ]     // data
                );
            }
            return $_aResult;

        }

    /**
     * Drops a table.
     * @since   1.1.0
     */
    public function uninstall() {

        if ( $this->aArguments[ 'version' ] ) {
            delete_option( $this->aArguments[ 'name' ]  . '_version' );
        }

        $GLOBALS[ 'wpdb' ]->query(
            "DROP  TABLE IF EXISTS " . $this->aArguments[ 'table_name' ]
        );
    }

    /**
     * Override this method in the extended class.
     *
     * @return      string
     * @since       1.1.0
     */
    public function getCreationQuery() {
        return '';
    }

    /**
     * @return      string
     * @since       1.1.0
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
     * @since   1.1.0
     */
    public function insert( $aRow, $asFormat=null ) {
        return $GLOBALS[ 'wpdb' ]->insert(
            $this->aArguments[ 'table_name' ],
            $aRow,
            null === $asFormat  // row format
                ? $this->_getPlaceHolders( $aRow )
                : $asFormat
        );
    }

    /**
     * ```
     * $wpdb->delete(
     *  'table',
     *   array( 'ID' => 1 ), array( '%d' )
     * );
     * ```
     *
     * @param       array           $aWhere     An array which defines the where SQL query part.
     * If empty, it attempts to delete all rows.
     * @param       array|string    $asFormat
     * @return      void
     * @since       1.1.0
     */
    public function delete( $aWhere=array(), $asFormat=null ) {

        if ( empty( $aWhere ) ) {
            $GLOBALS[ 'wpdb' ]->query(
                "TRUNCATE TABLE `{$this->aArguments[ 'table_name' ]}`"
            );
            return;
        }

        $GLOBALS[ 'wpdb' ]->delete(
            $this->aArguments[ 'table_name' ],
            $aWhere,
            null === $asFormat  // row format
                ? $this->_getPlaceHolders( $aWhere )
                : $asFormat
        );

    }

    /**
     *
     * @see     https://codex.wordpress.org/Class_Reference/wpdb#REPLACE_row
     * @since   1.1.0
     */
    public function replace( $aRow, $asFormat=null ) {
        $aRow = $this->_getSanitizedRow( $aRow );
        return $GLOBALS[ 'wpdb' ]->replace(
            $this->aArguments[ 'table_name' ],
            $aRow,
            null === $asFormat  // row format
                ? $this->_getPlaceHolders( $aRow )
                : $asFormat
        );
    }

    /**
     *
     * @see     https://codex.wordpress.org/Class_Reference/wpdb#REPLACE_row
     * @since   1.1.0
     */
    public function update( $aRow, $aWhere=array(), $asFormat=null, $asWhereFormat=null ) {
        $aRow = $this->_getSanitizedRow( $aRow );
        return $GLOBALS[ 'wpdb' ]->update(
            $this->aArguments[ 'table_name' ],
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
         * @since       1.1.0
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
         * @since       1.1.0
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
     * @since       1.1.0
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
     * @since   1.1.0
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
     * @since   1.1.0
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
     * Retrieves a total count of rows.
     * @sine        2.5.3
     * @return      integer     The total row count.
     */
    public function getTotalItemCount() {
        return ( integer ) $this->getVariable(
            "SELECT COUNT(*) FROM `{$this->aArguments[ 'table_name' ]}`"
        );
    }

    /**
     * Retrieves the table name.
     * @since       2.5.3
     * @return      string
     */
    public function getTableName() {
        return $this->aArguments[ 'table_name' ];
    }

    /**
     * @since       2.5.0
     * @since       3.7.3       Added an option to get the value as integer
     * @param       boolean $bNumeric   Whether the return value is a numeric value (integer|float) or not. The unit is megabyte.
     * @return      string|integer|float
     */
    public function getTableSize( $bNumeric=false ) {
        $_aResult = $GLOBALS[ 'wpdb' ]->get_results(
            "SELECT 
            table_name AS `Table`, 
            round(((data_length + index_length) / 1024 / 1024), 2) `Size_in_MB` 
            FROM information_schema.TABLES 
            WHERE table_schema = (SELECT DATABASE())
            AND table_name = '{$this->aArguments[ 'table_name' ]}'",
            'ARRAY_A'
        );
        if ( $bNumeric ) {
            return isset( $_aResult[ 0 ][ 'Size_in_MB' ] )
                ? $_aResult[ 0 ][ 'Size_in_MB' ]
                : 0;
        }
        return isset( $_aResult[ 0 ][ 'Size_in_MB' ] )
            ? $_aResult[ 0 ][ 'Size_in_MB' ] . ' MB'
            : 'n/a';
    }

}
