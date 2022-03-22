<?php
/**
 * Admin Page Framework
 *
 * http://amazon-auto-links.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods for text messages.
 *
 * @since    2.0.0
 * @since    2.1.6       Multiple instances of this class are disallowed.
 * @since    3.2.0       Multiple instances of this class are allowed but the instantiation is restricted to per text domain basis.
 * @package  AmazonAutoLinks_AdminPageFramework/Common/Factory/Property
 * @internal
 * @remark   When adding a new framework translation item,
 * Step 1: add a key and the default value to the `$aDefaults` property array.
 * Step 2: add a dummy function call in the `___doDummy()` method so that parser programs can catch it.
 */
class AmazonAutoLinks_AdminPageFramework_Message {

    /**
     * Stores the framework's messages.
     *
     * @since  2.0.0
     * @since  3.1.3       No item is defined by default but done on the fly per request. The below array structure is kept for backward compatibility.
     * @remark The user may modify this property directly.
     */
    public $aMessages = array();

    /**
     * Stores default translated items.
     *
     * @remark      These items should be accessed only when its label needs to be displayed.
     * So the translation method `__()` only gets executed for one file.
     *
     * Consider the difference between the two.
     * <code>
     * $_aTranslations = array(
     *      'foo'  => __( 'Foo', 'amazon-auto-links' ),
     *      'bar'  => __( 'Bar', 'amazon-auto-links' ),
     *       ... more 100 items
     * )
     * return isset( $_aTranslations[ $sKey ] ) ? $_aTranslations[ $sKey ] : '';
     * </code>
     *
     * <code>
     * $_aTranslations = array(
     *      'foo'  => 'Foo',
     *      'bar'  => 'Bar',
     *       ... more 100 items
     * )
     * return isset( $_aTranslations[ $sKey ] )
     *      ? __( $_aTranslations[ $sKey ], $sUserSetTextdomain )
     *      : '';
     * </code>
     * @since       3.5.3
     */
    public $aDefaults = array(

        // AmazonAutoLinks_AdminPageFramework
        'option_updated'                        => 'The options have been updated.',
        'option_cleared'                        => 'The options have been cleared.',
        'export'                                => 'Export',
        'export_options'                        => 'Export Options',
        'import'                                => 'Import',
        'import_options'                        => 'Import Options',
        'submit'                                => 'Submit',
        'import_error'                          => 'An error occurred while uploading the import file.',
        'uploaded_file_type_not_supported'      => 'The uploaded file type is not supported: %1$s',
        'could_not_load_importing_data'         => 'Could not load the importing data.',
        'imported_data'                         => 'The uploaded file has been imported.',
        'not_imported_data'                     => 'No data could be imported.',
        'upload_image'                          => 'Upload Image',
        'use_this_image'                        => 'Use This Image',
        'insert_from_url'                       => 'Insert from URL',
        'reset_options'                         => 'Are you sure you want to reset the options?',
        'confirm_perform_task'                  => 'Please confirm your action.',
        'specified_option_been_deleted'         => 'The specified options have been deleted.',
        'nonce_verification_failed'             => 'A problem occurred while processing the form data. Please try again.',
        'check_max_input_vars'                  => 'Not all form fields could not be sent. '
            . 'Please check your server settings of PHP <code>max_input_vars</code> and consult the server administrator to increase the value. '
            . '<code>max input vars</code>: %1$s. <code>$_POST</code> count: %2$s',  // 3.5.11+ // sanitization unnecessary as it is just a literal string
        'send_email'                            => 'Is it okay to send the email?',     // 3.3.0+
        'email_sent'                            => 'The email has been sent.',  // 3.3.0+, 3.3.5+ deprecated, 3.8.32 Re-added
        'email_scheduled'                       => 'The email has been scheduled.', // 3.3.5+, 3.8.32 deprecated
        'email_could_not_send'                  => 'There was a problem sending the email',     // 3.3.0+

        // AmazonAutoLinks_AdminPageFramework_PostType
        'title'                                 => 'Title',
        'author'                                => 'Author',
        'categories'                            => 'Categories',
        'tags'                                  => 'Tags',
        'comments'                              => 'Comments',
        'date'                                  => 'Date',
        'show_all'                              => 'Show All',
        'show_all_authors'                      => 'Show all Authors', // 3.5.10+

        // AmazonAutoLinks_AdminPageFramework_Link_Base
        'powered_by'                            => 'Thank you for creating with',
        'and'                                   => 'and',

        // AmazonAutoLinks_AdminPageFramework_Link_admin_page
        'settings'                              => 'Settings',

        // AmazonAutoLinks_AdminPageFramework_Link_post_type
        'manage'                                => 'Manage',

        // AmazonAutoLinks_AdminPageFramework_FieldType_{...}
        'select_image'                          => 'Select Image',
        'upload_file'                           => 'Upload File',
        'use_this_file'                         => 'Use This File',
        'select_file'                           => 'Select File',
        'remove_value'                          => 'Remove Value',  // 3.2.0+
        'select_all'                            => 'Select All',    // 3.3.0+
        'select_none'                           => 'Select None',   // 3.3.0+
        'no_term_found'                         => 'No term found.', // 3.3.2+

        // AmazonAutoLinks_AdminPageFramework_Form_View___Script_{...}
        'select'                                => 'Select', // 3.4.2+
        'insert'                                => 'Insert',  // 3.4.2+
        'use_this'                              => 'Use This', // 3.4.2+
        'return_to_library'                     => 'Return to Library', // 3.4.2+

        // AmazonAutoLinks_AdminPageFramework_PageLoadInfo_Base
        'queries_in_seconds'                    => '%1$s queries in %2$s seconds.',
        'out_of_x_memory_used'                  => '%1$s out of %2$s (%3$s) memory used.',
        'peak_memory_usage'                     => 'Peak memory usage %1$s.',
        'initial_memory_usage'                  => 'Initial memory usage  %1$s.',

        // Repeatable sections & fields
        'repeatable_section_is_disabled'        => 'The ability to repeat sections is disabled.', // 3.8.13+
        'repeatable_field_is_disabled'          => 'The ability to repeat fields is disabled.',   // 3.8.13+
        'warning_caption'                       => 'Warning',   // 3.8.13+

        // AmazonAutoLinks_AdminPageFramework_FormField
        'allowed_maximum_number_of_fields'      => 'The allowed maximum number of fields is {0}.',
        'allowed_minimum_number_of_fields'      => 'The allowed minimum number of fields is {0}.',
        'add'                                   => 'Add',
        'remove'                                => 'Remove',

        // AmazonAutoLinks_AdminPageFramework_FormPart_Table
        'allowed_maximum_number_of_sections'    => 'The allowed maximum number of sections is {0}',
        'allowed_minimum_number_of_sections'    => 'The allowed minimum number of sections is {0}',
        'add_section'                           => 'Add Section',
        'remove_section'                        => 'Remove Section',
        'toggle_all'                            => 'Toggle All',
        'toggle_all_collapsible_sections'       => 'Toggle all collapsible sections',

        // AmazonAutoLinks_AdminPageFramework_FieldType_reset 3.3.0+
        'reset'                                 => 'Reset',

        // AmazonAutoLinks_AdminPageFramework_FieldType_system 3.5.3+
        'yes'                                   => 'Yes',
        'no'                                    => 'No',
        'on'                                    => 'On',
        'off'                                   => 'Off',
        'enabled'                               => 'Enabled',
        'disabled'                              => 'Disabled',
        'supported'                             => 'Supported',
        'not_supported'                         => 'Not Supported',
        'functional'                            => 'Functional',
        'not_functional'                        => 'Not Functional',
        'too_long'                              => 'Too Long',
        'acceptable'                            => 'Acceptable',
        'no_log_found'                          => 'No log found.',

        // 3.7.0+ - accessed from `AmazonAutoLinks_AdminPageFramework_Form`
        'method_called_too_early'               => 'The method is called too early.',

        // 3.7.0+  - accessed from `AmazonAutoLinks_AdminPageFramework_Form_View___DebugInfo`
        'debug_info'                            => 'Debug Info',
        // 3.8.5+
        'debug'                                 => 'Debug',
        // 'field_arguments'                       => 'Field Arguments', // @deprecated 3.8.22
        'debug_info_will_be_disabled'           => 'This information will be disabled when <code>WP_DEBUG</code> is set to <code>false</code> in <code>wp-config.php</code>.',

        // 'section_arguments'                     => 'Section Arguments', // 3.8.8+   // @deprecated 3.8.22

        'click_to_expand'                       => 'Click here to expand to view the contents.',
        'click_to_collapse'                     => 'Click here to collapse the contents.',

        // 3.7.0+ - displayed while the page laods
        'loading'                               => 'Loading...',
        'please_enable_javascript'              => 'Please enable JavaScript for better user experience.',

        'submit_confirmation_label'             => 'Submit the form.',
        'submit_confirmation_error'             => 'Please check this box if you want to proceed.',
        'import_no_file'                        => 'No file is selected.',

        // 3.9.0
        'please_fill_out_this_field'            => 'Please fill out this field.',

    );

