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
 * Creates plugin specific task tables.
 * 
 * @since       4.3.0
 */
class AmazonAutoLinks_DatabaseTable_aal_tasks extends AmazonAutoLinks_DatabaseTable_Utility {

    /**
     * Returns the table arguments.
     * @return      array
     * @since       4.3.0
     */
    protected function _getArguments() {
        return AmazonAutoLinks_Registry::$aDatabaseTables[ 'aal_tasks' ];
    }

    /**
     * 
     * @return      string
     * @since       4.3.0
     */
    public function getCreationQuery() {
        // request_id bigint(20) unsigned UNIQUE NOT NULL,
        return "CREATE TABLE " . $this->aArguments[ 'table_name' ] . " (
            name varchar(255) UNIQUE,
            context varchar(32),    
            arguments text,
            creation_time datetime NOT NULL default '0000-00-00 00:00:00',
            next_run_time datetime NOT NULL default '0000-00-00 00:00:00',
            PRIMARY KEY  (name)
        ) " . $this->_getCharactersetCollation() . ";";    
    }

    /**
     * Deletes rows by given name(s).
     */
    public function deleteRows( $asNames='' ) {
                
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
