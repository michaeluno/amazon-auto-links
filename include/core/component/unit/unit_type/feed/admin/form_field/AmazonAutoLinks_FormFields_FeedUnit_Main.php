<?php
/**
 * Provides the definitions of form fields for the 'feed' unit type.
 * 
 * @since           4.0.0
 */
class AmazonAutoLinks_FormFields_FeedUnit_Main extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
            
        $_oOption = $this->oOption;
        $_aFields = array(  
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'feed',
            ),          
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
                'type'          => 'text',
                'description'   => 'e.g. <code>My Feed Unit</code>',
                'value'         => '',    // a previous value should not appear
            ),
// @todo the URL unit type's this field definition needs to be updated to the latest supported locales by PA-API.
//            array(
//                'field_id'      => $sFieldIDPrefix . 'country',
//                'type'          => 'select',
//                'title'         => __( 'Country', 'amazon-auto-links' ),
//                'label'         => array(
//                    'CA' => 'CA - ' . __( 'Canada', 'amazon-auto-links' ),
//                    'CN' => 'CN - ' . __( 'China', 'amazon-auto-links' ),
//                    'FR' => 'FR - ' . __( 'France', 'amazon-auto-links' ),
//                    'DE' => 'DE - ' . __( 'Germany', 'amazon-auto-links' ),
//                    'IT' => 'IT - ' . __( 'Italy', 'amazon-auto-links' ),
//                    'JP' => 'JP - ' . __( 'Japan', 'amazon-auto-links' ),
//                    'UK' => 'UK - ' . __( 'United Kingdom', 'amazon-auto-links' ),
//                    'ES' => 'ES - ' . __( 'Spain', 'amazon-auto-links' ),
//                    'US' => 'US - ' . __( 'United States', 'amazon-auto-links' ),
//                    'IN' => 'IN - ' . __( 'India', 'amazon-auto-links' ),
//                    'BR' => 'BR - ' . __( 'Brazil', 'amazon-auto-links' ),
//                    'MX' => 'MX - ' . __( 'Mexico', 'amazon-auto-links' ),
//                    'AU' => 'AU - ' . __( 'Australia', 'amazon-auto-links' ),
//                ),
//                'default' => 'US',
//            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'feed_urls',
                'type'          => 'text',
                'title'         => __( 'JSON Feed URL', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style' => 'min-width: 520px; max-width: 100%; ',
                    'size'  => version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' )
                        ? 40 
                        : 60,
                ),
                'repeatable'    => array(
                    'max'   => $_oOption->isAdvancedAllowed()
                        ? 0
                        : 1,
                ),
                'description'   => array(
                    __( 'Paste the JSON feed URL of an Amazon Auto Links unit on an external site.', 'amazon-auto-links' )
                    . ' e.g. <code>http://{your-site}?productlink=feed&output=json&id=189</code>',
                    isset( $_GET[ 'page' ] ) && AmazonAutoLinks_Registry::$aAdminPages[ 'feed_unit' ] === $_GET[ 'page' ]
                        ? '<img src="' . $this->getSRCFromPath( AmazonAutoLinks_UnitTypeLoader_feed::$sDirPath . '/asset/image/screenshot_json_link.png' ) . '" style="display: inline-block; max-height: 140px;" />'
                        : null,
                ),
            ),
            // @todo check the `sort` key compatibility with the category unit type that feed unit type extends
            array(
                'field_id'          => $sFieldIDPrefix . 'sort',
                'type'              => 'select',
                'title'             => __( 'Sort Order', 'amazon-auto-links' ),
                'label'             => array(                        
                    'raw'               => __( 'Raw', 'amazon-auto-links' ),
                    'title'             => __( 'Title', 'amazon-auto-links' ),
                    'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
                    'random'            => __( 'Random', 'amazon-auto-links' ),
                ),
                'tip'               => __( 'In order not to sort and leave it as the found order, choose <code>Raw</code>.', 'amazon-auto-links' ),
                'default'           => 'raw',
            ),
        );
        return $_aFields;
        
    }
  
}