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
 * Loads the component, AALB Support.
 *
 * Amazon Associates Link Builder (AALB) is discontinues as of Feb 11, 2020.
 * This component attempts to convert their shortcode outputs into this plugin outputs.
 * So the users of AALB can safely migrate to Amazon Auto Links without modifying the database.
 * 
 * @package      Amazon Auto Links
 * @since        3.11.1
 */
class AmazonAutoLinks_AALBSupportLoader {
        
    public function __construct() {

        if ( is_admin() ) {
            new AmazonAutoLinks_AALBSupport_Setting;
        }

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->get( array( 'aalb', 'support' ) ) ) {
            return;
        }

        new AmazonAutoLinks_AALBSupport_Shortcode_amazon_link;
        new AmazonAutoLinks_AALBSupport_Shortcode_amazon_textlink;
        new AmazonAutoLinks_AALBSupport_GutenbergBlock;

    }

}