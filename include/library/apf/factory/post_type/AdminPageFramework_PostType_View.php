<?php
abstract class AmazonAutoLinks_AdminPageFramework_PostType_View extends AmazonAutoLinks_AdminPageFramework_PostType_Model {
    function __construct($oProp) {
        parent::__construct($oProp);
        if ($this->_isInThePage()) {
            add_action('restrict_manage_posts', array($this, '_replyToAddAuthorTableFilter'));
            add_action('restrict_manage_posts', array($this, '_replyToAddTaxonomyTableFilter'));
            add_filter('parse_query', array($this, '_replyToGetTableFilterQueryForTaxonomies'));
            add_action('admin_head', array($this, '_replyToPrintStyle'));
        }
        if ($this->oProp->bIsAdmin) {
            add_action('admin_menu', array($this, '_replyToRemoveAddNewSidebarMenu'));
        }
        add_action('the_content', array($this, '_replyToFilterPostTypeContent'));
    }
    public function _replyToRemoveAddNewSidebarMenu() {
        if ($this->oUtil->getElement($this->oProp->aPostTypeArgs, 'show_submenu_add_new', true)) {
            return;
        }
        $_bsShowInMenu = $this->_getShowInMenuValue($this->oProp->aPostTypeArgs);
        $this->_removeAddNewSidebarSubMenu(is_string($_bsShowInMenu) ? $_bsShowInMenu : 'edit.php?post_type=' . $this->oProp->sPostType, $this->oProp->sPostType);
    }
    private function _getShowInMenuValue($aPostTypeArguments) {
        return $this->oUtil->getElement($aPostTypeArguments, 'show_in_menu', $this->oUtil->getElement($this->oProp->aPostTypeArgs, 'show_ui', $this->oUtil->getElement($this->oProp->aPostTypeArgs, 'public', false)));
    }
    private function _removeAddNewSidebarSubMenu($sMenuKey, $sPostTypeSlug) {
        if (!isset($GLOBALS['submenu'][$sMenuKey])) {
            return;
        }
        foreach ($GLOBALS['submenu'][$sMenuKey] as $_iIndex => $_aSubMenu) {
            if (!isset($_aSubMenu[2])) {
                continue;
            }
            if ('post-new.php?post_type=' . $sPostTypeSlug === $_aSubMenu[2]) {
                unset($GLOBALS['submenu'][$sMenuKey][$_iIndex]);
                continue;
            }
        }
    }
    public function _replyToAddAuthorTableFilter() {
        if (!$this->oProp->bEnableAuthorTableFileter) {
            return;
        }
        if (!(isset($_GET['post_type']) && post_type_exists($_GET['post_type']) && in_array(strtolower($_GET['post_type']), array($this->oProp->sPostType)))) {
            return;
        }
        wp_dropdown_users(array('show_option_all' => $this->oMsg->get('show_all_authors'), 'show_option_none' => false, 'name' => 'author', 'selected' => !empty($_GET['author']) ? $_GET['author'] : 0, 'include_selected' => false,));
    }
    public function _replyToAddTaxonomyTableFilter() {
        if ($GLOBALS['typenow'] != $this->oProp->sPostType) {
            return;
        }
        $_oPostCount = wp_count_posts($this->oProp->sPostType);
        if (0 == $_oPostCount->publish + $_oPostCount->future + $_oPostCount->draft + $_oPostCount->pending + $_oPostCount->private + $_oPostCount->trash) {
            return;
        }
        foreach (get_object_taxonomies($GLOBALS['typenow']) as $_sTaxonomySulg) {
            if (!in_array($_sTaxonomySulg, $this->oProp->aTaxonomyTableFilters)) {
                continue;
            }
            $_oTaxonomy = get_taxonomy($_sTaxonomySulg);
            if (0 == wp_count_terms($_oTaxonomy->name)) {
                continue;
            }
            wp_dropdown_categories(array('show_option_all' => $this->oMsg->get('show_all') . ' ' . $_oTaxonomy->label, 'taxonomy' => $_sTaxonomySulg, 'name' => $_oTaxonomy->name, 'orderby' => 'name', 'selected' => intval(isset($_GET[$_sTaxonomySulg])), 'hierarchical' => $_oTaxonomy->hierarchical, 'show_count' => true, 'hide_empty' => false, 'hide_if_empty' => false, 'echo' => true,));
        }
    }
    public function _replyToGetTableFilterQueryForTaxonomies($oQuery = null) {
        if ('edit.php' != $this->oProp->sPageNow) {
            return $oQuery;
        }
        if (!isset($GLOBALS['typenow'])) {
            return $oQuery;
        }
        foreach (get_object_taxonomies($GLOBALS['typenow']) as $sTaxonomySlug) {
            if (!in_array($sTaxonomySlug, $this->oProp->aTaxonomyTableFilters)) {
                continue;
            }
            $sVar = & $oQuery->query_vars[$sTaxonomySlug];
            if (!isset($sVar)) {
                continue;
            }
            $oTerm = get_term_by('id', $sVar, $sTaxonomySlug);
            if (is_object($oTerm)) {
                $sVar = $oTerm->slug;
            }
        }
        return $oQuery;
    }
    public function _replyToPrintStyle() {
        if ($this->oUtil->getCurrentPostType() !== $this->oProp->sPostType) {
            return;
        }
        if (isset($this->oProp->aPostTypeArgs['screen_icon']) && $this->oProp->aPostTypeArgs['screen_icon']) {
            $this->oProp->sStyle.= $this->_getStylesForPostTypeScreenIcon($this->oProp->aPostTypeArgs['screen_icon']);
        }
        $this->oProp->sStyle = $this->oUtil->addAndApplyFilters($this, "style_{$this->oProp->sClassName}", $this->oProp->sStyle);
        if (!empty($this->oProp->sStyle)) {
            echo "<style type='text/css' id='amazon-auto-links-style-post-type'>" . $this->oProp->sStyle . "</style>";
        }
    }
    private function _getStylesForPostTypeScreenIcon($sSRC) {
        $sNone = 'none';
        $sSRC = $this->oUtil->getResolvedSRC($sSRC);
        return <<<CSSRULES
#post-body-content {
    margin-bottom: 10px;
}
#edit-slug-box {
    display: {$sNone};
}
#icon-edit.icon32.icon32-posts-{$this->oProp->sPostType} {
    background: url('{$sSRC}') no-repeat;
    background-size: 32px 32px;
}     
CSSRULES;
        
    }
    public function content($sContent) {
        return $sContent;
    }
    public function _replyToFilterPostTypeContent($sContent) {
        if (!is_singular()) {
            return $sContent;
        }
        if (!is_main_query()) {
            return $sContent;
        }
        global $post;
        if ($this->oProp->sPostType !== $post->post_type) {
            return $sContent;
        }
        return $this->oUtil->addAndApplyFilters($this, "content_{$this->oProp->sClassName}", $this->content($sContent));
    }
}