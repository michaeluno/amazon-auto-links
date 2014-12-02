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
abstract class AmazonAutoLinks_AdminPage_Setting extends AmazonAutoLinks_AdminPage_Template {

    /*
     * Settings Page
     */    
    public function do_after_aal_settings () {    // do_after_ + page slug
    
        if ( ! $this->oOption->isDebugMode() ) return;
        echo "<h4>Saved Options</h4>";
        echo $this->oDebug->getArray( $this->oProps->arrOptions  );
        echo "<h4>Actual Options</h4>";
        echo "<p class='description'>Options merged with plugin's default option values</p>";
        echo $this->oDebug->getArray( $this->oOption->arrOptions );
    
    }
    
    public function validation_aal_settings( $arrInput, $arrOldInput ) {    // validation_ + page slug
        return $arrInput;    
    }
    
    public function do_form_aal_settings_authentication() {
    
        $this->renderAuthenticationStatus();
    

        
    // AmazonAutoLinks_Debug::dumpArray( $arrResponse );
        
    }
    /**
     * Renders the authentication status table.
     * 
     * @since            2.0.0
     * @param            array            $arrStatus            This arrays should be the merged array of the results of 'account/verify_credientials' and 'rate_limit_status' requests.
     * 
     */
    protected function renderAuthenticationStatus() {
    
        $strPublicKey = $this->getFieldValue( 'access_key' );
        $strPrivateKey = $this->getFieldValue( 'access_key_secret' );
        $oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 'com', $strPublicKey, $strPrivateKey );
        $fVerified = $oAmazonAPI->test();

        ?>        
        <h3><?php _e( 'Status', 'amazon-auto-links' ); ?></h3>
        <table class="form-table auth-status">
            <tbody>
                <tr valign="top">
                    <th scope="row">
                        <?php _e( 'Status', 'amazon-auto-links' ); ?>
                    </th>
                    <td>
                        <?php echo $fVerified ? '<span class="authenticated">' . __( 'Authenticated', 'amazon-auto-links' ) . '</span>': '<span class="unauthenticated">' . __( 'Not authenticated', 'amazon-auto-links' ) . '</span>'; ?>
                    </td>
                </tr>
            </tbody>
        </table>
                    
        <?php

