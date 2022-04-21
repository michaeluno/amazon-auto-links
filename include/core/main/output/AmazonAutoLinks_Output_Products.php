<?php
/**
 * Auto Amazon Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Retrieves product output as array.
 * 
 * @since 5.2.6
*/
class AmazonAutoLinks_Output_Products extends AmazonAutoLinks_Output {

    /**
     * @since 5.2.6
     * @var   array 
     */
    public $aProducts = array();
    
    /**
     * Instantiates the class and returns the object.
     *
     * This is for calling methods in one line like
     * ```
     * $_aProducts = AmazonAutoLinks_Output_Products::getInstance()->get();
     * ```
     *
     * @since  5.2.6
     * @param  array $aArguments
     * @return AmazonAutoLinks_Output_Products
     */
    static public function getInstance( $aArguments ) {
        return new self( $aArguments );
    }

    /**
     * Sets up properties and hooks.
     */
    public function __construct( $aArguments ) {
        parent::__construct( $aArguments );
        $this->aArguments = array( '_no_rendering' => true ) + $this->aArguments;
    }

    /**
     * @since 5.2.6
     */
    public function render() {
        AmazonAutoLinks_Debug::dump( $this->get() );
    }

    /**
     * @since  5.2.6
     * @return array
     */
    public function get() {
        $this->aProducts = array();
        add_filter( 'aal_filter_products', array( $this, 'replyToCaptureProducts' ), 100 ); // set low priority as an error capture callback needs to be intercepted before this
        AmazonAutoLinks_Output::getInstance( $this->aArguments )->get();
        remove_filter( 'aal_filter_products', array( $this, 'replyToCaptureProducts' ), 100 );
        return $this->aProducts;
    }

    /**
     * @since  5.2.6
     * @param  array $aProducts
     * @return array
     */
    public function replyToCaptureProducts( $aProducts ) {
        $this->aProducts = $aProducts;
        return $aProducts;
    }

}