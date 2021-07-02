<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Generates a prime SVG icon.
 * @since 4.6.0
 */
class AmazonAutoLinks_SVGGenerator_PrimeIcon extends AmazonAutoLinks_SVGGenerator_Base {

    /**
     * @return string
     * @since  4.6.0
     */
    public function get() {

        if ( ! $this->bUseCache ) {
            return $this->_getDefinition( true );
        }

        $_sTitle = $this->sTitle ? "<title>" . esc_html( $this->sTitle ) . "</title>" : '';
        return "<svg height='20px' viewBox='0 0 64 20' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:svg='http://www.w3.org/2000/svg' preserveAspectRatio='xMinYMin meet'>"
                . $_sTitle
                . "<use xlink:href='#svg-prime-icon' />"
                . "<image src='" . esc_url( $this->sSRCFallbackImage ) . "' />" // fallback for browsers not supporting SVG
            . "</svg>";

    }

    /**
     * @return string  Invisible SVG definition element.
     * @since  4.6.1
     */
    protected function _getDefinition( $bVisible=false ) {
        $_sTitle   = $this->sTitle ? "<title>" . esc_html( $this->sTitle ) . "</title>" : '';
        $_sStyle   = $bVisible ? '' : " style='position: absolute; width: 0; height: 0; overflow: hidden;'";
        return "<svg{$_sStyle} height='20px' viewBox='0 0 64 20' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:svg='http://www.w3.org/2000/svg' preserveAspectRatio='xMinYMin meet'>"    // xMinYMin meet to align left
                . $_sTitle
                . "<g id='svg-prime-icon' transform='translate(-1.4046423,-0.11313718)'>"
                    . "<g fill='#fd9a18' transform='matrix(1.1343643,0,0,0.86753814,-0.1955262,0.39080443)'>"
                        . "<path d='M 2.4895689,12.174275 Q 2.3072772,11.982868 2.2070168,11.84615 2.115871,11.709431 2.115871,11.581827 q 0,-0.164063 0.1549479,-0.328125 0.1640625,-0.164063 0.4101563,-0.291667 0.2552083,-0.127604 0.5468749,-0.200521 0.3007813,-0.08203 0.5742188,-0.08203 0.2916666,0 0.4739583,0.08203 0.1914062,0.07292 0.3645833,0.255209 0.7018229,0.738281 1.266927,1.51302 0.5742188,0.765625 1.1210938,1.585938 Q 7.5572771,12.70292 8.1223812,11.39042 8.6874854,10.068806 9.3163916,8.8109935 9.9544124,7.5440664 10.674464,6.3227123 q 0.729167,-1.2304687 1.576823,-2.470052 0.309896,-0.4648437 0.911459,-0.6835937 0.610677,-0.21875 1.558593,-0.21875 0.282552,0 0.455729,0.082031 0.173178,0.082031 0.173178,0.21875 0,0.1184896 -0.05469,0.2369792 -0.05469,0.1184896 -0.164062,0.2734375 -2.069011,2.8893228 -3.764323,6.0520831 -1.6953127,3.1627606 -2.9440105,6.6718746 -0.082031,0.255209 -0.4010417,0.382813 -0.3190104,0.127604 -0.938802,0.127604 -0.2916667,0 -0.4921875,-0.01823 Q 6.3906104,16.959431 6.2538917,16.904743 6.1171729,16.85917 6.0260271,16.786254 5.9348813,16.713337 5.8710792,16.603962 5.4791521,15.965941 5.1054542,15.419066 4.7408709,14.863077 4.3398293,14.343545 3.9479022,13.824014 3.492173,13.295368 3.0455585,12.766722 2.4895689,12.174275 Z' />"
                    . "</g>"
                    . "<g fill='#2492c1'>"
                        . "<path d='m 21.362997,12.694364 h -0.03385 v 4.99348 H 18.654669 V 5.034874 h 2.674474 v 1.3033828 h 0.03385 q 0.990233,-1.514971 2.7845,-1.514971 1.684242,0 2.598302,1.159503 0.922525,1.1510394 0.922525,3.1399678 0,2.1666624 -1.074868,3.4785094 -1.066404,1.311846 -2.843745,1.311846 -1.565752,0 -2.386714,-1.218748 z m -0.07617,-3.5546803 v 0.6940091 q 0,0.8971332 0.473957,1.4641902 0.473957,0.567056 1.244138,0.567056 0.914061,0 1.413409,-0.702473 0.507811,-0.710936 0.507811,-2.0058553 0,-2.2851518 -1.77734,-2.2851518 -0.820962,0 -1.3457,0.6263009 -0.516275,0.6178373 -0.516275,1.6419239 z' />"
                        . "<path d='M 35.056981,7.4469786 Q 34.57456,7.1846094 33.931332,7.1846094 q -0.871743,0 -1.362627,0.6432279 Q 32.07782,8.4626017 32.07782,9.56286 v 4.138664 h -2.674474 v -8.66665 h 2.674474 v 1.6080698 h 0.03385 q 0.634765,-1.7604133 2.285152,-1.7604133 0.423177,0 0.660155,0.1015623 z' />"
                        . "<path d='m 37.655283,3.6637829 q -0.677082,0 -1.108722,-0.3977857 -0.431639,-0.4062492 -0.431639,-0.9902325 0,-0.6009102 0.431639,-0.9817689 0.43164,-0.38085862 1.108722,-0.38085862 0.685546,0 1.108722,0.38085862 0.43164,0.3808587 0.43164,0.9817689 0,0.6093739 -0.43164,0.998696 -0.423176,0.3893222 -1.108722,0.3893222 z m 1.32031,10.0377411 h -2.674474 v -8.66665 h 2.674474 z' />"
                        . "<path d='M 54.988583,13.701524 H 52.322572 V 8.7588251 q 0,-1.8873662 -1.388018,-1.8873662 -0.660155,0 -1.074867,0.5670562 -0.414713,0.5670562 -0.414713,1.4134087 V 13.701524 H 46.7705 V 8.7080439 q 0,-1.836585 -1.362628,-1.836585 -0.685545,0 -1.100258,0.5416656 -0.406249,0.5416657 -0.406249,1.4726534 v 4.8157461 h -2.674474 v -8.66665 h 2.674474 v 1.354164 h 0.03385 q 0.414713,-0.6940091 1.159503,-1.1256489 0.753254,-0.4401033 1.641924,-0.4401033 1.836585,0 2.513667,1.6165333 0.990232,-1.6165333 2.911452,-1.6165333 2.826818,0 2.826818,3.4869724 z' />"
                        . "<path d='m 64.890907,10.129916 h -5.653635 q 0.135417,1.887366 2.378251,1.887366 1.430336,0 2.513667,-0.677082 v 1.929684 q -1.201821,0.643228 -3.123041,0.643228 -2.098954,0 -3.258457,-1.159503 -1.159503,-1.167967 -1.159503,-3.2499937 0,-2.158199 1.252602,-3.4192642 1.252601,-1.2610653 3.080723,-1.2610653 1.895829,0 2.92838,1.1256489 1.041013,1.1256488 1.041013,3.0553326 z M 62.411094,8.4879922 q 0,-1.8619755 -1.506507,-1.8619755 -0.643228,0 -1.117186,0.5332021 -0.465493,0.5332021 -0.567056,1.3287734 z' />"
                    . "</g>"
                . "</g>"
               . "<image src='" . esc_url( $this->sSRCFallbackImage ) . "' />" // fallback for browsers not supporting SVG
            . "</svg>";
    }

}