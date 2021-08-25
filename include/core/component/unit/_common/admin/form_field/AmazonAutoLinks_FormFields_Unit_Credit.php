<?php
/**
 * Provides the definitions of auto-insert form fields for units.
 * 
 * @since 3.2.3
 * @since 4.5.0 Change the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_Unit_Credit extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
        $_oOption       = AmazonAutoLinks_Option::getInstance();
        return array(    
            array(
                'field_id'          => $sFieldIDPrefix . 'credit_link',
                'type'              => 'revealer',
                'select_type'       => 'radio',
                'label_min_width'   => '140px;',
                'title'             => __( 'Credit Link', 'amazon-auto-links' ),
                'label'             => array(                        
                    1   => __( 'On', 'amazon-auto-links' ),
                    0   => __( 'Off', 'amazon-auto-links' ),
                ),
                'tip'               => __( 'Inserts the credit link at the end of the unit output.', 'amazon-auto-links' ),
                'default'           => 1,
                'selectors'         => array(
                    1   => '.fieldrow_credit_link_type',                
                ),
            ),          
            array(
                'field_id'          => $sFieldIDPrefix . 'credit_link_type',
                'type'              => 'radio',
                'title'             => __( 'Credit Link Type', 'amazon-auto-links' ),
                'label'             => array(
                    0   => __( 'Normal', 'amazon-auto-links' ) 
                        . "<div style='width: 160px; height: 160px; margin-top: 1em;'>" 
                            . apply_filters( 'aal_filter_credit_link_0', '', $this->oOption )
                        . "</div>",
                    1   => __( 'Square Image', 'amazon-auto-links' ) 
                        . "<div style='width: 200px; height: 160px; margin-top: 1em;'>" 
                            . apply_filters( 'aal_filter_credit_link_1', '', $this->oOption )
                        . "</div>",
                    2   => __( 'Horizontal Image', 'amazon-auto-links' ) 
                        . "<div style='width: 320px; margin-top: 1em;'>" 
                            . apply_filters( 'aal_filter_credit_link_2', '', $this->oOption )
                        . "</div>",
                ),
                'label_min_width'   => '100px; vertical-align: top;',
                'default'           => 1,
                'hidden'            => true,
                'class'             => array(
                    'fieldrow'  => 'fieldrow_credit_link_type'
                ),
                'description'       => array(
                    $_oOption->get( 'miunosoft_affiliate', 'affiliate_id' )
                        ? ''
                        : '<span class="icon-info dashicons dashicons-info"></span>'
                          . sprintf(
                              __( 'Participate in the <a href="%1$s" target="_blank">Pro affiliate program</a> and get an affiliate ID. Then earn commissions by setting it <a href="%2$s">here</a>.', 'amazon-auto-links' ),
                              esc_url( 'https://store.michaeluno.jp/amazon-auto-links-pro/affiliate-area/' ),
                              esc_url( apply_filters( 'aal_filter_opt_setting_tab_url', '', 'affiliate_id' ) )
                          ),
                ),
            )
        );
    }
  
}