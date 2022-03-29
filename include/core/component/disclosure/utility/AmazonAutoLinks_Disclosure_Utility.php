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
 * Provides shared methods within the Disclosure component.
 *
 * @since        4.7.0
 */
class AmazonAutoLinks_Disclosure_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * @return integer
     */
    static public function getDisclosurePageCreated() {
        $_sPageContent = <<<PAGECONENT
<!-- wp:shortcode -->
[aal_disclosure]
<!-- /wp:shortcode -->
PAGECONENT;
        return self::createPost(
            'page',
            array( // post columns
                'post_title'    => __( 'Affiliate Disclosure', 'amazon-auto-links' ),
                'post_content'  => $_sPageContent,
                'guid'          => AmazonAutoLinks_Disclosure_Loader::$sDisclosureGUID,
            )
        );
    }

    /**
     * Retrieves and return posts with the array structure of `select2` AJAX format.
     *
     * <h4>Structure of Response Array</h4>
     * It must be an associative array with the element keys of `results` and `pagination`.
     * In the `results` element must be a numerically indexed array holding an array with the kes of `id` and `text`.
     * The `pagination` element can be optional and should be an array holding an element named `more` which accepts a boolean value.
     *
     * ```
     * array(
     *      'results'  => array(
     *          array( 'id' => 223, 'text' => 'Title of 223' ),
     *          array( 'id' => 665, 'text' => 'Title of 665' ),
     *          array( 'id' => 9355, 'text' => 'Title of 9355' ),
     *          ...
     *      ),
     *      'pagination' => array(
     *          'more'  => true,    // (boolean) or false - whether the next paginated item exists or not.
     *      )
     * )
     * ```
     * Or the `pagination` element can be omitted.
     * ```
     * array(
     *      'results'  => array(
     *          array( 'id' => 223, 'text' => 'Title of 223' ),
     *          array( 'id' => 665, 'text' => 'Title of 665' ),
     *          array( 'id' => 9355, 'text' => 'Title of 9355' ),
     *          ...
     *      ),
     * )
     * ```
     *
     * @access      static      For faster processing.
     * @remark      The arguments of the passed queries by select2 are `page` (the page number) and `q` (the user-typed keyword in the input).
     * @remark      For the WP_Query arguments, see https://codex.wordpress.org/Class_Reference/WP_Query#Pagination_Parameters
     * @see         https://codex.wordpress.org/Class_Reference/WP_Query#Pagination_Parameters
     * @see         https://select2.github.io/examples.html#data-ajax
     * @return      array
     */
    static public function getPages( $aQueries, $aFieldset ) {

        $_aArgs         = array(
            'post_type'         => 'page',
            'paged'             => $aQueries[ 'page' ],
            's'                 => $aQueries[ 'q' ],
            'posts_per_page'    => 30,
            'nopaging'          => false,
        );
        $_oResults      = new WP_Query( $_aArgs );
        $_aPostTitles   = array();
        foreach( $_oResults->posts as $_iIndex => $_oPost ) {
            $_aPostTitles[] = array(    // must be numeric
                'id'    => $_oPost->ID,
                'text'  => $_oPost->post_title,
            );
        }
        return array(
            'results'       => $_aPostTitles,
            'pagination'    => array(
                'more'  => intval( $_oResults->max_num_pages ) !== intval( $_oResults->get( 'paged' ) ),
            ),
        );

    }

}