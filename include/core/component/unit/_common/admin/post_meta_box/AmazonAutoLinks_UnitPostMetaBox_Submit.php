<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Defines the meta box that shows the Select Categories submit button
 */
class AmazonAutoLinks_UnitPostMetaBox_Submit extends AmazonAutoLinks_UnitPostMetaBox_Base {

    public function setUp() {
        add_action( "do_" . $this->oProp->sClassName, array( $this, 'replyToPrintMetaBoxContent' ) );
    }
        
    public function _replyToRegisterMetaBoxes() {
        $this->___disablePreviewChanges();
        if ( ! $this->___canRegister() ) {
            // The native submit meta-box will appear.
            return;
        }
        // Overrides the native submit meta-box
        parent::_replyToRegisterMetaBoxes();
    }
        private function ___canRegister() {
            $_oNumber = AmazonAutoLinks_WPUtility::countPosts( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );
            $_iNumber = $_oNumber->publish + $_oNumber->private + $_oNumber->trash;
            if ( $_iNumber < 4 ) {
                return false;
            }
            $_oOption = AmazonAutoLinks_Option::getInstance();
            return ! $_oOption->isAdvancedUnitSubmitSupported();
        }
        private function ___disablePreviewChanges() {
            $_oPostType = get_post_type_object( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );
            $_oPostType->publicly_queryable = false;
        }

    /**
     * Draws the Select Category submit button and some other links.
     * A short version of post_submit_meta_box().
     * @since 4.7.0
     * @see   post_submit_meta_box()
     */
    public function replyToPrintMetaBoxContent( $oFactory ) {

        global $action;
        $post = get_post( AmazonAutoLinks_WPUtility::getCurrentPostID() );

        $post_id          = (int) $post->ID;
        $post_type        = $post->post_type;
        $post_type_object = get_post_type_object( $post_type );
        $can_publish      = current_user_can( $post_type_object->cap->publish_posts );
        ?>
<div class="submitbox" id="submitpost">

    <div id="minor-publishing">

        <div id="misc-publishing-actions">
            <div class="misc-pub-section misc-pub-post-status">
                <?php _e( 'Status:' ); ?>
                <span id="post-status-display">
                    <?php
                    switch ( $post->post_status ) {
                        case 'private':
                            _e( 'Privately Published' );
                            break;
                        case 'publish':
                            _e( 'Published' );
                            break;
                        case 'future':
                            _e( 'Scheduled' );
                            break;
                        case 'pending':
                            _e( 'Pending Review' );
                            break;
                        case 'draft':
                        case 'auto-draft':
                            _e( 'Draft' );
                            break;
                    }
                    ?>
                </span>

                <?php
                if ( 'publish' === $post->post_status || 'private' === $post->post_status || $can_publish ) {
                    $private_style = '';
                    if ( 'private' === $post->post_status ) {
                        $private_style = 'style="display:none"';
                    }
                    ?>
                    <a href="#post_status" <?php echo $private_style; ?> class="edit-post-status hide-if-no-js" role="button"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit status' ); ?></span></a>

                    <div id="post-status-select" class="hide-if-js">
                        <input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ( 'auto-draft' === $post->post_status ) ? 'draft' : $post->post_status ); ?>" />
                        <label for="post_status" class="screen-reader-text"><?php _e( 'Set status' ); ?></label>
                        <select name="post_status" id="post_status">
                            <?php if ( 'publish' === $post->post_status ) : ?>
                                <option<?php selected( $post->post_status, 'publish' ); ?> value='publish'><?php _e( 'Published' ); ?></option>
                            <?php elseif ( 'private' === $post->post_status ) : ?>
                                <option<?php selected( $post->post_status, 'private' ); ?> value='publish'><?php _e( 'Privately Published' ); ?></option>
                            <?php elseif ( 'future' === $post->post_status ) : ?>
                                <option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e( 'Scheduled' ); ?></option>
                            <?php endif; ?>
                                <option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e( 'Pending Review' ); ?></option>
                            <?php if ( 'auto-draft' === $post->post_status ) : ?>
                                <option<?php selected( $post->post_status, 'auto-draft' ); ?> value='draft'><?php _e( 'Draft' ); ?></option>
                            <?php else : ?>
                                <option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e( 'Draft' ); ?></option>
                            <?php endif; ?>
                        </select>
                        <a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e( 'OK' ); ?></a>
                        <a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div class="misc-pub-section misc-pub-visibility" id="visibility">
                <?php _e( 'Visibility:' ); ?>
                <span id="post-visibility-display">
                    <?php
                    if ( 'private' === $post->post_status ) {
                        $post->post_password = '';
                        $visibility          = 'private';
                        $visibility_trans    = __( 'Private' );
                    } elseif ( ! empty( $post->post_password ) ) {
                        $visibility       = 'password';
                        $visibility_trans = __( 'Password protected' );
                    } elseif ( 'post' === $post_type && is_sticky( $post_id ) ) {
                        $visibility       = 'public';
                        $visibility_trans = __( 'Public, Sticky' );
                    } else {
                        $visibility       = 'public';
                        $visibility_trans = __( 'Public' );
                    }

                    echo esc_html( $visibility_trans );
                    ?>
                </span>

                <?php if ( $can_publish ) { ?>
                    <a href="#visibility" class="edit-visibility hide-if-no-js" role="button"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit visibility' ); ?></span></a>

                    <div id="post-visibility-select" class="hide-if-js">
                        <?php if ( 'post' === $post_type ) : ?>
                            <input type="checkbox" style="display:none" name="hidden_post_sticky" id="hidden-post-sticky" value="sticky" <?php checked( is_sticky( $post_id ) ); ?> />
                        <?php endif; ?>

                        <input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php checked( $visibility, 'public' ); ?> /> <label for="visibility-radio-public" class="selectit"><?php _e( 'Public' ); ?></label><br />

                        <?php if ( 'post' === $post_type && current_user_can( 'edit_others_posts' ) ) : ?>
                            <span id="sticky-span"><input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky( $post_id ) ); ?> /> <label for="sticky" class="selectit"><?php _e( 'Stick this post to the front page' ); ?></label><br /></span>
                        <?php endif; ?>

                        <input type="radio" name="visibility" id="visibility-radio-password" value="password" <?php checked( $visibility, 'password' ); ?> /> <label for="visibility-radio-password" class="selectit"><?php _e( 'Password protected' ); ?></label><br />
                        <span id="password-span"><label for="post_password"><?php _e( 'Password:' ); ?></label> <input type="text" name="post_password" id="post_password" value="<?php echo esc_attr( $post->post_password ); ?>"  maxlength="255" /><br /></span>

                        <input type="radio" name="visibility" id="visibility-radio-private" value="private" <?php checked( $visibility, 'private' ); ?> /> <label for="visibility-radio-private" class="selectit"><?php _e( 'Private' ); ?></label><br />

                        <p>
                            <a href="#visibility" class="save-post-visibility hide-if-no-js button"><?php _e( 'OK' ); ?></a>
                            <a href="#visibility" class="cancel-post-visibility hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
                        </p>
                    </div>
                <?php } ?>
            </div>

