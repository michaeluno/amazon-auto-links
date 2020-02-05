<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Displays image preview for category units.
 *
 * Only used for the category selection pages
 * and shows only product thumbnails, ignoring the item format.
 *
 * @package     Amazon Auto Links
 * @sicne  3.8.1
 * @deprecated
 */
class AmazonAutoLinks_UnitOutput_category2_preview extends AmazonAutoLinks_UnitOutput_category2 {

    /**
     * Sets up properties. Called at the end of the constructor.
     *
     * @remark      The 'tag' unit type will override this method.
     */
    protected function _setProperties() {
        $this->oUnitOption->set(
            'item_format',
            '<p>You should not see this message. '
            . 'This is a dummy output to prevent extra database queries by not setting Item Format variables.'
            . 'The output format is taken cared in the Preview template.</p>'
        );
        parent::_setProperties();
        $this->oUnitOption->set( '_sort', 'raw' );
    }

}