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
 * A class for inserting credit output at the bottom of each unit output.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput__Credit extends AmazonAutoLinks_UnitOutput__DelegationBase {

    /**
     * @return array
     */
    protected function _getFilterArguments() {
        return array(
            array(
                'aal_filter_unit_output',
                array( $this, '_replyToInsertOutput' ),
                100,  // priority - set low as it should be inserted last
                5    // 5 parameters
            ),
        );
    }

    /**
     * @param       string      $sContent
     * @param       array       $aUnitOptions
     * @param       string      $sTemplatePath
     * @param       array       $aPluginOptions
     * @param       array       $aProducts
     * @return      string
     */
    public function _replyToInsertOutput( $sContent, $aUnitOptions, $sTemplatePath, $aPluginOptions, $aProducts ) {
        return $sContent . $this->___getCredit();
    }
        /**
         * @return      string
         */
        private function ___getCredit() {

            $_sHTMLComment = apply_filters( 'aal_filter_credit_comment', '' );
            if ( ! $this->_oUnitOutput->oUnitOption->get( 'credit_link' ) ) {
                return $_sHTMLComment;
            }
            $_iCreditType = ( integer ) $this->_oUnitOutput->oUnitOption->get(
                array( 'credit_link_type' ),
                0   // default
            );
            return apply_filters(
                'aal_filter_credit_link_' . $_iCreditType,
                $_sHTMLComment,
                $this->_oUnitOutput->oOption
            );

        }

}