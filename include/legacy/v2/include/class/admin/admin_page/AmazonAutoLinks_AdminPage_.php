<?php
/**
 * Deals with the plugin admin pages.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 * 
 */
abstract class AmazonAutoLinks_AdminPage_ extends AmazonAutoLinks_AdminPage_AddUnitByCategory {
    
    /*
     * Layout the setting pages
     * */
    function head_AmazonAutoLinks_AdminPage( $strHead ) {

        return '<div class="top-right">' . $this->oUserAds->getTopBanner() . '</div>'
            . $strHead 
            . '<div class="amazon-auto-links-admin-body">'
                . '<table border="0" cellpadding="0" cellspacing="0" unselectable="on" width="100%">'
                    . '<tbody>'
                        . '<tr>'
                            . '<td valign="top">'
                                . '<div style="margin-top: 10px;">'
                                    . $this->oUserAds->getTextAd()
                                . '</div>';
            
    }
    function foot_AmazonAutoLinks_AdminPage( $strFoot ) {
        
        switch ( isset( $_GET['tab'] ) ? $_GET['tab'] : '' ) {
            case 'tabname':
                $numItems = defined( 'WPLANG' ) && WPLANG == 'ja' ? 4 : 4;
                break;
            default:
                $numItems = 4;
                break;
        }    
        
        return $strFoot 
                        // . '<div style="float:left; margin-top: 10px" >' 
                        // . $this->oUserAds->getTextAd() 
                        // . '</div>'
                            . '</td>
                            <td valign="top" rowspan="2" style="padding-top:20px;">' 
                            . ( rand( 0, 1 ) ? $this->oUserAds->get160xNTopRight() : $this->oUserAds->get160xN( $numItems ) )
                            // . $this->oUserAds->GetSkyscraper( $numItems ) 
                            . '</td>
                        </tr>
                        <tr>
                            <td valign="bottom" align="center">'
                                // . $this->oUserAds->getBottomBanner() 
                        . '</td>
                        </tr>
                    </tbody>
                </table>'
            . '</div><!-- end amazon-auto-links-admin-body -->';
            
    }     
            
