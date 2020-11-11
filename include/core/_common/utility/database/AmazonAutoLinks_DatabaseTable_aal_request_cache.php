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
 * Creates plugin specific database tables.
 * 
 * @since       3
 */
class AmazonAutoLinks_DatabaseTable_aal_request_cache extends AmazonAutoLinks_DatabaseTable_Utility {

    /**
     * Returns the table arguments.
     * @return      array
     * @since       3.5.0
     */
    protected function _getArguments() {
        return AmazonAutoLinks_Registry::$aDatabaseTables[ 'aal_request_cache' ];
    }

    /**
     * 
     * @return      string
     * @since       3
     */
    public function getCreationQuery() {
        // request_id bigint(20) unsigned UNIQUE NOT NULL,
        return "CREATE TABLE " . $this->aArguments[ 'table_name' ] . " (
            name varchar(191) UNIQUE,    
            request_uri text,   
            type varchar(20),
            charset varchar(20),
            cache mediumblob,
            modified_time datetime NOT NULL default '0000-00-00 00:00:00',
            expiration_time datetime NOT NULL default '0000-00-00 00:00:00',
            PRIMARY KEY  (name)
        ) " . $this->_getCharactersetCollation() . ";";    
    }

    /**
     *
     * @param string $sName
     * @param mixed $mData
     * @param int $iDuration
     * @param array $aExtra
     * @return  boolean     Whether it is set or not.
     */
    public function setCache( $sName, $mData, $iDuration=0, array $aExtra=array() ) {
        
        $_aColumns = array(
            'name'              => '',
            'request_uri'       => '',
            'type'              => '',
            'charset'           => '',
            'cache'             => '',
            'modified_time'     => '0000-00-00 00:00:00',
            'expiration_time'   => '0000-00-00 00:00:00',
        );

        $_aRow = array(
                'name'  => $sName,
                'cache' => maybe_serialize( $mData ),
            )
            + array_intersect_key( $aExtra, $_aColumns ) // removes unsupported items
            + array(
                'modified_time'   => date( 'Y-m-d H:i:s' ),
                'expiration_time' => date( 'Y-m-d H:i:s', time() + $iDuration ),
            );

        if ( -1 === $iDuration ) {
            unset( $_aRow[ 'expiration_time' ] );
        }

        return $this->setRow( $_aRow );
        
    }
        /**
         * Sets a row.
         * @param       array $aRow
         * @return      boolean     Whether it is set or not.
         */
        public function setRow( array $aRow ) {

            if ( ! isset( $aRow[ 'name' ] ) ) {
                return false;
            }
            $_iCountSetRows = parent::setRow( $aRow );

            // @deprecated 4.3.0
//            if ( $this->doesRowExist( $aRow[ 'name' ] ) ) {
//                $_iCountSetRows = $this->update(
//                    $aRow, // data
//                    array( // where
//                        'name' => $aRow[ 'name' ],
//                    )
//                );
//            } else {
//                $_iCountSetRows = $this->replace( $aRow );
//            }
            return $_iCountSetRows
                ? true
                : false;
                
        }    
            /**
             * Checks whether the given cache exists or not by the given name.
             * @param  string $sName
             * @return boolean
             * @deprecated 4.3.4 unused
             */
            /*public function doesRowExist( $sName ) {
                return ( boolean ) $this->getVariable(
                    "SELECT name
                    FROM {$this->aArguments[ 'table_name' ]}
                    WHERE name = '{$sName}'"
                );
            }*/

    /**
     *
     * @param  array|string $asNames
     * @param  integer|null $iCacheDuration
     * @return array
     * The structure
     * array(
     *  'remained_time' => (integer)
     *  'data'          => (mixed)
     * )
     */
    public function getCache( $asNames, $iCacheDuration=null ) {
        return is_array( $asNames )
            ? $this->___getMultipleRows( $asNames, $iCacheDuration )
            : $this->___getCacheEach( $asNames, $iCacheDuration );
    }
        /**
         *
         * @remark Saves the number of MySQL queries by passing multiple items at once.
         * @param  array $aNames
         * @param  integer|null $iCacheDuration
         * @return array
         * Structure:
         *  array(
         *      "{cache name}" => array(
         *           'remained_time'          => 0,
         *           'charset'                => '',
         *           'data'                   => null,
         *           'name'                   => '',
         *           '_cache_duration'        => 0,
         *           '_now'                   => time(),
         *           '_modified_timestamp'    => strtotime( $aRow[ 'modified_time' ] ),
         *           '_expiration_timestamp'  => strtotime( $aRow[ 'expiration_time' ] ),
         *      ),
         *      "{cache name}" => array(
         *          ...
         *       ),
         * )
         */
        private function ___getMultipleRows( $aNames, $iCacheDuration=null ) {

            $_sNames   = "('" . implode( "','", $aNames ) . "')";
            $_aResults =  $this->getRows(
                "SELECT cache,modified_time,expiration_time,charset,request_uri,name"
                . " FROM `{$this->aArguments[ 'table_name' ]}`"
                . " WHERE name in {$_sNames};"
            );       

            $_aRows = array();
            foreach( $_aResults as $_aResult ) {
                if ( ! is_array( $_aResult ) ) {
                    continue;
                }
                $_aRows[ $_aResult[ 'name' ] ] = $this->___getRowFormatted( $_aResult, $iCacheDuration );
            }
            return $_aRows;
            
        }

        /**
         *
         * @param string $sName
         * @param integer|null $iCacheDuration
         * @return array
         */
        private function ___getCacheEach( $sName, $iCacheDuration=null ) {
            $_aRow = $this->getRow(
                "SELECT cache,modified_time,expiration_time,charset,request_uri,name"
                    . " FROM `{$this->aArguments[ 'table_name' ]}`"
                    . " WHERE name = '{$sName}';"
            );
            return $this->___getRowFormatted( $_aRow, $iCacheDuration );
        }
            /**
             * @param  array        $aRow               The row array returned from the database.
             * @param  integer|null $iCacheDuration     The cache duration in seconds. If not set, the stored cache duration will be used.
             * @return array
             */
            private function ___getRowFormatted( array $aRow, $iCacheDuration=null ) {
                if ( empty( $aRow ) ) {
                    return array();
                }
                $aRow  = $aRow + array(
                    'modified_time' => null,
                    'expiration_time' => null,
                    'charset' => null,
                    'cache' => null,
                );
                $_aRow = array(
                        'remained_time' => null !== $iCacheDuration
                            ? strtotime( $aRow[ 'modified_time' ] ) + $iCacheDuration - time()
                            : strtotime( $aRow[ 'expiration_time' ] ) - time(),                        
                        'charset'       => $aRow[ 'charset' ],
                        'data'          => maybe_unserialize( $aRow[ 'cache' ] ), 
                    ) + $aRow;
                unset( $_aRow[ 'cache' ] );
                return $_aRow + array(
                    'remained_time' => 0,
                    'charset'       => '',
                    'data'          => null,
                    'name'          => '',
                    
                    // These are for debugging
                    '_cache_duration'        => $iCacheDuration,
                    '_now'                   => time(),
                    '_modified_timestamp'    => strtotime( $aRow[ 'modified_time' ] ),
                    '_expiration_timestamp'  => strtotime( $aRow[ 'expiration_time' ] ),
                    
                );
            }


    /**
     * @remark Currently only used by scratches.
     * @param  string  $sSQLQuery
     * @param  boolean $bFormat
     * @return array
     * @since  4.4.0
     */
    public function getCachesByQuery( $sSQLQuery, $bFormat=false ) {
        $_aResults =  $this->getRows( $sSQLQuery );
        $_aRows = array();
        foreach( $_aResults as $_aResult ) {
            if ( ! is_array( $_aResult ) ) {
                continue;
            }
            $_aRows[ $_aResult[ 'name' ] ] = $bFormat
                ? $this->___getRowFormatted( $_aResult, null )
                : $_aResult;
        }
        return $_aRows;
    }

    /**
     * Deletes the cache(s) by given cache name(s).
     * @param array|string $asNames
     */
    public function deleteCache( $asNames='' ) {
                
        $_aNames = is_array( $asNames )
            ? $asNames
            : array( 0 => $asNames );

        // 3.7.5+ To support multiple cache items to be processed at once,
        $_sCacheNames = "'" . implode( "','", $_aNames ) . "'";
        $GLOBALS[ 'wpdb' ]->query(
            "DELETE FROM {$this->aArguments[ 'table_name' ]} "
            . "WHERE `name` IN( {$_sCacheNames} )"
        );
        
    }

}
