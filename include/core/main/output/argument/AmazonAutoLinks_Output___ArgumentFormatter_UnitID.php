<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Provides methods to format the `id` output argument.
 * @since       3.5.0
 */
class AmazonAutoLinks_Output___ArgumentFormatter_UnitID extends AmazonAutoLinks_Output___ArgumentFormatter_Base {

    /**
     * Returns an array an array holding unit IDs which get found from the arguments passed to the constructor.
     * @since   3.5.0
     * @return  array An array holding unit IDs.
     */
    public function get() {

        $_aIDs = array();

        // The id parameter - the id parameter can accept comma delimited ids.
        if ( isset( $this->_aArguments[ 'id' ] ) ) {
            $_aIDs = $this->___getIDsFormatted( $this->_aArguments[ 'id' ] );
        }

        // The label parameter.
        if ( isset( $this->_aArguments[ 'label' ] ) ) {
            $this->_aArguments[ '_labels' ] = $this->getStringIntoArray( $this->_aArguments[ 'label' ], ',' );
            $_aIDs = array_merge(
                $this->___getPostIDsByLabel(
                    $this->_aArguments[ '_labels' ],
                    $this->getElement( $this->_aArguments, 'operator' )
                ),
                $_aIDs
            );
        }
        return array_unique( $_aIDs );

    }
        /**
         * Formates the `id` argument.
         * @since       3.4.9
         * @since       3.5.0       Moved from `AmazonAutoLinks_Output`.
         * @return      array
         */
        private function ___getIDsFormatted( $aisIDs ) {
            if ( is_scalar( $aisIDs ) ) {
                return $this->getStringIntoArray( $aisIDs, ',' );
            }
            // The Auto-insert feature passes ids with an array.
            if ( is_array( $aisIDs ) ) {
                return $aisIDs;
            }
            return $this->getAsArray( $aisIDs );
        }

        /**
         * Retrieves the post (unit) IDs by the given unit label.
         * @return      array
         */
        private function ___getPostIDsByLabel( $aLabels, $sOperator ) {

            // Retrieve the taxonomy slugs of the given taxonomy names.
            $_aTermSlugs = array();
            foreach( ( array ) $aLabels as $_sTermName ) {
                $_aTerm         = get_term_by(
                    'name',
                    $_sTermName,
                    AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ],
                    ARRAY_A
                );
                $_aTermSlugs[]  = $_aTerm[ 'slug' ];

            }
            return $this->___getPostIDsByTag( $_aTermSlugs, 'slug', $sOperator );

        }
            /**
             * Retrieves post (unit) IDs by the plugin tag taxonomy slug.
             * @return      array       An array holding found post ids.
             */
            private function ___getPostIDsByTag( $aTermSlugs, $sFieldType='slug', $sOperator='AND' ) {

                if ( empty( $aTermSlugs ) ) {
                    return array();
                }

                return get_posts(
                    array(
                        'post_type'         => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                        'posts_per_page'    => -1, // ALL posts
                        'fields'            => 'ids',
                        'tax_query'         => array(
                            array(
                                'taxonomy'  => AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ],
                                'field'     => $this->___sanitizeFieldKey( $sFieldType ),    // id or slug
                                'terms'     => $aTermSlugs, // the array of term slugs
                                'operator'  => $this->___sanitizeOperator( $sOperator ),    // 'IN', 'NOT IN', 'AND. If the item is only one, use AND.
                            )
                        )
                    )
                );

            }
                private function ___sanitizeFieldKey( $sField ) {
                    switch( strtolower( trim( $sField ) ) ) {
                        case 'id':
                            return 'id';
                        default:
                        case 'slug':
                            return 'slug';
                    }
                }
                private function ___sanitizeOperator( $sOperator ) {
                    switch( strtoupper( trim( $sOperator ) ) ) {
                        case 'NOT IN':
                            return 'NOT IN';
                        case 'IN':
                            return 'IN';
                        default:
                        case 'AND':
                            return 'AND';
                    }
                }

}