<?php
class AmazonAutoLinks_AdminPageFramework_Property_UserMeta extends AmazonAutoLinks_AdminPageFramework_Property_MetaBox {
    public $_sPropertyType = 'user_meta';
    public $_sFormRegistrationHook = 'admin_enqueue_scripts';
    protected function _getOptions() {
        return array();
    }
}