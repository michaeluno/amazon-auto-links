<?php
abstract class AmazonAutoLinks_MetaBox_Categories_ {

    public function __construct() {
        
        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] != AmazonAutoLinks_Commons::PostTypeSlug ) return;
        add_action( 'add_meta_boxes', array( $this, 'addCustomMetaBoxes' ) );
        
    }
    
    public function addCustomMetaBoxes() {

        // Sponsors' box.
        add_meta_box( 
            'amazon_auto_links_meta_box_category_box',         // id
            __( 'Added Categories', 'amazon-auto-links' ),     // title
            array( $this, 'drawCategoryBox' ),     // callback
            AmazonAutoLinks_Commons::PostTypeSlug,        // post type
            'side',     // context ('normal', 'advanced', or 'side'). 
            'high',    // priority ('high', 'core', 'default' or 'low') 
            null // argument
        );    
        
    }
    
    public function drawCategoryBox() {
        
        $strSelectCategoryPageURL = add_query_arg( 
            array( 
                'mode' => 0,
                'page' => 'aal_add_category_unit',
                'tab' => 'select_categories',
                'post_type' => AmazonAUtoLinks_Commons::PostTypeSlug,
                'post' => isset( $_GET['post'] ) ? $_GET['post'] : '',
                'transient_id' => uniqid(),
            ) 
            , admin_url( 'edit.php' ) 
        );
// wp-admin/edit.php?post_type=amazon_auto_links&page=aal_add_category_unit

        ?>
        <div style="padding: 2em 0 1.5em; ">
            <div style="text-align: center;">
                <a class="button button-primary button-large" href="<?php echo $strSelectCategoryPageURL; ?>">
                    <?php _e( 'Select Categories', 'amazon-auto-links' ); ?>
                </a>
            </div>        
        </div>
        <?php
        if ( ! isset( $_GET['post'] ) ) return;
        
        $arrCategories = get_post_meta( $_GET['post'], 'categories', true );
        $arrCategories = is_array( $arrCategories ) ? $arrCategories : array();    // do not cast since null value creates a 0 index when casted.
        $arrExcludingCategories =  get_post_meta( $_GET['post'], 'categories_exclude', true );
        $arrExcludingCategories = is_array( $arrExcludingCategories ) ? $arrExcludingCategories : array();
        
        // $oEncrypt = new AmazonAutoLinks_Encrypt;
        $arrCategoryList = array();
        foreach( $arrCategories as $arrCategory ) 
            $arrCategoryList[] = "<li style=''>" . $arrCategory['breadcrumb'] . "</li>";
            // $arrCategoryList[] = "<li style=''>" . $oEncrypt->decode( $arrCategory['breadcrumb'] ) . "</li>";
        $arrExcludingCategoryList = array();
        foreach( $arrExcludingCategories as $arrCategory ) 
            $arrExcludingCategoryList[] = "<li style=''>" . $arrCategory['breadcrumb'] . "</li>";            
        
        if ( empty( $arrCategoryList ) )
            $arrCategoryList[] = "<li>" . __( 'No category added.', 'amazon-auto-links' ) . "</li>";
            
        if ( empty( $arrExcludingCategoryList ) )
            $arrExcludingCategoryList[] = "<li>" . __( 'No excluding sub-category added.', 'amazon-auto-links' ) . "</li>";
            
        echo "<h4 style='text-align: center'>" . __( 'Added Categories', 'amazon-auto-links' ) . "</h4>"
            . "<div style='text-align: center; font-weight: normal; padding-bottom: 0.5em;'>"
                . "<ul style='margin-left:0;'>" 
                    . implode( '', $arrCategoryList ) 
                . "</ul>"
            . "</div>"
            . "<h4 style='text-align: center'>" . __( 'Added Excluding Sub-categories', 'amazon-auto-links' ) . "</h4>"
            . "<div style='text-align: center; font-weight: normal; padding-bottom: 0.5em;'>"
                . "<ul style='margin-left:0;'>" 
                    . implode( '', $arrExcludingCategoryList ) 
                . "</ul>"
            . "</div>";
    
        // echo "<pre>" . print_r( $arrCategories, true ) . "</pre>";    
    }    
    
}