    /**
     * Stores the text domain.
     * @since 3.x
     * @since 3.5.0       Declared as a default property.
     */
    protected $_sTextDomain = 'amazon-auto-links';

    /**
     * Stores the self instance by text domain.
     * @internal
     * @since    3.2.0
     */
    static private $_aInstancesByTextDomain = array();

    /**
     * Ensures that only one instance of this class object exists. ( no multiple instances of this object )
     *
     * @since       2.1.6
     * @since       3.2.0       Changed it to create an instance per text domain basis.
     * @param       string      $sTextDomain
     * @remark      This class should be instantiated via this method.
     * @return      AmazonAutoLinks_AdminPageFramework_Message
     */
    public static function getInstance( $sTextDomain='amazon-auto-links' ) {

        $_oInstance = isset( self::$_aInstancesByTextDomain[ $sTextDomain ] ) && ( self::$_aInstancesByTextDomain[ $sTextDomain ] instanceof AmazonAutoLinks_AdminPageFramework_Message )
            ? self::$_aInstancesByTextDomain[ $sTextDomain ]
            : new AmazonAutoLinks_AdminPageFramework_Message( $sTextDomain );
        self::$_aInstancesByTextDomain[ $sTextDomain ] = $_oInstance;
        return self::$_aInstancesByTextDomain[ $sTextDomain ];

    }
        /**
         * Ensures that only one instance of this class object exists. ( no multiple instances of this object )
         * @deprecated  3.2.0
         */
        public static function instantiate( $sTextDomain='amazon-auto-links' ) {
            return self::getInstance( $sTextDomain );
        }

