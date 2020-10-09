<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * The Saudi locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_SA extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'SA';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown.
     */
    public $sLocaleNumber = '01';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.sa';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.sa';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAMCAYAAABr5z2BAAAB20lEQVQ4T52Su2sUURyFv3ncnZ3JJhuJ2CwqChHBR2zNYGGrYDoxpZBGEBRF9E/QvyKkijZqMFhYanZTqtiJVokpFJPM7Lx2nt47xN1Fu8zwm5l7YT7OPedoLLEBuHIOc/U0CajcRZeqqiiLgiLLKctqBJP7aGqpoesahjDRTUOuNLqrXfk8ABR5wSCMMVODJI1piRZJnuAIR/2Kn3pM2W1CI8KasCVEp7fa+wuYJxtkhHs+y1dX2Al2mD1yhpUvy6RFyo3ZBV5/fYl7/ArPPj5FnzRp2Ba95weA+UUJiFMyL+Hh3CNunr3F2+/rnDt6nh/BNlv+FhePzdWz9O42n+LPtYrNF5sjBXmaE3p9Hl94wm70u/ZEGILLHZe1b6+4c+kuv6KfPNi4z665j+UoBUOASyENTPohzcQiTiLKqiTKIoQQFFrJTGOGIOuDreNMtzAbYuSBOoJyP/JCYj+U3xkqB2W+bhhydEx526ZNauU4bQmw/gHkSUoR5KxdW6fT6gxj3E/2+LD9nhNTJznVPs3Cm+sEzfh/E4tMxRgxiAZ1H4YSaiUa7cZ0HalXehgtgWg2xmMcK5LsQzVepFpLJU1Vr6oukanKJI/VVT1o3tP8JKwmD9Pj5oTW/wPm/up+A5UZTQAAAABJRU5ErkJggg==';

    /**
     * @return string
     * @remark Override it.
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Saudi Arabia', 'amazon-auto-links' );
    }

}