        // $arrResponse = $oAmazonAPI->request(
            // array(
                // 'Operation' => 'BrowseNodeLookup',
                // 'BrowseNodeId' => '0',    // the Books node 
                // 'BrowseNodeId' => '1000,301668',    // the Books node 
            // ),
            // 'US'    // or 'com' would work
        // );    


// AmazonAutoLinks_Debug::dumpArray( $arrResponse );
        
    }
    
    public function validation_aal_settings_authentication( $arrInput, $arrOldInput ) {    // validation_ + page slug + tab slug

        $fVerified = true;
        $arrErrors = array();

        // Access Key must be 20 characters
        $arrInput['aal_settings']['authentication_keys']['access_key'] = trim( $arrInput['aal_settings']['authentication_keys']['access_key'] );
        $strPublicKey = $arrInput['aal_settings']['authentication_keys']['access_key'];
        if ( strlen( $strPublicKey ) != 20 ) {
            
            $arrErrors['authentication_keys']['access_key'] = __( 'The Access Key ID must consist of 20 characters.', 'amazon-auto-links' ) . ' ';
            $fVerified = false;
            
        }
        
        // Access Secret Key must be 40 characters.
        $arrInput['aal_settings']['authentication_keys']['access_key_secret'] = trim( $arrInput['aal_settings']['authentication_keys']['access_key_secret'] );
        $strPrivateKey = $arrInput['aal_settings']['authentication_keys']['access_key_secret'];
        if ( strlen( $strPrivateKey ) != 40 ) {
            
            $arrErrors['authentication_keys']['access_key_secret'] = __( 'The Secret Access Key must consist of 40 characters.', 'amazon-auto-links' ) . ' ';
            $fVerified = false;
            
        }
        
        // An invalid value is found.
        if ( ! $fVerified ) {
        
            // Set the error array for the input fields.
            $this->setFieldErrors( $arrErrors );
            $this->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $arrOldInput;
            
        }            
    
        // Test authentication - browse the Books node in amazon.com.
        $oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 'com', $strPublicKey, $strPrivateKey );
        if ( ! $oAmazonAPI->test() ) {
            
            $arrErrors['authentication_keys']['access_key'] = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $strPublicKey;
            $arrErrors['authentication_keys']['access_key_secret'] = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $strPrivateKey;            
            $this->setFieldErrors( $arrErrors );
            $this->setSettingNotice( __( 'Failed authentication.', 'amazon-auto-links' ) );
            $arrOldInput;
            
        }


        return $arrInput;
        
    }
    
    public function validation_aal_settings_misc( $arrInput, $arrOldInput ) {
        
        // Sanitize text inputs
        // [aal_settings] => Array
                // [form_options] => Array
                        // [allowed_html_tags]     
        $arrInput['aal_settings']['form_options']['allowed_html_tags'] = trim( AmazonAutoLinks_Utilities::trimDelimitedElements( $arrInput['aal_settings']['form_options']['allowed_html_tags'], ',' ) );
        return $arrInput;
    }
    
    public function validation_aal_settings_general( $arrInput, $arrOldInput ) {
        
        // Sanitize text inputs
        foreach( $arrInput['aal_settings']['product_filters']['black_list'] as &$str1 )
            $str1 = trim( AmazonAutoLinks_Utilities::trimDelimitedElements( $str1, ',' ) ); 
        foreach( $arrInput['aal_settings']['product_filters']['white_list'] as &$str2 ) 
            $str2 = trim( AmazonAutoLinks_Utilities::trimDelimitedElements( $str2, ',' ) );            
            
        // Sanitize the query key.
        $arrInput['aal_settings']['query']['cloak'] = AmazonAutoLinks_Utilities::sanitizeCharsForURLQueryKey( $arrInput['aal_settings']['query']['cloak'] );
        
        
/*       [aal_settings] => Array
        (
            [product_filters] => Array
                (
                    [white_list] => Array
                        (
                            [asin] => 
                            [title] => 
                            [description] => 
                        )

                    [black_list] => Array
                        (
                            [asin] => 
                            [title] => 
                            [description] => 
                        )

                )

            [support] => Array
                (
                    [rate] => 10
                    [review] => 0
                )

            [query] => Array
                (
                    [cloak] => productlink
                    [submit_general] => Save Changes
                )

        )     */
// AmazonAutoLinks_Debug::logArray( $arrInput );                    
        return $arrInput;
        
    }
    
        
    public function validation_aal_settings_reset( $arrInput, $arrOldInput ) {

        if ( isset( $arrInput['aal_settings']['caches']['clear_caches'] ) && $arrInput['aal_settings']['caches']['clear_caches'] ) {
            AmazonAutoLinks_WPUtilities::cleanTransients( 'AAL' );
            $this->setSettingNotice( __( 'The caches have been cleared.', 'amazon-auto-links' ) );            
        }

        return $arrOldInput;    // no need to update the options.
        
    }

    public function load_aal_settings_support() {
        
        $this->setAdminNotice( __( 'Please select your preferences.', 'amazon-auto-links' ), 'updated' );
        $this->showInPageTabs( false, 'aal_settings' );
    
    }
    
    public function validation_aal_settings_support( $arrInput, $arrOldInput ) {
        

        return $arrInput;
        
    }
    
    /**
     * The v1 Option Importer page(tab)
     */
    public function load_aal_settings_import_v1_options() {
        
        if ( ! isset( $_GET['bounce_url'] ) ) return;
        
        $strBounceURL = AmazonAutoLinks_WPUtilities::getTransient( $_GET['bounce_url'] );    // AAL_BounceURL_Importer
        
// AmazonAutoLinks_Debug::logArray( $strBounceURL );
        // $strBounceURL = $this->oEncode->decode( $_GET['bounce_url'] );

        // If the Dismiss link is selected, 
        if ( isset( $_GET['action'] ) && $_GET['action'] == 'dismiss' ) {
            $this->oOption->arrOptions['aal_settings']['import_v1_options']['dismiss'] = true;
            $this->oOption->save();
            die( wp_redirect( $strBounceURL ) );
            
        } 
        
        $oImportV1Options = new AmazonAutoLinks_ImportV1Options;
        
        $arrV1Options = get_option( 'amazonautolinks' );        
        if ( $arrV1Options === false ) {
            $this->oOption->arrOptions['aal_settings']['import_v1_options']['dismiss'] = true;
            $this->oOption->save();            
            die( wp_redirect( $strBounceURL . "&aal-option-upgrade=not-found" ) );
        }
    
        $intRemained = $this->oOption->getRemainedAllowedUnits();            
// AmazonAutoLinks_Debug::logArray( "remained allowed number of units: " . $intRemained );        
        if ( $intRemained > 0 ) {
            
            // Import units and general options and delete the option from the database
            $oImportV1Options->importGeneralSettings( $arrV1Options['general'] );
            $intCount = $oImportV1Options->importUnits( $arrV1Options['units'] );
            
            // Delete the old options from the database.
// delete_option( 'amazonautolinks' );
            
            $this->oOption->arrOptions['aal_settings']['import_v1_options']['dismiss'] = true;
            $this->oOption->save();
            if ( $intCount )
                die( wp_redirect( $strBounceURL . "&aal-option-upgrade=succeed&count={$intCount}" ) );
            else 
                die( wp_redirect( $strBounceURL . "&aal-option-upgrade=failed" ) );
        }
        
        // Means it's free version and the old version has more than the allowed units.
        // In this case, just import the remained allowed number of units and leave them and do not delete the v1's old options from the database.
        $oImportV1Options->importGeneralSettings( $arrV1Options['general'] );
        $arrV1Units = array_slice( $arrV1Options['units'], 0, $intRemained );

        $intCount = $oImportV1Options->importUnits( $arrV1Units );
        $this->oOption->arrOptions['aal_settings']['import_v1_options']['dismiss'] = true;
        $this->oOption->save();
        if ( $intCount )
            die( wp_redirect( $strBounceURL . "&aal-option-upgrade=partial&count={$intCount}" ) );
        else 
            die( wp_redirect( $strBounceURL . "&aal-option-upgrade=failed" ) );
        
    }
    
}