<?php
class AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Form extends AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Base {
    protected function _get() {
        $_sSpinnerURL = esc_url(admin_url('/images/wpspin_light-2x.gif'));
        return <<<CSSRULES
.amazon-auto-links-form-warning {
    font-weight: bold;
    color: red;
    font-size: 1.32em;
}
CSSRULES;
        
    }
}