            <?php
            /* translators: Publish box date string. 1: Date, 2: Time. See https://www.php.net/manual/datetime.format.php */
            $date_string = __( '%1$s at %2$s' );
            /* translators: Publish box date format, see https://www.php.net/manual/datetime.format.php */
            $date_format = _x( 'M j, Y', 'publish box date format' );
            /* translators: Publish box time format, see https://www.php.net/manual/datetime.format.php */
            $time_format = _x( 'H:i', 'publish box time format' );

            if ( 'future' === $post->post_status ) { // Scheduled for publishing at a future date.
                /* translators: Post date information. %s: Date on which the post is currently scheduled to be published. */
                $stamp = __( 'Scheduled for: %s' );
            } elseif ( 'publish' === $post->post_status || 'private' === $post->post_status ) { // Already published.
                /* translators: Post date information. %s: Date on which the post was published. */
                $stamp = __( 'Published on: %s' );
            } elseif ( '0000-00-00 00:00:00' === $post->post_date_gmt ) { // Draft, 1 or more saves, no date specified.
                $stamp = __( 'Publish <b>immediately</b>' );
            } elseif ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // Draft, 1 or more saves, future date specified.
                /* translators: Post date information. %s: Date on which the post is to be published. */
                $stamp = __( 'Schedule for: %s' );
            } else { // Draft, 1 or more saves, date specified.
                /* translators: Post date information. %s: Date on which the post is to be published. */
                $stamp = __( 'Publish on: %s' );
            }
            $date = sprintf(
                $date_string,
                date_i18n( $date_format, strtotime( $post->post_date ) ),
                date_i18n( $time_format, strtotime( $post->post_date ) )
            );

            if ( $can_publish ) : // Contributors don't get to choose the date of publish.
                ?>
                <div class="misc-pub-section curtime misc-pub-curtime">
                    <span id="timestamp">
                        <?php printf( $stamp, '<b>' . $date . '</b>' ); ?>
                    </span>
                    <a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" role="button">
                        <span aria-hidden="true"><?php _e( 'Edit' ); ?></span>
                        <span class="screen-reader-text"><?php _e( 'Edit date and time' ); ?></span>
                    </a>
                    <fieldset id="timestampdiv" class="hide-if-js">
                        <legend class="screen-reader-text"><?php _e( 'Date and time' ); ?></legend>
                        <?php touch_time( ( 'edit' === $action ), 1 ); ?>
                    </fieldset>
                </div>
                <?php
            endif;

            if ( 'draft' === $post->post_status && get_post_meta( $post_id, '_customize_changeset_uuid', true ) ) :
                ?>
                <div class="notice notice-info notice-alt inline">
                    <p>
                        <?php
                        printf(
                            /* translators: %s: URL to the Customizer. */
                            __( 'This draft comes from your <a href="%s">unpublished customization changes</a>. You can edit, but there&#8217;s no need to publish now. It will be published automatically with those changes.' ),
                            esc_url(
                                add_query_arg(
                                    'changeset_uuid',
                                    rawurlencode( get_post_meta( $post_id, '_customize_changeset_uuid', true ) ),
                                    admin_url( 'customize.php' )
                                )
                            )
                        );
                        ?>
                    </p>
                </div>
                <?php
            endif;
            ?>
        </div>
        <div class="clear"></div>
    </div>

    <div id="major-publishing-actions">
        <div id="delete-action">
            <?php
            if ( current_user_can( 'delete_post', $post_id ) ) {
                $delete_text = EMPTY_TRASH_DAYS ? __( 'Move to Trash' ) : __( 'Delete permanently' );
                ?>
                <a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post_id ); ?>"><?php echo esc_html( $delete_text ); ?></a>
                <?php
            }
            ?>
        </div>
        <div id="publishing-action">
            <span class="spinner"></span>
            <span id="publish" class="button button-primary button-large disabled amazon-auto-links-form-tooltip" ><?php esc_html_e( 'Update' ); ?>
                <span class="amazon-auto-links-form-tooltip-content hidden">
                    <span class="dashicons dashicons-warning field-error"></span>
                    <span><?php echo wp_kses( AmazonAutoLinks_Message::getUpgradePromptMessage(), 'post' ); ?></span>
                </span>
            </span>
        </div>
        <div class="clear"></div>
    </div>

</div>
        <?php
    }

}