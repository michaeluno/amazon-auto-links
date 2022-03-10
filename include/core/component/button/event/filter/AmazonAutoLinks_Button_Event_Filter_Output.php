<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

 
/**
 *  Outputs the button.
 *  
 *  @since      4.3.0
 */
class AmazonAutoLinks_Button_Event_Filter_Output extends AmazonAutoLinks_PluginUtility {

    protected $_aArguments = array(
        'type'              => 1,    // 0: Link to the product page, 1: Add to cart button.
        'id'                => 0,    // can be omitted
        'asin'              => '',   // comma delimited ASINs
        'quantity'          => '1',   // comma delimited ASINs
        'country'           => '',   // the locale (country)
        'associate_id'      => '',
        'access_key'        => '',   // public PA-API access key
        'label'             => '',   // MUST be empty. Otherwise, the 'button label' unit option does not take efferect.
        'offer_listing_id'  => '',   // offer listing id that Amazon gives
    );

    /**
     * Sets up hooks and properties.
     */
    public function __construct() {
        add_filter( 'aal_filter_linked_button', array( $this, 'replyToGetLinkedButton' ), 10, 2 );
        add_filter( 'aal_filter_button', array( $this, 'replyToGetButton' ), 10, 5 );
    }

    /**
     * @param string $sOutput
     * @param array $aArguments
     * @return string
     */
    public function replyToGetLinkedButton( $sOutput, $aArguments ) {

        $_oOption   = AmazonAutoLinks_Option::getInstance();
        $_sLocale   = $_oOption->getMainLocale();
        $aArguments = $this->getAsArray( $aArguments ) + array(
            'type'         => $_oOption->get( array( 'unit_default', 'button_type' ), 1 ),
            'id'           => 0,   // can be omitted
            'asin'         => '',  // comma delimited ASINs
            'country'      => $_sLocale,
            'associate_id' => $_oOption->get( array( 'unit_default', 'associate_id' ), '' ),  // Associate ID
            'access_key'   => $_oOption->getPAAPIAccessKey( $_sLocale ),
        ) + $this->_aArguments;
        return $this->___getButtonOutput( $aArguments );

    }
        /**
         * @param array $aArguments
         *
         * @return string
         * @since   4.3.0
         */
        private function ___getButtonOutput( array $aArguments ) {
            switch( ( integer ) $aArguments[ 'type' ] ) {
                case 1:
                    return $this->___getAddToCartButton(
                        $aArguments[ 'asin' ],
                        $aArguments[ 'quantity' ],
                        $aArguments[ 'country' ],
                        $aArguments[ 'associate_id' ],
                        $aArguments[ 'id' ],
                        $aArguments[ 'offer_listing_id' ],
                        $aArguments[ 'access_key' ],
                        $aArguments[ 'label' ]
                    );

                default:
                case 0:
                    $_sProductURL = $this->___getProductURL( $aArguments );
                    return $this->___getLinkButton(
                        $aArguments[ 'id' ],
                        $_sProductURL,
                        $aArguments[ 'label' ]
                    );
            }
        }
            /**
             * @param array $aArguments
             * @return string
             */
            private function ___getProductURL( array $aArguments ) {
                $_sLocale    = $aArguments[ 'country' ];
                $_sASIN      = $aArguments[ 'asin' ];
                $_aASINs     = $this->getStringIntoArray( $_sASIN, ',' ); // format it as it can be comma delimited
                $sASIN       = reset( $_aASINs );
                $_oLocale    = new AmazonAutoLinks_Locale( $_sLocale );
                $_sURL       = $_oLocale->getMarketPlaceURL( '/dp/' . $sASIN );
                $_oOption    = AmazonAutoLinks_Option::getInstance();
                $_aDefaults  = $this->getAsArray( $_oOption->get( 'unit_default' ) );
                return apply_filters(
                    'aal_filter_product_link',
                    $_sURL,
                    $_sURL,
                    $sASIN,
                    array_filter( $aArguments ) + $_aDefaults,
                    $this->getElement( $_aDefaults, array( 'language' ), '' ),
                    $this->getElement( $_aDefaults, array( 'preferred_currency' ), '' )
                );
            }

        /**
         * @since  3.1.0
         * @param  string|integer $isButtonID
         * @param  string         $sProductURL
         * @param  string         $sLabel
         * @return string
         */
        private function ___getLinkButton( $isButtonID, $sProductURL, $sLabel ) {
            $sProductURL = esc_url( $sProductURL );
            return "<a href='{$sProductURL}' target='_blank' rel='nofollow noopener' class='amazon-auto-links-button-link'>"
                    . $this->getButton( $isButtonID, $sLabel )
                . "</a>";
        }

