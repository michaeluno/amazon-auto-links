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
 * Simulates a captcha error.
 *
 * @since   4.3.4
 */
class AmazonAutoLinks_Test_Event_Query_CaptchaError {

    public function __construct() {
        if ( ! isset( $_GET[ 'aal_test' ] ) ) {
            return;
        }
        if ( 'captcha_error' !== $_GET[ 'aal_test' ] ) {
            return;
        }

        if ( isset( $_COOKIE[ 'second' ] ) ) {
            echo 'ERROR';
            // $this->___renderCaptchaError();
            exit;
        }
        echo 'OK';
        exit();

    }
        private function ___renderCaptchaError() {
            ?>
<body>
<head></head>
<h3>Simulating Captcha Page</h3>
<div class="a-container a-padding-double-large" style="min-width:350px;padding:44px 0 !important">

    <div class="a-row a-spacing-double-large" style="width: 350px; margin: 0 auto">

        <div class="a-row a-spacing-medium a-text-center"><i class="a-icon a-logo"></i></div>

        <div class="a-box a-alert a-alert-info a-spacing-base">
            <div class="a-box-inner">
                <i class="a-icon a-icon-alert"></i>
                <h4>Geben Sie die Zeichen unten ein</h4>
                <p class="a-last">Wir bitten um Ihr Verständnis und wollen uns sicher sein dass Sie kein Bot sind. Für beste Resultate, verwenden Sie bitte einen Browser der Cookies akzeptiert.</p>
                </div>
            </div>

            <div class="a-section">

                <div class="a-box a-color-offset-background">
                    <div class="a-box-inner a-padding-extra-large">

                        <form method="get" action="/errors/validateCaptcha" name="">
                            <input type="hidden" name="amzn" value="O+6yeRzbgZ9/MYpMg4QIcQ=="><input type="hidden" name="amzn-r" value="/">
                            <div class="a-row a-spacing-large">
                                <div class="a-box">
                                    <div class="a-box-inner">
                                        <h4>Geben Sie die angezeigten Zeichen im Bild ein:</h4>
                                        <!-- F&uuml;r automatischen Zugang zu Preis- und Angebots&auml;nderungen, wenden Sie sich bitte an unser MWS Subscription API:
                                             https://developer.amazonservices.de/gp/mws/api.html?ie=UTF8&section=subscriptions&group=subscriptions&version=latest
                                        -->
                                        <div class="a-row a-text-center">
                                            <img src="https://images-na.ssl-images-amazon.com/captcha/lqbiackd/Captcha_wbnpcgvgnd.jpg">
                                        </div>
                                        <div class="a-row a-spacing-base">
                                            <div class="a-row">
                                                <div class="a-column a-span6">
                                                    <label for="captchacharacters">Zeichen eingeben</label>
                                                </div>
                                                <div class="a-column a-span6 a-span-last a-text-right">
                                                    <a onclick="window.location.reload()">Anderes Bild probieren</a>
                                                </div>
                                            </div>
                                            <input autocomplete="off" spellcheck="false" id="captchacharacters" name="field-keywords" class="a-span12" autocapitalize="off" autocorrect="off" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="a-section a-spacing-extra-large">

                                <div class="a-row">
                                    <span class="a-button a-button-primary a-span12">
                                        <span class="a-button-inner">
                                            <button type="submit" class="a-button-text">Weiter shoppen</button>
                                        </span>
                                    </span>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>

        <div class="a-divider a-divider-section"><div class="a-divider-inner"></div></div>

        <div class="a-text-center a-spacing-small a-size-mini">
            <a href="https://www.amazon.de/gp/help/customer/display.html/ref=footer_cou/275-2496043-9483305?ie=UTF8&amp;nodeId=505048">Unsere AGB</a>
            <span class="a-letter-space"></span>
            <span class="a-letter-space"></span>
            <span class="a-letter-space"></span>
            <span class="a-letter-space"></span>
            <a href="https://www.amazon.de/gp/help/customer/display.html/ref=footer_privacy?ie=UTF8&amp;nodeId=3312401">Datenschutzerklärung</a>
        </div>

        <div class="a-text-center a-size-mini a-color-secondary">
          © 1996-2015, Amazon.com, Inc. oder Tochtergesellschaften
          <noscript>
            <img src="https://fls-eu.amazon.de/1/oc-csi/1/OP/requestId=0QVZXCJZRMH8X5K1N8SD&amp;js=0"></img>
          </noscript>
        </div>
    </div>
</body>
</html>
<?php

        }

}