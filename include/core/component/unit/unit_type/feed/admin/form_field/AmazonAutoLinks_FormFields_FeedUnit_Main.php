<?php
/**
 * Provides the definitions of form fields for the 'feed' unit type.
 * 
 * @since  4.0.0
 * @since  4.5.0    Changed the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_FeedUnit_Main extends AmazonAutoLinks_FormFields_Unit_Base {

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
                'class'         => array(
                    'input' => 'width-full',
                    'field' => 'width-half',
                ),
                'value'         => '',    // a previous value should not appear
            ),
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
                'class'         => array(
                    'input' => 'width-full',
                    'field' => 'width-two-third',
                ),
                'repeatable'    => array(
                    'max'   => $_oOption->isAdvancedAllowed()
                        ? 0
                        : 1,
                ),
                'description'   => array(
                    __( 'Paste the JSON feed URL of an Auto Amazon Links unit on an external site.', 'amazon-auto-links' )
                    . ' e.g. <code>http://{your-site}?productlink=feed&output=json&id=189</code>',
                    isset( $_GET[ 'page' ] ) && AmazonAutoLinks_Registry::$aAdminPages[ 'feed_unit' ] === $_GET[ 'page' ]   // sanitization unnecessary as just checking
                        ? '<img src="' . $this->getSRCFromPath( AmazonAutoLinks_Unit_UnitType_Loader_feed::$sDirPath . '/asset/image/screenshot_json_link.png' ) . '" style="display: inline-block; max-height: 140px;" />'
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