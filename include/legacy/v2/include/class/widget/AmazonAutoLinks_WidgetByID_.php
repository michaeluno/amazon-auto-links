<?php

abstract class AmazonAutoLinks_WidgetByID_ extends AmazonAutoLinks_Widget_ {

    // This method should be called in the bootstrap script.
    public static function registerWidget() {
        return register_widget( 'AmazonAutoLinks_WidgetByID' );    // the class name - get_class( self ) does not work.
    }    
    
    public function __construct() {
                        
        parent::__construct(
             'amazon_auto_links_widget_by_id', // base ID
            'Amazon Auto Links by Unit',     // widget name
            array( 'description' => __( 'A widget that display Amazon products of defined Amazon Auto Links units.', 'amazon-auto-links' ), ) 
        );
        
    }

    protected function echoContents( $arrInstance ) {
        AmazonAutoLinks( $arrInstance );
// var_dump( $arrInstance );
    }
    
    protected function echoFormElements( $arrInstance, $arrIDs, $arrNames ) {
    ?>
        <label for="<?php echo $arrIDs['title']; ?>">
            <?php _e( 'Title', 'amazon-auto-links' ); ?>:
        </label>
        <p>
            <input type="text" name="<?php echo $arrNames['title']; ?>" id="<?php echo $arrIDs['title']; ?>" value="<?php echo $arrInstance['title']?>"/>
        </p>
        
        <label for="<?php echo $arrIDs['id']; ?>">
            <?php _e( 'Select Rules', 'amazon-auto-links' ); ?>:
        </label>
        <br />
        <select name="<?php echo $arrNames['id']; ?>[]" id="<?php echo $arrIDs['id']; ?>"  multiple style="min-width: 220px;">
            <?php 
            $oQuery = new WP_Query(
                array(
                    'post_status' => 'publish',     // optional
                    'post_type' => AmazonAutoLinks_Commons::PostTypeSlug,// 'amazon_auto_links', //  post_type
                    'posts_per_page' => -1, // ALL posts
                )
            );            
            foreach( $oQuery->posts as $oPost ) 
                echo "<option value='{$oPost->ID}' "                
                    . ( in_array( $oPost->ID, $arrInstance['id'] ) ? 'selected="Selected"' : '' )
                    . ">"
                    . $oPost->post_title
                    . "</option>";
            ?>
        </select>
        <p class="description" style="margin-top: 10px;">
            <?php _e( 'Hold down the Ctrl (windows) / Command (Mac) key to select multiple items.', 'amazon-auto-links' ); ?>
        </p>     
            
    <?php
    }
    
    public function update( $arrNewInstance, $arrOldInstance ) {
        return $arrNewInstance;
    }
    
}