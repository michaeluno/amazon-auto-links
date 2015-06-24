<?php
/**
 * Deals with the plugin admin pages.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.5
 * 
 */
abstract class AmazonAutoLinks_AdminPage_Help extends AmazonAutoLinks_AdminPage_About {

    /*
     * The Help Page
     */
    public function do_before_aal_help() {    // do_before_ + {page slug}
        
        include_once( AmazonAutoLinks_Commons::$strPluginDirPath . '/include/library/wordpress-plugin-readme-parser/parse-readme.php' );
        $this->oWPReadMe = new WordPress_Readme_Parser;
        $this->arrWPReadMe = $this->oWPReadMe->parse_readme( AmazonAutoLinks_Commons::$strPluginDirPath . '/readme.txt' );
        
    }
    public function do_aal_help_install() {        // do_ + page slug + _ + tab slug
        echo $this->arrWPReadMe['sections']['installation'];
    }    
    public function do_aal_help_faq() {        // do_ + page slug + _ + tab slug
        echo $this->arrWPReadMe['sections']['frequently_asked_questions'];
    }
    public function do_aal_help_notes() {        // do_ + page slug + _ + tab slug
        
        include_once( AmazonAutoLinks_Commons::$strPluginDirPath . '/include/library/simple_html_dom.php' ) ;

        $_oHTML = str_get_html( $this->arrWPReadMe['remaining_content'] );
        
        $_oHTML->find( 'h3', 0 )->outertext = '';
        $_oH3_1 =  $_oHTML->find( 'h3', 1 );
        if ( is_object( $_oH3_1 ) ) {
            $_oH3_1->outertext = '';
        }
        
        $_sTOC = '';
        $_iLastLevel = 0;

        foreach( $_oHTML->find( 'h4,h5,h6' ) as $_oHTag ){    // original: foreach($html->find('h1,h2,h3,h4,h5,h6') as $_oHTag
            $_sInnerTEXT = trim( $_oHTag->innertext );
            $_sID =  str_replace( ' ', '_', $_sInnerTEXT );
            $_oHTag->id = $_sID; // add id attribute so we can jump to this element
            $_iLevel = intval( $_oHTag->tag[ 1 ] );

            if( $_iLevel > $_iLastLevel )
                $_sTOC .= "<ol>";
            else{
                $_sTOC .= str_repeat( '</li></ol>', $_iLastLevel - $_iLevel );
                $_sTOC .= '</li>';
            }

            $_sTOC .= "<li><a href='#{$_sID}'>{$_sInnerTEXT}</a>";

            $_iLastLevel = $_iLevel;
        }

        $_sTOC .= str_repeat( '</li></ol>', $_iLastLevel );
        echo $_sTOC . "<hr />" . $_oHTML->save();    
        
    }    
        
}