    /**
     * The global page load
     * 
     */
    public function load_AmazonAutoLinks_AdminPage() {

        // Check the support rate and ads visibility
        if ( 
            ! ( isset( $_GET['tab'], $_GET['bounce_url'] ) && $_GET['tab'] == 'support' )
            && ! $this->oOption->arrOptions['aal_settings']['support']['agreed']
            && $this->oOption->isSupportMissing()
        ) {
            
            $strBounceURL = htmlspecialchars_decode( AmazonAutoLinks_WPUtilities::getCurrentAdminURL() );
            $strBounceURL = str_replace( 'tab=support', '', $strBounceURL );        // prevent infinite redirects            ;
            $strBounceURL = remove_query_arg( 'aal-option-upgrade', $strBounceURL );
            AmazonAutoLinks_WPUtilities::setTransient( 'AAL_BounceURL', $strBounceURL, 60*10 );        
            exit(
                wp_redirect( 
                    admin_url( 'edit.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlug . '&page=aal_settings&tab=support&bounce_url=AAL_BounceURL' ) 
                )
            );
        
        }

        // Check the v1 options exist and redirect to the v1 options importer.
        if ( 
            ! ( isset( $_GET['tab'], $_GET['bounce_url'] ) && ( $_GET['tab'] == 'import_v1_options' || $_GET['tab'] == 'support' ) )
            && ! $this->oOption->arrOptions['aal_settings']['import_v1_options']['dismiss']
            && false !== get_option( 'amazonautolinks' )
        ) {
            
            $strBounceURL = htmlspecialchars_decode( AmazonAutoLinks_WPUtilities::getCurrentAdminURL() );
            $strBounceURL = str_replace( 'tab=import_v1_options', '', $strBounceURL );        // prevent infinite redirects
            AmazonAutoLinks_WPUtilities::setTransient( 'AAL_BounceURL_Importer', $strBounceURL, 60*10 );
            $this->setAdminNotice( 
                sprintf( 
                    __( 'Please upgrade the options of previous versions of the plugin by clicking <a href="%1$s">here</a>.', 'amazon-auto-links' )
                    . ' ' . __( 'Before you do it, please <strong>back up</strong> the database.', 'amazon-auto-links' )
                    . ' ' . __( 'Dismiss this message by clicking <a href="%2$s">here</a>.', 'amazon-auto-links' ),
                    admin_url( 'edit.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlug . '&page=aal_settings&tab=import_v1_options&bounce_url=AAL_BounceURL_Importer' ),
                    admin_url( 'edit.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlug . '&page=aal_settings&tab=import_v1_options&action=dismiss&bounce_url=AAL_BounceURL_Importer' )
                ),
                'error'    
            );            
            return;
        }
            
        // Check v1 option importer messages
        if ( isset( $_GET['aal-option-upgrade'] ) ) {
            
            switch( $_GET['aal-option-upgrade'] ) {
                case 'not-found' :   
                    $this->setAdminNotice( __( 'Could not find the options to import.', 'amazon-auto-links' ), 'error' );
                break;
                case 'succeed' :   
                    $this->setAdminNotice( sprintf( __( 'Options have been imported. ( %1$s unit(s) )', 'amazon-auto-links' ), $_GET['count'] ), 'updated' );
                break;
                case 'partial' :   
                    $this->setAdminNotice( sprintf( __( 'Options been partially imported. ( %1$s unit(s) )', 'amazon-auto-links' ), $_GET['count'] ), 'error' );
                break;                
                case 'failed' :
                    $this->setAdminNotice( __( 'No unit was imported.', 'amazon-auto-links' ), 'error' );
                break;
                
            }

        }
       
        // 3+ Add a setting notice to upgrade the options to v3
        $_sBounceURL = htmlspecialchars_decode( AmazonAutoLinks_WPUtilities::getCurrentAdminURL() );
        $_sBounceURL = str_replace( 'tab=create_v3_options', '', $_sBounceURL ); // prevent infinite redirects
        AmazonAutoLinks_WPUtilities::setTransient( 'AAL_BounceURL_Importer', $_sBounceURL, 60*10 );
        $this->setAdminNotice( 
            '<strong>' . AmazonAutoLinks_Commons::Name . '</strong>: '
            . sprintf( 
                __( 'Please upgrade the options by clicking <strong><a href="%1$s">here</a></strong>.', 'amazon-auto-links' )
                . ' ' . __( 'Before you do it, please <strong>back up</strong> the database.', 'amazon-auto-links' ),
                admin_url( 'edit.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlug . '&page=aal_settings&tab=create_v3_options&bounce_url=AAL_BounceURL_Importer' )
            ),
            'error'    
        );            

    }
    
    /**
     * The global validation task.
     */
    public function validation_AmazonAutoLinks_AdminPage( $arrInput, $arrOldInput ) {
        
        // Deal with the reset button.
        // [option key][page slug][section][field]
        if ( isset( $_POST[ AmazonAutoLinks_Commons::AdminOptionKey ]['aal_settings']['reset_settings']['options_to_delete'] ) ) {
            
            $arrReset = $_POST[ AmazonAutoLinks_Commons::AdminOptionKey ]['aal_settings']['reset_settings']['options_to_delete'];
            if ( $arrReset['all'] )
                return array();    // this will save an empty array in the option.
            if ( $arrReset['general'] )
                unset( $arrInput['aal_settings'] );    // removes the element named 'aal_settings' from the options array
            if ( $arrReset['template'] )
                unset( $arrInput['arrTemplates'] ); // removes the element named 'arrTemplates' from the options array
    
        }
        
        // Manually set the support rate and ad visibility. 
        // this should be done in the global validation callback, not in validation_{pageslug}_{tab} as modified values in that method will be lost when merged with the global one.
        if ( isset( $_POST['tab'] ) && $_POST['tab'] == 'support' )
            foreach ( $arrInput['aal_settings']['initial_support'] as $strKey => $vValue  )
                $arrInput['aal_settings']['support'][ preg_replace( '/^initial_/', '', $strKey ) ] = $vValue;

        return $arrInput;
        
    }
        
}