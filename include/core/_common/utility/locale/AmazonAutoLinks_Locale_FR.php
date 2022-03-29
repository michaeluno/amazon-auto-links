<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * The French locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_FR extends AmazonAutoLinks_Locale_EuropeanUnion {

    /**
     * The locale code.
     * @var string
     */
    public $sSlug = 'FR';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '08';

    /**
     * @var string
     */
    public $sDomain = 'www.amazon.fr';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://partenaires.amazon.fr';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAjUlEQVRIS2N01pktzszMvIeBgUEHiHECm6T/+KQx5JQrc/GqZ2RguMLAyOjC6KY/7zIhy0EmUdsBIDNBjgA5AOw1BeEQvC7u38ZJUgh8UFXCq/7A27dg+VEHjIbAaAiMhsBoCIyGwGgIjIbAaAgMihAYsEYpsEl4nhHULGdiZt4HbKFq0bNZDrackdETAIE6sTNLga3zAAAAAElFTkSuQmCC';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'France', 'amazon-auto-links' );
    }

    /**
     * @return string
     * @remark Override it.
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'France', 'amazon-auto-links' );
    }

    /**
     * @remark The supported locales: US, CA, FR, DE, UK, JP.
     * @see    https://www.assoc-amazon.com/s/impression-counter-common.js
     * @see    https://ir-na.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=8
     * @return string
     */
    protected function _getImpressionCounterScript() {
        return <<<SCRIPT
var amazon_impression_url   = "www.assoc-amazon.fr";
var amazon_impression_campaign = '2522';
var amazon_impression_ccmids =  {
    'as2'  : '8066',
    '-as2' : '9498',
    'am2'  : '9482',
    '-am2' : '9498',
    'ur2'  : '9498'
    };
document.write("<scr"+"ipt src='https://" 
    + amazon_impression_url 
    + "/s/impression-counter-common.js' type='text/javascr"+"ipt'></scr"+"ipt>");             
SCRIPT;
    }

}