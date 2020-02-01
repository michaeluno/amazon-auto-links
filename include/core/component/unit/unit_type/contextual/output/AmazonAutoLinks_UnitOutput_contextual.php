<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Provides methods to output of contextual units.
 *
 * @remark      Unlike unit output classes of other unit types, this does not extend the unit output base class.
 * @package     Amazon Auto Links
 * @since       3.5.0
 * @filter      aal_filter_contextual_keywords
 */
class AmazonAutoLinks_UnitOutput_contextual extends AmazonAutoLinks_PluginUtility {

    private $___aArguments = array();

    public function __construct( $aArguments ) {
        $this->___aArguments = $this->___getArgumentsFormatted( $aArguments );
    }
        /**
         * @param $aArguments
         * @return array
         */
        private function ___getArgumentsFormatted( $aArguments ) {
            $_oUnitOptions = new AmazonAutoLinks_UnitOption_contextual(
                null,   // unit id
                $aArguments
            );
            return $aArguments + $_oUnitOptions->get();
        }

    /**
     * @return      string
     */
    public function get() {

        $_oContextualSearch = new AmazonAutoLinks_ContextualUnit_SearchKeyword(
            $this->___aArguments[ 'criteria' ],
            $this->___aArguments[ 'additional_keywords' ],
            $this->___aArguments[ 'excluding_keywords' ]
        );
        $_aSearchKeywords   = $_oContextualSearch->get(); // get as an array
        if ( empty( $_aSearchKeywords ) ) {
            return '';
        }

        shuffle ( $_aSearchKeywords );
        array_splice( $_aSearchKeywords, 5 );   // up to 5 keywords.

        // 3.6.0+ This allows third parties to modify search keywords for cases that the keyword gets too long for long product title.
        // @see https://wordpress.org/support/topic/problem-with-title-width/
        $_aSearchKeywords = apply_filters( 'aal_filter_contextual_keywords', $_aSearchKeywords );

        $_aArguments = $this->___aArguments;
        unset( $_aArguments[ 'title' ] );   // this is a widget title field value and causes a conflict with the PA-API payload argument.
        return AmazonAutoLinks(
            array(
                'Keywords'         => implode( ',', $_aSearchKeywords ),
                'Operation'        => 'SearchItems',

                /**
                 * Fixed a bug that contextual widgets did not return outputs
                 * due to the form data having the value of `category` for the `unit_type` argument.
                 * This was because the unit option formatter class did not set the correct `unit_type` in the class,
                 * which has been fixed in 3.4.7.
                 *
                 * So setting the value here is a workaround to keep backward compatibility.
                 * @since       3.4.7
                 */
                'unit_type'        => 'search',

                // The `Power` parameter will not be used as it only works with the Books category.

                // 3.1.4+   By default the given comma-delimited multiple keywords such as `PHP,WordPress` are searched in one query.
                // The Amazon API does not provide an OR operator for the Keywords parameter. The `Power` argument cannot be used for categories other than Books.
                // So here we set a plugin specific option to perform search by each keyword.
                'search_per_keyword'    => true,

                // 3.6.0+ This avoids dabble nested containers.
                // '_no_outer_container'  => true,  // @todo figure out why this does not take effect.

            )
            + $_aArguments,
            false // echo or output
        );

    }

}