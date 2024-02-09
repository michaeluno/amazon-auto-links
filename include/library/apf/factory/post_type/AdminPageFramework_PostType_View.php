<?php
/*
 * Admin Page Framework v3.9.2b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2023, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_PostType_View extends AmazonAutoLinks_AdminPageFramework_PostType_Model {
    public function __construct($oProp)
    {
        parent::__construct($oProp);
        if ($this->oProp->bIsAdmin) {
            add_action('load_' . $this->oProp->sPostType, array( $this, '_replyToSetUpHooksForView' ));
            add_action('admin_menu', array( $this, '_replyToRemoveAddNewSidebarMenu' ));
        }
        add_action('the_content', array( $this, '_replyToFilterPostTypeContent' ));
    }
    public function _replyToSetUpHooksForView()
    {
        add_action('restrict_manage_posts', array( $this, '_replyToAddAuthorTableFilter' ));
        add_action('restrict_manage_posts', array( $this, '_replyToAddTaxonomyTableFilter' ));
        add_filter('parse_query', array( $this, '_replyToGetTableFilterQueryForTaxonomies' ));
        add_filter('post_row_actions', array( $this, '_replyToModifyActionLinks' ), 10, 2);
        add_action('admin_head', array( $this, '_replyToPrintStyle' ));
    }
    public function _replyToRemoveAddNewSidebarMenu()
    {
        if ($this->oUtil->getElement($this->oProp->aPostTypeArgs, 'show_submenu_add_new', true)) {
            return;
        }
        $this->_removeAddNewSidebarSubMenu($this->oUtil->getPostTypeSubMenuSlug($this->oProp->sPostType, $this->oProp->aPostTypeArgs), $this->oProp->sPostType);
    }
    private function _removeAddNewSidebarSubMenu($sMenuKey, $sPostTypeSlug)
    {
        if (! isset($GLOBALS[ 'submenu' ][ $sMenuKey ])) {
            return;
        }
        foreach ($GLOBALS[ 'submenu' ][ $sMenuKey ] as $_iIndex => $_aSubMenu) {
            if (! isset($_aSubMenu[ 2 ])) {
                continue;
            }
            if ('post-new.php?post_type=' . $sPostTypeSlug === $_aSubMenu[ 2 ]) {
                $this->oUtil->unsetDimensionalArrayElement($GLOBALS[ 'submenu' ], array( $sMenuKey, $_iIndex ));
                break;
            }
        }
    }
    public function _replyToModifyActionLinks($aActionLinks, $oPost)
    {
        if ($oPost->post_type !== $this->oProp->sPostType) {
            return $aActionLinks;
        }
        return $this->oUtil->addAndApplyFilters($this, "action_links_{$this->oProp->sPostType}", $aActionLinks, $oPost);
    }
    public function _replyToAddAuthorTableFilter()
    {
        if (! $this->oProp->bEnableAuthorTableFileter) {
            return;
        }
        if (! (isset($_GET[ 'post_type' ]) && post_type_exists($_GET[ 'post_type' ]) && strtolower($_GET[ 'post_type' ]) == $this->oProp->sPostType)) {
            return;
        }
        wp_dropdown_users(array( 'show_option_all' => $this->oMsg->get('show_all_authors'), 'show_option_none' => false, 'name' => 'author', 'selected' => $this->oUtil->getHTTPQueryGET('author', 0), 'include_selected' => false, ));
    }
    public function _replyToAddTaxonomyTableFilter()
    {
        if ($GLOBALS[ 'typenow' ] != $this->oProp->sPostType) {
            return;
        }
        $_oPostCount = wp_count_posts($this->oProp->sPostType);
        if (0 == $_oPostCount->publish + $_oPostCount->future + $_oPostCount->draft + $_oPostCount->pending + $_oPostCount->private + $_oPostCount->trash) {
            return;
        }
        foreach (get_object_taxonomies($GLOBALS[ 'typenow' ]) as $_sTaxonomySulg) {
            if (! in_array($_sTaxonomySulg, $this->oProp->aTaxonomyTableFilters)) {
                continue;
            }
            $_oTaxonomy = get_taxonomy($_sTaxonomySulg);
            if (0 == wp_count_terms($_oTaxonomy->name)) {
                continue;
            }
            wp_dropdown_categories(array( 'show_option_all' => $this->oMsg->get('show_all') . ' ' . $_oTaxonomy->label, 'taxonomy' => $_sTaxonomySulg, 'name' => $_oTaxonomy->name, 'orderby' => 'name', 'selected' => intval(isset($_GET[ $_sTaxonomySulg ])), 'hierarchical' => $_oTaxonomy->hierarchical, 'show_count' => true, 'hide_empty' => false, 'hide_if_empty' => false, 'echo' => true, ));
        }
    }
    public function _replyToGetTableFilterQueryForTaxonomies($oQuery=null)
    {
        if ('edit.php' != $this->oProp->sPageNow) {
            return $oQuery;
        }
        if (! isset($GLOBALS[ 'typenow' ])) {
            return $oQuery;
        }
        foreach (get_object_taxonomies($GLOBALS[ 'typenow' ]) as $sTaxonomySlug) {
            if (! in_array($sTaxonomySlug, $this->oProp->aTaxonomyTableFilters)) {
                continue;
            }
            $sVar = &$oQuery->query_vars[ $sTaxonomySlug ];
            if (! isset($sVar)) {
                continue;
            }
            $oTerm = get_term_by('id', $sVar, $sTaxonomySlug);
            if (is_object($oTerm)) {
                $sVar = $oTerm->slug;
            }
        }
        return $oQuery;
    }
    public function _replyToPrintStyle()
    {
        if ($this->oUtil->getCurrentPostType() !== $this->oProp->sPostType) {
            return;
        }
        if (isset($this->oProp->aPostTypeArgs[ 'screen_icon' ]) && $this->oProp->aPostTypeArgs[ 'screen_icon' ]) {
            $this->oProp->sStyle .= $this->_getStylesForPostTypeScreenIcon($this->oProp->aPostTypeArgs[ 'screen_icon' ]);
        }
        $_sStyle = $this->oUtil->isDebugMode() ? $this->oProp->sStyle : $this->oUtil->getCSSMinified($this->oProp->sStyle);
        $_sStyle = trim($_sStyle);
        if (! empty($_sStyle)) {
            echo "<style type='text/css' id='amazon-auto-links-style-post-type'>" . $this->oProp->sStyle . "</style>";
        }
        $this->oProp->sStyle = '';
    }
    private function _getStylesForPostTypeScreenIcon($sSRC)
    {
        $sNone = 'none';
        $sSRC = esc_url($this->oUtil->getResolvedSRC($sSRC));
        return <<<CSSRULES
#post-body-content{margin-bottom:10px}#edit-slug-box{display:{$sNone}}#icon-edit.icon32.icon32-posts-{$this->oProp->sPostType}{background:url({$sSRC}) no-repeat;background-size:32px 32px}
CSSRULES;
    }
    public function content($sContent)
    {
        return $sContent;
    }
    public function _replyToFilterPostTypeContent($sContent)
    {
        if (! is_singular()) {
            return $sContent;
        }
        if (! is_main_query()) {
            return $sContent;
        }
        global $post;
        if ($this->oProp->sPostType !== $post->post_type) {
            return $sContent;
        }
        return $this->oUtil->addAndApplyFilters($this, "content_{$this->oProp->sClassName}", $this->content($sContent));
    }
}
