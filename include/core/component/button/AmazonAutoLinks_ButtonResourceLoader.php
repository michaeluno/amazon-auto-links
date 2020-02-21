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
 *  Loads CSS files (style.css) of active buttons.
 *  
 *  @remark     Currently only handles stylesheets. 
 * 
 *  @package    Amazon Auto Links
 *  @since      3
 */
class AmazonAutoLinks_ButtonResourceLoader extends AmazonAutoLinks_ButtonUtility {
  
    /**
     * Sets up hooks and properties.
     */
    public function __construct() {
        
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return;
        }

        // If not front-end
        if ( ! is_admin() ) {
            add_action( 
                'wp_head', 
                array( $this, 'replyToPrintStyleTag' ) 
            );
            add_action(
                'embed_head',
                array( $this, 'replyToPrintStyleTag' )
            ); // 4.0.0
            return;
        } 
        
        add_action(
            'current_screen',
            array( $this, 'replyToLoadStyleTag' )
        );
            
    }
    /**
     * @callback        action      current_screen
     */
    public function replyToLoadStyleTag( $oScreen ) {

        // Add style tags only in the button listing page for preview.
        if ( 
            isset( $_GET[ 'post_type' ] )
            && in_array( 
                $_GET[ 'post_type' ],
                array(
                    AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                )
            )
        ) {                    
            add_action(
                'admin_head' . '-edit.php',
                array( $this, 'replyToPrintStyleTag' )
            );
            return;
        }
        
        
        // If it is not in a post definition page, return.
        if ( ! in_array( $GLOBALS[ 'pagenow' ], array( 'post.php', 'post-new.php' ) ) ) {
            return;
        }

        if ( 
            in_array( 
                $oScreen->post_type,
                array(
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                )
            )
        ) {        
            // For unit definition page
            add_action(
                'admin_head' . '-post.php',
                array( $this, 'replyToPrintStyleTag' )
            );
            add_action(
                'admin_head' . '-post-new.php',
                array( $this, 'replyToPrintStyleTag' )
            );            
        }             
        
    }
    
    /**
     * Enqueues activated templates' CSS file.
     * 
     * @callback    action      admin_head, wp_head
     * @since       3
     */
    public function replyToPrintStyleTag() {
        
        $_sCSSRules = $this-> getButtonsCSS();
        if ( ! $_sCSSRules ) {
            return;
        }     

        $_sCSSRules = defined( 'WP_DEBUG' ) && WP_DEBUG
            ? $_sCSSRules
            : $this->getCSSMinified( $_sCSSRules );
        echo "<style type='text/css' id='amazon-auto-links-button-css'>"
                . $_sCSSRules
            . "</style>";
        
    }      
    
    /**
     * Stores active button CSS rules.
     */
    static public $sButtonsCSS;
    /**
     * 
     * @scope       public      It is static public as unit class needs to retrieve the CSS rules to check the parsing button exists.
     * @return      string
     */
    static public function getButtonsCSS() {
        
        // Use cache
        if ( isset( self::$sButtonsCSS ) ) {
            return self::$sButtonsCSS;
        }
        $_sCSSStored = get_option(
            AmazonAutoLinks_Registry::$aOptionKeys[ 'button_css' ],
            ''  // default
        );
        if ( ! $_sCSSStored ) {
            // Update the button CSS option.
            $_sCSSStored = self::getCSSRulesOfActiveButtons();
            if ( $_sCSSStored ) {                
                update_option(
                    AmazonAutoLinks_Registry::$aOptionKeys[ 'button_css' ],
                    $_sCSSStored // data
                );
            }
        }        
                
        $_sCSSRules = apply_filters(
            'aal_filter_button_css',
            self::getDefaultButtonCSS() . PHP_EOL
                . $_sCSSStored
        );     
        
        // Cache for later use.
        self::$sButtonsCSS = $_sCSSRules;
        return self::$sButtonsCSS;
        
    }
    /**
     * 
     * @return      string
     * @scope       public      This is static public as it is accessed from a class that creates a default button.
     */
    static public function getDefaultButtonCSS( $isButtonID='default' ) {
                                        
        return ".amazon-auto-links-button.amazon-auto-links-button-{$isButtonID} {
            background: #4997e5;
            background-image: -webkit-linear-gradient(top, #4997e5, #3f89ba);
            background-image: -moz-linear-gradient(top, #4997e5, #3f89ba);
            background-image: -ms-linear-gradient(top, #4997e5, #3f89ba);
            background-image: -o-linear-gradient(top, #4997e5, #3f89ba);
            background-image: linear-gradient(to bottom, #4997e5, #3f89ba);
            -webkit-border-radius: 4;
            -moz-border-radius: 4;
            border-radius: 4px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            white-space: nowrap;
            color: #ffffff;
            font-size: 13px;
            text-shadow-color: transparent;
            width: 100px;
            box-shadow-color: #666666;
            padding: 7px 8px 8px 8px;
            background: #3498db;
            border: solid #6891a5 1px;
            text-decoration: none;
        }
        .amazon-auto-links-button.amazon-auto-links-button-{$isButtonID}:hover {
            background: #3cb0fd;
            background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
            background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
            background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
            background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
            background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
            text-decoration: none;
        }
        .amazon-auto-links-button.amazon-auto-links-button-{$isButtonID} > a {
            color: inherit; 
            border-bottom: none;
            text-decoration: none;
        }
        .amazon-auto-links-button.amazon-auto-links-button-{$isButtonID} > a:hover {
            color: inherit;
        }        
        ";
        
    }

}