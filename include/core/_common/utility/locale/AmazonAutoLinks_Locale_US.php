<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The U.S. locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_US extends AmazonAutoLinks_Locale_NorthAmerica {

    /**
     * The locale code.
     * @var string
     */
    public $sSlug = 'US';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '01';

    /**
     * @var string
     */
    public $sDomain = 'www.amazon.com';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.com/';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAWCAYAAAChWZ5EAAABSklEQVRIS2PMFlaJ3SwbPO8dqwgLAwgwM4Ephr//IDQRfCYONobqI7kQ9cSDl///M6QyyhmU/37LKAixnEzAyM7KUHummBzdLxl5TLv////zl2HnvBQGRkYGhpiGC2CDFtcbEM1nZmNhODXXgiQHTJm3FKwe7oBd84EOAApEQx2wpAHoACL5zKxAB8wj1wEWvf////zNICZvQrLPYV5m5WZn2HYqkqQQWHv/CTQE0BxAis9hNrIBHbCVbAeMpoHRNDCaBtDSAEmZGaqYonKAz2Hy/38/fjFIyBkzMPxnYGBiYwaXgH9//yWaz87LwbB+byBJbocXRE0T5/5nZ2djSI0OJckAShXD64IBd0AnCyjgGRiCFWUo9RRJ+uFRMOAOGPAoGHAHAKPgBTDyxEmKQOopfsbYwczgC2yKzQGaKUY9c4ky6RmwUZoBAO2BIfPVOVScAAAAAElFTkSuQmCC';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'United States', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'United States', 'amazon-auto-links' );
    }

    /**
     * @remark The supported locales: US, CA, FR, DE, UK, JP.
     * @see https://www.assoc-amazon.com/s/impression-counter-common.js
     * @return string
     */
    protected function _getImpressionCounterScript() {
        return <<<SCRIPT
var amazon_impression_url      = "www.assoc-amazon.com";
var amazon_impression_campaign = '211189';
var amazon_impression_ccmids   =  {
    'as2'  : '374929',
    '-as2' : '9325',
    'am2'  : '374925',
    '-am2' : '9325',
    'ur2'  : '9325'
    };
document.write("<scr"+"ipt src='https://" 
    + amazon_impression_url 
    + "/s/impression-counter-common.js' type='text/javascript'></scr"+"ipt>");             
SCRIPT;
    }

}