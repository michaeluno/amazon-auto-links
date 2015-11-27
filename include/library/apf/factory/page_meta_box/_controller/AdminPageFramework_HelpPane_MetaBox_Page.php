<?php
class AmazonAutoLinks_AdminPageFramework_HelpPane_MetaBox_Page extends AmazonAutoLinks_AdminPageFramework_HelpPane_MetaBox {
    protected function _isInThePage() {
        if (!$this->oProp->bIsAdmin) {
            return false;
        }
        if (!isset($_GET['page'])) {
            return false;
        }
        if (!$this->oProp->isPageAdded($_GET['page'])) {
            return false;
        }
        if (!isset($_GET['tab'])) {
            return true;
        }
        return $this->oProp->isCurrentTab($_GET['tab']);
    }
}