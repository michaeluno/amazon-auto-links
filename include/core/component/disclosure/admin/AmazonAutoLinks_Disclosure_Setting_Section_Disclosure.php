<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Adds a form section to an in-page tab.
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_Disclosure_Setting_Section_Disclosure extends AmazonAutoLinks_AdminPage_Section_Base {

    protected function _getArguments() {
        return array(
            'section_id'    => 'disclosure',
            'tab_slug'      => $this->sTabSlug,
            'title'         => __( 'Affiliate Disclosure', 'amazon-auto-links' ),
            'description'   => array(
                 __( 'Identify yourself as an associate.', 'amazon-auto-links' ),
            ),
            'class' => 'width-full',
        );
    }

    protected function _construct( $oFactory ) {
        add_filter(
            'field_definition_' . $oFactory->oProp->sClassName . '_' . $this->sSectionID . '_page',
            array( $this, 'replyToGetFieldDefinition_page' )
        );
    }

    /**
     * Adds form fields.
     * @since       4.7.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {
        
        /**
         * @var array
         * ``` 
         * Array(
         *    [ID] => (string, length: 3) 110
         *    [post_title] => (string, length: 20) Affiliate Disclosure
         * )
         * ```
         */
        $_aDisclosurePage   = AmazonAutoLinks_Disclosure_Utility::getPostByGUID( AmazonAutoLinks_Disclosure_Loader::$sDisclosureGUID, 'ID,post_title' );
        $_iPageID = $this->getElement( $_aDisclosurePage, 'ID' );
        /**
         * @var array
         * ```
         * array(
         *     [value] => (string, length: 3) 107
         *     [encoded] => (string, length: 44) [{"id":"107","text":"Affiliate Disclosure"}]
         * )
         * ```
         */
        $_anPageFieldDefault = $_iPageID 
            ? array(
                'value'   => $_iPageID,
                'encoded' => json_encode( array( array(
                    'id'    => $_iPageID,
                    'text'  => $this->getElement( $_aDisclosurePage, 'post_title' ),
                ) ) ),
            )
            : null;

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => 'page',
                'title'             => __( 'Disclosure Page', 'amazon-auto-links' ),
                'type'              => 'select2',
                'label'             => array(),
                // 'description'       => '<span class="dashicons dashicons-welcome-write-blog"></span><a href="">' . __( 'Edit' ) . '</a>',   // @todo set the url
                'tip'       => array(
                    __( 'The following shortcodes are available in the page.', 'amazon-auto-links' ),
                    '<ul>'
                        . '<li><code>[aal_associates]</code> - ' . __( 'Lists Amazon Associates sites that you are enrolled in as an affiliate.', 'amazon-auto-links' ) . '</li>'
                    . '</ul>',
                ),
                'options'         => array(
                    'minimumInputLength' => 2,
                    'width'              => '50%',
                ),
                'callback'        => array(
                    'search'    => 'AmazonAutoLinks_Disclosure_Utility::getPages',
                ),
                'default'   => $_anPageFieldDefault,
            ),
            array(
                'field_id'          => 'create_page',
                'type'              => 'submit',
                'attributes'        => array(
                    'class' => 'button button-secondary',
                ),
                'save'              => false,
                'value'             => __( 'Create', 'amazon-auto-links' ),
                'if'                => ! $_iPageID,
                'description'       => __( 'An affiliate disclosure page is missing on your site. Create one.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'          => 'never_create_page',
                'type'              => 'checkbox',
                'title'             => __( 'No Need', 'amazon-auto-links' ),
                'label'             => __( 'Never automatically create a disclosure page.', 'amazon-auto-links' ),
                'tip'               => __( 'By default, the plugin attempts to create an affiliate disclosure page when it does not exist upon plugin activation and updates.', 'amazon-auto-links' )
                    . ' ' . __( 'Check this option only if your locale does not require affiliate disclosure statements.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'          => 'disclosure_text',
                'title'             => __( 'Disclosure Text', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'rich'              => true,
                'class'             => 'width-100',
                'description'       => array(
                    __( 'The following tags are available.', 'amazon-auto-links' ),
                    "<ul>"
                        . "<li><code>%site_host%</code> - " . __( 'Your site host name.', 'amazon-auto-links' ) .  ' <code>' . $this->getSubDomain( site_url() ) . "</code></li>"
                        . "<li><code>%associates_list%</code> - " . __( 'A list of Amazon Associates in which you are participating, which will be automatically generated by parsing your set Associates IDs in the Associates section.', 'amazon-auto-links' ) . "</li>"
                    . "</ul>"
                ),
            ),
            array(
                'field_id'          => 'link_disclaimer_to_page',
                'title'             => __( 'Disclaimer Link', 'amazon-auto-links' ),
                'type'              => 'checkbox',
                'label'             => __( 'Link the "more info" disclaimer link to the affiliate disclosure page.', 'amazon-auto-links' ),
                'tip'               => __( 'The "more info" disclaimer link is supposed to be placed next to product prices and shown with the <code>%disclaimer%</code> tag in the <code>Item Format</code> unit option.', 'amazon-auto-links' )
            ),
            array(
                'field_id'          => 'disclaimer_text',
                'title'             => __( 'Disclaimer Text', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'rich'              => true,
                'class'             => 'width-100',
            ),
            array(
                'field_id'          => 'quote',
                'title'             => __( 'Why do we need this?', 'amazon-auto-links' ),
                'content'           => ''
                    . "<p>"
                        . sprintf(
                            __( 'According to the <a href="%1$s" target="_blank">terms</a> of Amazon Associates, it is required to display affiliate disclosure on your site.', 'amazon-auto-links' ),
                        'https://affiliate-program.amazon.com/help/operating/agreement'
                        )
                    . "</p>"
                    . "<blockquote class='blockquote'><p>" . <<<QUOTE
You must clearly and prominently state the following, or any substantially similar statement previously allowed under this Agreement, on your Site or any other location where Amazon may authorize your display or other use of Program Content: “As an Amazon Associate I earn from qualifying purchases.” Except for this disclosure, and other than as required by applicable law, you will not make any public communication with respect to this Agreement or your participation in the Associates Program without our advance written permission. You will not misrepresent or embellish our relationship with you (including by expressing or implying that we support, sponsor, or endorse you), or express or imply any affiliation between us and you or any other person or entity except as expressly permitted by this Agreement.
QUOTE
                . "</p><cite><a href='https://affiliate-program.amazon.com/help/operating/agreement'>Amazon.com Associates Central - Associates Program Operating Agreement</a></cite>"
                . "</blockquote>",
            ),
            array()
        );

    }

    /**
     * There is a case that the user deletes the set page. In that case, the option remains to keep the non-existent post ID.
     * So here checks if the post exists, then if not, it gives an empty value.
     * @param  array $aField
     * @return array
     */
    public function replyToGetFieldDefinition_page( $aField ) {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        $_iPage   = $_oOption->get( 'disclosure', 'page', 'value' );
        $_oPost   = get_post( $_iPage );
        if ( null === $_oPost ) {
            $aField[ 'value' ] = array();
        }
        return $aField;

    }

    /**
      * Called upon form validation.
      * @since    4.7.0
      * @return   array
      * @callback add_filter()      'validation_{class name}_{section id}'
      */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {

        $aInputs[ 'disclosure_text' ] = str_replace( '[aal_disclosure]', '', $aInputs[ 'disclosure_text' ] );

        if ( 'create_page' === $aSubmitInfo[ 'field_id' ] ) {
            return $this->___getInputsByCreatingDisclosurePage( $aInputs, $oAdminPage );
        }
        return $aInputs;
    }
        /**
         * @return array
         * @since  4.7.1
         */
        private function ___getInputsByCreatingDisclosurePage( $aInputs, $oAdminPage ) {

            $_iPostID  = AmazonAutoLinks_Disclosure_Utility::getDisclosurePageCreated();
            if ( $_iPostID ) {
                $_oPost = get_post( $_iPostID );
                $aInputs[ 'page' ] = array(
                    'value' => $_iPostID,
                    'encoded' => json_encode( array( array( 'id' => $_iPostID, 'text' => $_oPost->post_title ) ) ),
                );
            }

            $_sMessage = $_iPostID
                ? __( 'A page has been created.', 'amazon-auto-links' )
                : __( 'Could not create a page.', 'amazon-auto-links' );
            $oAdminPage->setSettingNotice( $_sMessage, $_iPostID ? 'updated' : 'error' );
            return $aInputs;
        }

}