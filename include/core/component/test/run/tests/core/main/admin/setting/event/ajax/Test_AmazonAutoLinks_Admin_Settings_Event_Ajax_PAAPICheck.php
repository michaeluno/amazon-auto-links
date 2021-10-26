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
 * Tests the class, `AmazonAutoLinks_Admin_Settings_Event_Ajax_PAAPICheck`.
 *
 * @package Auto Amazon Links
 * @since   4.6.18
 * @see     AmazonAutoLinks_Admin_Settings_Event_Ajax_PAAPICheck
 * @tags    http
*/
class Test_AmazonAutoLinks_Admin_Settings_Event_Ajax_PAAPICheck extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags sanitize
     */
    public function test_sanitize_text_field() {

        $_aFieldValues = array(
            'AKIAJLZ2AUPIF25UBPHZ',
            'AKIAJ37G90AFKWW6RUWZ',
            'AKIAI2RPC1DBDTYNNO2A',
            'AKIAJNGV3FS2TEIWMUZQ',
            'AKIAJPCRMN46WIBCD7AD',
            'AKIAIZBDAMTFN7YVB46A',
            'DKIAJTEISB8JLEXAAEFA',
            'BBBAJA5YSA4CU75XZAAA',
            'AKIBI7DD6ZMJJMLM36RG',
            'AKIAJVIHHRFWGFINXJIA',
            'BKIAJONMHG3KTOAWXAMB',
            'CKIAJPS5URK78AC9KDDA',
            'aigk5j6Rp7p6oFLXM70z6hlgXMx0aKWyJT7KVhiZ',
            'STYpT10ieJlH18ishQ8/L9QyYG1Rd4iNdi6jNsbm',
            '5wdfZIDc1UMBdcm/IWl1wU2WAR5u0Tw32ZbdoHuz',
            'm03/2sroAfOXdL8XAIU3Zxvtn5WS81N5zhXzIZg0',
            'm03/2sroAfOXdL8XAIU3Zxvtn5WS81N5zhXzIZg0',
            '4ZK+JvTAHWFWJa/xGpnRH6zQFhIMbDWVYt0PpE5c',
            '1Sf7eSF1Dnpns6+LBeP6qv3AxBFnY16hlKIn7dpn',
            'C0GFirCK7kPvgWTTj6IL9JfUHWWbIEVkgR1E6tO9',
            'Ot+oK7IVthbTZYEAr0OAXy+2sigQQjZ16Ypo/trR',
            'bn1QekLSg219pFFN0AD95VAA7UToxg5c1cYYXhAm',
            '9/wdNyOnvBK1aKEuMVrCZJ8yGBGsgvIohOH42Fwr',
            'oyfyxgAnzC2wZq97BxXE/QLO+FKTei6OIb/WNnef',
            'SdggQAwpGkp08y2Vp1rXv5IrofVh9nJNhOV1/VI9',
            'ileankator-20',
            'uza168-20',
            'azsifordres-20',
            'hyamicuo-21',
            'hshopgogo0d-21',
            'ddsitefind02-21',
            'spocoolatest0d-21',
            'hdahistoirespour-21',
        );
        foreach( $_aFieldValues as $_sValue ) {
            $this->_assertEqual( $_sValue, sanitize_text_field( $_sValue ), 'The sanitize_text_field() should not break the input: ' . $_sValue );
        }

    }

}