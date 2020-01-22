<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */


// If the WordPress version is below 3.5, which uses SimplePie below 1.3,
if ( version_compare( get_bloginfo( 'version' ) , '3.5', "<" ) ) {    

    abstract class AmazonAutoLinks_SimplePie_Base extends AmazonAutoLinks_SimplePie_Base_Base {
        
        public function sort_items( $a, $b ) {

            // Sort 
            // by date
            if ( 'date' === self::$sortorder  ) {
                return $a->get_date( 'U' ) <= $b->get_date( 'U' );
            }
            // by title ascending
            if ( 'title' === self::$sortorder ) {
                return self::sort_items_by_title( $a, $b );
            }
            // by title decending
            if ( 'title_descending' === self::$sortorder ) {
                return self::sort_items_by_title_descending( $a, $b );
            }
            // by random 
            return rand( -1, 1 );    
            
        }        
    }
    
} else {
    
    abstract class AmazonAutoLinks_SimplePie_Base extends AmazonAutoLinks_SimplePie_Base_Base {
        
        public static function sort_items( $a, $b ) {
            
            // Sort 
            // by date
            if ( self::$sortorder == 'date' ) 
                return $a->get_date( 'U' ) <= $b->get_date( 'U' );
            // by title ascending
            if ( self::$sortorder == 'title' ) 
                return self::sort_items_by_title( $a, $b );
            // by title decending
            if ( self::$sortorder == 'title_descending' ) 
                return self::sort_items_by_title_descending( $a, $b );
            // by random 
            return rand( -1, 1 );    
            
        }        
    }    

}