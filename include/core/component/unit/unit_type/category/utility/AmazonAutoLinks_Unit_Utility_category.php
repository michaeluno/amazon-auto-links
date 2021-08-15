<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A class that provides utility methods for the category unit type component.
 * @since   4.2.0
 */
class AmazonAutoLinks_Unit_Utility_category extends AmazonAutoLinks_Unit_Utility {

    /**
     * @param string $sCategoryURL
     * @param string $sBreadcrumb
     * @param string $sInputName
     *
     * @return string
     */
    static public function getCategoryCheckbox( $sCategoryURL, $sBreadcrumb, $sInputName='added' ) {
        $_sName     = md5( $sCategoryURL );
        $sPageURL   = self::getLinkURLFormatted( $sCategoryURL );
        $_oEncrypt  = new AmazonAutoLinks_Encrypt;
        $_sSetURL   = $_oEncrypt->encode( $sCategoryURL );
        $_sSetBread = $_oEncrypt->encode( $sBreadcrumb );
        return "<label for='cb-{$_sName}'>"
                . "<input type='checkbox' name='{$sInputName}[{$_sName}][name]'        value='{$_sName}' i   id='cb-name-{$_sName}' />"
                . "<input type='hidden'   name='{$sInputName}[{$_sName}][breadcrumb]'  value='{$_sSetBread}' id='cb-breadcrumb-{$_sName}' />"
                . "<input type='hidden'   name='{$sInputName}[{$_sName}][page_url]'    value='{$_sSetURL}'   id='cb-page-url-{$_sName}' />"
                . "<a href='{$sPageURL}' data-url='" . esc_url( $sCategoryURL ) . "'>{$sBreadcrumb}</a>"
            . "</label>";
    }

    /**
     * @param string $sURL
     * @param array $aQueries
     *
     * @return string
     */
    static public function getLinkURLFormatted( $sURL, $aQueries=array() ) {
        $_oEncrypt = new AmazonAutoLinks_Encrypt;
        return add_query_arg(
            array(
                'href' => $_oEncrypt->encode( $sURL ),
            ) + $aQueries + self::getHTTPQueryGET()
            , admin_url( $GLOBALS[ 'pagenow' ] )
        );
    }

}
