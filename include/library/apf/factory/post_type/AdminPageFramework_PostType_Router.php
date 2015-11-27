<?php
abstract class AmazonAutoLinks_AdminPageFramework_PostType_Router extends AmazonAutoLinks_AdminPageFramework_Factory {
    public function _isInThePage() {
        if (!$this->oProp->bIsAdmin) {
            return false;
        }
        if ($this->oUtil->getElement($this->oProp->aPostTypeArgs, 'public', true) && $this->oProp->bIsAdminAjax) {
            return true;
        }
        if (!in_array($this->oProp->sPageNow, array('edit.php', 'edit-tags.php', 'post.php', 'post-new.php'))) {
            return false;
        }
        return ($this->oUtil->getCurrentPostType() == $this->oProp->sPostType);
    }
}