    /**
     * Sets up properties.
     * @param string $sTextDomain
     */
    public function __construct( $sTextDomain='amazon-auto-links' ) {

        $this->_sTextDomain = $sTextDomain;

        // Fill the $aMessages property with the keys extracted from the $aDefaults property
        // with the value of null.  The null is set to let it trigger the __get() method
        // so that each translation item gets processed individually.
        $this->aMessages    = array_fill_keys(
            array_keys( $this->aDefaults ),
            null
        );

    }

    /**
     * Returns the set text domain string.
     *
     * This is used from field type and input classes to display deprecated admin errors/
     *
     * @since 3.3.3
     */
    public function getTextDomain() {
        return $this->_sTextDomain;
    }

    /**
     * Sets a message for the given key.
     * @since       3.7.0
     */
    public function set( $sKey, $sValue ) {
        $this->aMessages[ $sKey ] = $sValue;
    }

    /**
     * Returns the framework system message by key.
     *
     * @remark An alias of the __() method.
     * @since  3.2.0
     * @since  3.7.0        If no key is specified, return the entire mesage array.
     * @param  string       $sKey
     * @return string|array
     */
    public function get( $sKey='' ) {
        if ( ! $sKey ) {
            return $this->_getAllMessages();
        }
        return isset( $this->aMessages[ $sKey ] )
            ? __( $this->aMessages[ $sKey ], $this->_sTextDomain )
            : __( $this->{$sKey}, $this->_sTextDomain );     // triggers __get()
    }
        /**
         * Returns the all registered messag items.
         * By default, no item is set for a performance reason; the message is retuned on the fly.
         * So all the keys must be iterated to get all the values.
         * @since       3.7.0
         * @return      array
         */
        private function _getAllMessages() {
            $_aMessages = array();
            foreach ( $this->aMessages as $_sLabel => $_sTranslation ) {
                $_aMessages[ $_sLabel ] = $this->get( $_sLabel );
            }
            return $_aMessages;
        }

