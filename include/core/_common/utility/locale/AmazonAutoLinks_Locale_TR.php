<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * The Turkish locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_TR extends AmazonAutoLinks_Locale_Base {


    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'TR';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown. Same as U.S.
     */
    public $sLocaleNumber = '41';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.com.tr';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://gelirortakligi.amazon.com.tr';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAADPklEQVRIS8VWTSxjURT+XrVT1F8HM0ZKMkgkiMTCRiLEUjIWNjYkmEwyQSIssJIICSsjsSAZs2DFEgssRCIRFjZCYiExxlSp/7+Wtk/fnHPrlZmJ1x8JJznNu+/de7/vnvOd0yuNAJ8g4TuA9+QvaXYo+CKNSDh4BXD1oHYmoPDos/h9Ofsh+bBCJuC552h4JteQCLgI7Iz86h/QWBqbyY1hkAmawCFtfqECSBIMGRmAokDe2YHi9Yov8eTvQiQRFAE/OAEnNDbC3N4OvcUioGSrFae9vbgYGhKEQiURkMA1geyzSPR6pExMIKay0n9Gz/Y25N1dSNHRcK+v47ChAYrbHRKJgAR+E9wteXJ/PxJaWgQ4Ax9UV+N2efkh4BQdyUCSpHQosiyaCWvjXuRPJkaTgHp6Y0EB0ldXAZ1ObL6bnw/35ia4AtRqYARjXh4SWlthr6/3AwZKiSaBI9rmnIU1OIj4piaxqWN6GraKCkTTs5NPSKQkkwne62tYFhYQVVwMa0kJ3Bsb8DqdIiVZGpHQJLB3D2KZn0dUWZkgcNLZidPubiTyM79gYTY3I6mvD5LxoRAdU1MiTd6rK2TSNN0TSdAkYKVFN+RpS0uILCoSWxxRiM+/fUMyPbvJ1dI0UVRSJyfFHOfsLPbKy0VVxNFY689Fk4CdFl+Sp4yNIbamRmx+OToKe22taDpMggV6TG7u6EBiV5dPG5mZ+JWdDdlmw0f6pn/i9PxakwCfjntAbFUVUsbHxTaKywVraSluV1bEmIlwh4yrq8MNpcpDZRlZWIiIpCQ4ZmaQSt9M4RKQaeFPcu4B6WtreJOT4yNB4jrt6cHF8DDuzrg5P5hKiN9wlaSTP5X/gBHgCWoX5BKzLC5CZ+au7zMuSW5Anq0t3J2cCHHK+/tIo2935FEBwIMiwJNUMRqyspA8MAATC4zUL4waj3NuDsdtbXBR6cXQqw9/xUR7ELAT8nJOBQuS655Nn5YGY24uiVyBm1IjH/Bdxndi3z9E8BYUAXU7zraDnEvzsTEwN6a3weP6Z4ZEQF3FEeEewMaiiwgDWF0SFoFn4P239DGB17yU2iS+lpOwR+hOGuql5rkBsdF1+OsfSE5HMaBO96YAAAAASUVORK5CYII=';

    /**
     * Whether the marketplace site requires visitors to accept cookies
     * @var boolean
     * @sicne 5.3.9
     */
    public $bPrefForm = true;

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Turkey', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Turkey', 'amazon-auto-links' );
    }

}