        /**
         * Returns an add to cart button.
         *
         * @param string $sASINs        Comma separated ASINs
         * @param string $sQuantities   Comma separated quantities
         * @param string $sLocale
         * @param string $sAssociateID
         * @param string|integer $isButtonID
         * @param string $sOfferListingID
         * @param string $sAccessKey
         * @param string $sLabel
         *
         * @return string
         * @since       3.1.0
         * @see https://webservices.amazon.com/paapi5/documentation/add-to-cart-form.html
         * @since       4.3.0   Moved from `AmazonAutoLinks_UNitOutput_ElementFormat`.
         */
        private function ___getAddToCartButton( $sASINs, $sQuantities, $sLocale, $sAssociateID, $isButtonID, $sOfferListingID, $sAccessKey='', $sLabel='Buy Now' ) {

            $_oLocale       = new AmazonAutoLinks_Locale( $sLocale );
            $_sURL          = $_oLocale->getAddToCartURL();
            $_aQuery        = array(
                'AssociateTag'      => $sAssociateID,
                'SubscriptionId'    => $sAccessKey,
                'AWSAccessKeyId'    => $sAccessKey,
                'OfferListingId'    => $sOfferListingID,
            );
            $_aQuery        = array_filter( $_aQuery );
            $_aASINs        = $this->getStringIntoArray( $sASINs, ',' );
            $_aQuantities   = $this->getStringIntoArray( $sQuantities, ',' );
            foreach( $_aASINs as $iIndex => $_sASIN ) {
                $_iItem = $iIndex + 1;
                $_aQuery[ 'ASIN.' . $_iItem ]     = $_sASIN;
                $_aQuery[ 'Quantity.' . $_iItem ] = isset( $_aQuantities[ $_iItem ] )
                    ? $_aQuantities[ $_iItem ]
                    : 1;
            }
            $_sButtonURL    = esc_url( add_query_arg( $_aQuery, $_sURL ) );
            return "<a href='{$_sButtonURL}' target='_blank' rel='nofollow noopener' class='amazon-auto-links-button-link'>"
                    . $this->getButton( $isButtonID, $sLabel )
                . "</a>";

        }


    /**
     * Returns a button output by a given button (custom post) ID.
     *
     * @param   string          $sOutput
     * @param   integer|string  $isButtonID
     * @param   null|string     $nsLabel
     * @param   boolean         $bVisible
     * @param   boolean         $bOuterContainer
     * @return  string
     * @since   3
     * @since   5.2.0           Accepts an empty string as a label. Use null to reflect the default label, "Buy Now".
     */
    public function replyToGetButton( $sOutput, $isButtonID, $nsLabel='', $bVisible=true, $bOuterContainer=true ) {

        $_sButtonLabel = $this->___getButtonLabel( $isButtonID, $nsLabel );
        $_sButtonLabel = wp_kses( $_sButtonLabel, 'post' );
        $_sNone        = 'none';
        $bVisible      = $bVisible ? '' : "display:{$_sNone};";

        if ( ! $isButtonID ) {
            $_sButton = "<button type='button' class='amazon-auto-links-button'>"
                        . $_sButtonLabel
                    . "</button>";
            return $bOuterContainer
                ? "<div class='amazon-auto-links-button-container' style='{$bVisible}'>"
                        . $_sButton
                    . "</div>"
                : $_sButton;
        }
        $_iButtonID = ( integer ) $isButtonID;
        $_sButtonIDSelector = $_iButtonID > 0   // preview sets it to -1
            ? "amazon-auto-links-button-$isButtonID"
            : "amazon-auto-links-button-___button_id___";
        $_sButton = "<div class='amazon-auto-links-button {$_sButtonIDSelector}'>"
                    . $_sButtonLabel
                . "</div>";
        return $bOuterContainer
            ? "<div class='amazon-auto-links-button-container' style='{$bVisible}'>"
                . $_sButton
            . "</div>"
            : $_sButton;

    }

        /**
         * @param  integer|string $isButtonID
         * @param  string|null    $nsLabel
         * @return string
         * @since  4.3.0
         * @since  5.2.0          Accepts null for the second parameter to respect an empty label (used for image buttons).
         */
        private function ___getButtonLabel( $isButtonID, $nsLabel ) {
            $_bnsButtonLabel = ! is_null( $nsLabel )
                ? $nsLabel
                : (
                    is_numeric( $isButtonID ) && ( $isButtonID > 0 )  // preview sets it to -1
                        ? get_post_meta( $isButtonID, 'button_label', true )
                        : null
                );
            if ( is_string( $_bnsButtonLabel ) ) {  // can be `null` or `false`
                return $_bnsButtonLabel;
            }
            $_oOption = AmazonAutoLinks_Option::getInstance();
            return $_oOption->get( array( 'unit_default', 'override_button_label' ), false )
                ? $_oOption->get( array( 'unit_default', 'button_label' ), '' )
                : __( 'Buy Now', 'amazon-auto-links' );
        }

        /**
         * @since  5.2.0
         * @param  integer|string $isButtonID
         * @return string   Either `classic`, `theme`, `image`, `button2`
         */
        private function ___getButtonType( $isButtonID ) {
            if ( empty( $isButtonID ) ) {
                return 'theme';
            }
            if ( ! is_numeric( $isButtonID ) ) {
                return 'classic';
            }
            if ( $isButtonID > 0 ) {
                return get_post_meta( $isButtonID, '_button_type', true );
            }
            return 'classic';
        }

}