    /**
     * Echoes the framework system message by key.
     * @remark An alias of the _e() method.
     * @since  3.2.0
     */
    public function output( $sKey ) {
        echo $this->get( $sKey );
    }

        /**
         * Returns the framework system message by key.
         * @since       2.x
         * @deprecated  3.2.0
         */
        public function __( $sKey ) {
            return $this->get( $sKey );
        }
        /**
         * Echoes the framework system message by key.
         * @since       2.x
         * @deprecated  3.2.0
         */
        public function _e( $sKey ) {
            $this->output( $sKey );
        }

    /**
     * Responds to a request to an undefined property.
     *
     * @since  3.1.3
     * @return string
     */
    public function __get( $sPropertyName ) {
        return isset( $this->aDefaults[ $sPropertyName ] ) ? $this->aDefaults[ $sPropertyName ] : $sPropertyName;
    }


    /**
     * A dummy method just lists translation items to be parsed by translation programs such as POEdit.
     *
     * @since 3.5.3
     * @since 3.8.19 Changed the name to avoid false-positives of PHP 7.2 incompatibility by third party tools.
     */
    private function ___doDummy() {

        __( 'The options have been updated.', 'amazon-auto-links' );
        __( 'The options have been cleared.', 'amazon-auto-links' );
        __( 'Export', 'amazon-auto-links' );
        __( 'Export Options', 'amazon-auto-links' );
        __( 'Import', 'amazon-auto-links' );
        __( 'Import Options', 'amazon-auto-links' );
        __( 'Submit', 'amazon-auto-links' );
        __( 'An error occurred while uploading the import file.', 'amazon-auto-links' );
        /* translators: 1: Uploaded file type */
        __( 'The uploaded file type is not supported: %1$s', 'amazon-auto-links' );
        __( 'Could not load the importing data.', 'amazon-auto-links' );
        __( 'The uploaded file has been imported.', 'amazon-auto-links' );
        __( 'No data could be imported.', 'amazon-auto-links' );
        __( 'Upload Image', 'amazon-auto-links' );
        __( 'Use This Image', 'amazon-auto-links' );
        __( 'Insert from URL', 'amazon-auto-links' );
        __( 'Are you sure you want to reset the options?', 'amazon-auto-links' );
        __( 'Please confirm your action.', 'amazon-auto-links' );
        __( 'The specified options have been deleted.', 'amazon-auto-links' );
        __( 'A problem occurred while processing the form data. Please try again.', 'amazon-auto-links' );
        /* translators: 1: The value of max_input_vars set by PHP 2: Actual $_POST element count */
        __( 'Not all form fields could not be sent. Please check your server settings of PHP <code>max_input_vars</code> and consult the server administrator to increase the value. <code>max input vars</code>: %1$s. <code>$_POST</code> count: %2$s', 'amazon-auto-links' ); // sanitization unnecessary as a literal string
        __( 'Is it okay to send the email?', 'amazon-auto-links' );
        __( 'The email has been sent.', 'amazon-auto-links' );
        __( 'The email has been scheduled.', 'amazon-auto-links' );
        __( 'There was a problem sending the email', 'amazon-auto-links' );
        __( 'Title', 'amazon-auto-links' );
        __( 'Author', 'amazon-auto-links' );
        __( 'Categories', 'amazon-auto-links' );
        __( 'Tags', 'amazon-auto-links' );
        __( 'Comments', 'amazon-auto-links' );
        __( 'Date', 'amazon-auto-links' );
        __( 'Show All', 'amazon-auto-links' );
        __( 'Show All Authors', 'amazon-auto-links' );
        __( 'Thank you for creating with', 'amazon-auto-links' );
        __( 'and', 'amazon-auto-links' );
        __( 'Settings', 'amazon-auto-links' );
        __( 'Manage', 'amazon-auto-links' );
        __( 'Select Image', 'amazon-auto-links' );
        __( 'Upload File', 'amazon-auto-links' );
        __( 'Use This File', 'amazon-auto-links' );
        __( 'Select File', 'amazon-auto-links' );
        __( 'Remove Value', 'amazon-auto-links' );
        __( 'Select All', 'amazon-auto-links' );
        __( 'Select None', 'amazon-auto-links' );
        __( 'No term found.', 'amazon-auto-links' );
        __( 'Select', 'amazon-auto-links' );
        __( 'Insert', 'amazon-auto-links' );
        __( 'Use This', 'amazon-auto-links' );
        __( 'Return to Library', 'amazon-auto-links' );
        /* translators: 1: Number of performed database queries 2: Elapsed seconds for page load */
        __( '%1$s queries in %2$s seconds.', 'amazon-auto-links' );
        /* translators: 1: Used memory amount 2: Max memory cap set by WordPress (WP_MEMORY_LIMIT) 3: Percentage of the memory usage */
        __( '%1$s out of %2$s MB (%3$s) memory used.', 'amazon-auto-links' );
        /* translators: 1: Peak memory usage amount */
        __( 'Peak memory usage %1$s MB.', 'amazon-auto-links' );
        /* translators: 1: Initial memory usage amount */
        __( 'Initial memory usage  %1$s MB.', 'amazon-auto-links' );
        __( 'The allowed maximum number of fields is {0}.', 'amazon-auto-links' );
        __( 'The allowed minimum number of fields is {0}.', 'amazon-auto-links' );
        __( 'Add', 'amazon-auto-links' );
        __( 'Remove', 'amazon-auto-links' );
        __( 'The allowed maximum number of sections is {0}', 'amazon-auto-links' );
        __( 'The allowed minimum number of sections is {0}', 'amazon-auto-links' );
        __( 'Add Section', 'amazon-auto-links' );
        __( 'Remove Section', 'amazon-auto-links' );
        __( 'Toggle All', 'amazon-auto-links' );
        __( 'Toggle all collapsible sections', 'amazon-auto-links' );
        __( 'Reset', 'amazon-auto-links' );
        __( 'Yes', 'amazon-auto-links' );
        __( 'No', 'amazon-auto-links' );
        __( 'On', 'amazon-auto-links' );
        __( 'Off', 'amazon-auto-links' );
        __( 'Enabled', 'amazon-auto-links' );
        __( 'Disabled', 'amazon-auto-links' );
        __( 'Supported', 'amazon-auto-links' );
        __( 'Not Supported', 'amazon-auto-links' );
        __( 'Functional', 'amazon-auto-links' );
        __( 'Not Functional', 'amazon-auto-links' );
        __( 'Too Long', 'amazon-auto-links' );
        __( 'Acceptable', 'amazon-auto-links' );
        __( 'No log found.', 'amazon-auto-links' );

        /* translators: 1: Method name */
        __( 'The method is called too early: %1$s', 'amazon-auto-links' );
        __( 'Debug Info', 'amazon-auto-links' );

        __( 'Click here to expand to view the contents.', 'amazon-auto-links' );
        __( 'Click here to collapse the contents.', 'amazon-auto-links' );

        __( 'Loading...', 'amazon-auto-links' );
        __( 'Please enable JavaScript for better user experience.', 'amazon-auto-links' );

        __( 'Debug', 'amazon-auto-links' );
        // __( 'Field Arguments', 'amazon-auto-links' ); @deprecated 3.8.22
        __( 'This information will be disabled when <code>WP_DEBUG</code> is set to <code>false</code> in <code>wp-config.php</code>.', 'amazon-auto-links' );

        // __( 'Section Arguments', 'amazon-auto-links' ); // 3.8.8+ @deprecated 3.8.22

        __( 'The ability to repeat sections is disabled.', 'amazon-auto-links' ); // 3.8.13+
        __( 'The ability to repeat fields is disabled.', 'amazon-auto-links' ); // 3.8.13+
        __( 'Warning.', 'amazon-auto-links' ); // 3.8.13+

        __( 'Submit the form.', 'amazon-auto-links' ); // 3.8.24
        __( 'Please check this box if you want to proceed.', 'amazon-auto-links' ); // 3.8.24
        __( 'No file is selected.', 'amazon-auto-links' ); // 3.8.24

        __( 'Please fill out this field.', 'amazon-auto-links' ); // 3.9.0

    }

}