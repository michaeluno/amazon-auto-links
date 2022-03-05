<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A class that provides methods to format the `updated_date` element value.
 *
 * @since 5.1.4
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_UpdatedTime extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return string
     * @throws Exception
     * @since  5.1.4
     */
    public function get() {
        $_isUpdatedTimeFetched  = $this->_aProduct[ 'updated_date' ];
        $_iUpdatedTimeFetched   = is_numeric( $_isUpdatedTimeFetched )
            ? ( integer ) $_isUpdatedTimeFetched
            : ( integer ) strtotime( $this->_aProduct[ 'updated_date' ] );
        $_sUpdatedTimeDB        = $this->getElement( $this->_aRow, array( 'modified_time' ) );
        $_iUpdatedTimeDB        = empty( $_sUpdatedTimeDB ) ? 0 : ( integer ) strtotime( $_sUpdatedTimeDB );
        return max( $_iUpdatedTimeFetched, $_iUpdatedTimeDB );  // use the larger value, which denotes latest.
    }

}