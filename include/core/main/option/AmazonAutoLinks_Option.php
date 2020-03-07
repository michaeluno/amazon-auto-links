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
 * Handles plugin options.
 * 
 * @since       3
 * @filter      apply       aal_filter_option_class_name
 */
class AmazonAutoLinks_Option extends AmazonAutoLinks_Option_Base {

    /**
     * Stores instances by option key.
     * 
     * @since       3
     */
    static public $aInstances = array(
        // key => object
    );
        
    /**
     * Stores the default values.
     */
    public $aDefault = array(
    
        'capabilities' => array(
            'setting_page_capability' => 'manage_options',
        ),        
        'debug' => array(
            'debug_mode' => 0,
        ),
        'form_options' => array(
            'allowed_html_tags'             => 'style, noscript',
            'allowed_attributes'            => 'additionalType, alternateName, description, image, mainEntityOfPage, name, potentialAction, sameAs, url, about, actionOption, category, characterAttribute, dataFeedElement, defaultValue, error, gameItem, gamePlatform, instrument, item, itemListElement, itemReviewed, mainEntity, mentions, object, purpose, quest, replacee, replacer, reservationFor, result, serviceOutput, targetCollection, actionStatus, agent, endTime, location, participant, startTime, target, interactionType, accessibilityAPI, accessibilityControl, accessibilityFeature, accessibilityHazard, accountablePerson, aggregateRating, alternativeHeadline, associatedMedia, audience, audio, author, award, character, citation, comment, commentCount, contentLocation, contentRating, contributor, copyrightHolder, copyrightYear, creator, dateCreated, dateModified, datePublished, discussionUrl, editor, educationalAlignment, educationalUse, encoding, exampleOfWork, fileFormat, genre, hasPart, headline, inLanguage, interactionStatistic, interactivityType, isBasedOnUrl, isFamilyFriendly, isPartOf, keywords, learningResourceType, license, locationCreated, offers, position, producer, provider, publication, publisher, publishingPrinciples, recordedAt, releasedEvent, review, schemaVersion, sourceOrganization, text, thumbnailUrl, timeRequired, translator, typicalAgeRange, version, video, workExample, cheatCode, discusses, encodesCreativeWork, gameTip, lyrics, recordedIn, sharedContent, softwareHelp, workFeatured, workPerformed, reviewBody, reviewRating, resultReview, broadcastOfEvent, event, firstPerformance, performerIn, subEvent, superEvent, attendee, doorTime, duration, endDate, eventStatus, organizer, performer, previousStartDate, startDate, code, guideline, medicineSystem, recognizingAuthority, relevantSpecialty, study, adverseOutcome, causeOf, estimatesRiskOf, guidelineSubject, increasesRiskOf, seriousAdverseOutcome, studySubject, address, alumni, areaServed, brand, contactPoint, department, dissolutionDate, duns, email, employee, faxNumber, founder, foundingDate, foundingLocation, globalLocationNumber, hasOfferCatalog, hasPOS, isicV4, legalName, logo, makesOffer, member, memberOf, naics, numberOfEmployees, owns, parentOrganization, seeks, subOrganization, taxID, telephone, vatID, acquiredFrom, affiliation, alumniOf, broadcastAffiliateOf, broadcaster, broker, composer, creditedTo, customer, endorsee, endorsers, followee, hiringOrganization, hostingOrganization, issuedBy, landlord, manufacturer, offeredBy, productionCompany, recipient, recordLabel, reviewedBy, seller, sender, serviceOperator, sponsor, underName, worksFor, additionalName, birthDate, birthPlace, children, colleague, deathDate, deathPlace, familyName, follows, gender, givenName, height, homeLocation, honorificPrefix, honorificSuffix, jobTitle, knows, nationality, netWorth, parent, relatedTo, sibling, spouse, weight, workLocation, actor, athlete, awayTeam, borrower, buyer, candidate, coach, competitor, director, homeTeam, illustrator, lender, loser, lyricist, musicBy, opponent, winner, additionaladdress, branchCode, containedInPlace, containsPlace, geo, hasMap, openingHoursSpecification, photo, availableAtOrFrom, dropoffLocation, eligibleRegion, exerciseCourse, foodEstablishment, fromLocation, gameLocation, ineligibleRegion, jobLocation, pickupLocation, regionsAllowed, serviceLocation, spatial, toLocation, isAccessoryOrSparePartFor, isConsumableFor, isRelatedTo, isSimilarTo, itemOffered, itemShipped, orderedItem, productSupported, typeOfGood, serialNumber, additionalaggregateRating, color, depth, gtin12, gtin13, gtin14, gtin8, itemCondition, model, mpn, productID, productionDate, purchaseDate, releaseDate, sku, width, acceptsReservations, isAccessibleForFree, isAvailableGenerically, isGift, isLiveBroadcast, isProprietary, multipleValues, readonlyValue, representativeOfPage, requiresSubscription, value, valueAddedTaxIncluded, valueRequired, commentTime, datePosted, dateVehicleFirstRegistered, expires, guidelineDate, lastReviewed, priceValidUntil, scheduledPaymentDate, uploadDate, validUntil, vehicleModelDate, arrivalTime, availabilityEnds, availabilityStarts, availableFrom, availableThrough, bookingTime, checkinTime, checkoutTime, coverageEndTime, coverageStartTime, datasetTimeInterval, dateDeleted, dateIssued, departureTime, dropoffTime, expectedArrivalFrom, expectedArrivalUntil, modifiedTime, orderDate, ownedFrom, ownedThrough, paymentDueDate, pickupTime, scheduledTime, validFrom, validThrough, webCheckinTime, additionalNumberOfGuests, amount, amountOfThisGood, baseSalary, bestRating, billingIncrement, childMaxAge, childMinAge, costPerUnit, discount, doseValue, elevation, geoRadius, highPrice, latitude, longitude, lowPrice, maxPrice, maxValue, minPrice, minValue, numberOfAirbags, numberOfAxles, numberOfDoors, numberOfForwardGears, numberOfPreviousOwners, numberedPosition, orderQuantity, price, repetitions, screenCount, stageAsNumber, stepValue, strengthValue, suggestedMaxAge, suggestedMinAge, totalPrice, valueMaxLength, valueMinLength, vehicleSeatingCapacity, worstRating, accessCode, accountId, actionPlatform, activeIngredient, activityFrequency, additionalVariable, addressCountry, addressLocality, addressRegion, administrationRoute, aircraft, alcoholWarning, algorithm, alignmentType, applicationCategory, applicationSubCategory, applicationSuite, arrivalGate, arrivalPlatform, arrivalTerminal, artEdition, artMedium, artform, articleBody, articleSection, artworkSurface, aspect, assemblyVersion, associatedPathophysiology, audienceType, availableOnDevice, background, biomechnicalClass, bitrate, boardingGroup, bodyLocation, bookEdition, box, breadcrumb, breastfeedingWarning, broadcastChannelId, broadcastDisplayName, broadcastServiceTier, broadcastTimezone, browserRequirements, busName, busNumber, caption, carrierRequirements, catalogNumber, characterName, circle, clinicalPharmacology, clipNumber, codeSampleType, codeValue, codingSystem, commentText, confirmationNumber, contactType, contentSize, contentType, cookingMethod, costCurrency, costOrigin, countriesNotSupported, countriesSupported, currenciesAccepted, currency, dateline, departureGate, departurePlatform, departureTerminal, dependencies, dietFeatures, discountCode, discountCurrency, dosageForm, doseUnit, driveWheelConfiguration, drugUnit, educationRequirements, educationalFramework, educationalRole, employmentType, encodingFormat, encodingType, epidemiology, episodeNumber, estimatedFlightDuration, evidenceOrigin, executableLibraryName, exerciseType, exifData, expectedPrognosis, experienceRequirements, expertConsiderations, featureList, fileSize, flightDistance, flightNumber, followup, foodWarning, frequency, fuelType, function, functionalClass, howPerformed, httpMethod, iataCode, icaoCode, incentiveCompensation, industry, infectiousAgent, intensity, isbn, isrcCode, issn, issueNumber, iswcCode, itemListOrder, jobBenefits, knownVehicleDamages, line, lodgingUnitDescription, lodgingUnitType, mealService, mechanismOfAction, membershipNumber, memoryRequirements, menu, muscleAction, musicCompositionForm, musicalKey, naturalProgression, nonProprietaryName, normalRange, occupationalCategory, openingHours, operatingSystem, orderItemNumber, orderNumber, outcome, overdosage, overview, pageEnd, pageStart, pagination, passengerPriorityStatus, passengerSequenceNumber, pathophysiology, paymentAccepted, paymentMethodId, paymentStatus, permissions, phase, physiologicalBenefits, playerType, polygon, population, possibleComplication, postOfficeBoxNumber, postOp, postalCode, preOp, pregnancyWarning, preparation, priceCurrency, priceRange, priceType, printColumn, printEdition, printPage, printSection, procedure, processorRequirements, proficiencyLevel, programName, programmingModel, propertyID, proprietaryName, providerMobility, publicationType, qualifications, query, ratingValue, recipeCategory, recipeCuisine, recipeIngredient, recipeInstructions, recipeYield, recommendationStrength, releaseNotes, reportNumber, requiredGender, reservationId, responsibilities, restPeriods, risks, roleName, runtimePlatform, safetyConsideration, salaryCurrency, seasonNumber, seatNumber, seatRow, seatSection, seatingType, securityScreening, servesCuisine, serviceType, servingSize, significance, skills, softwareRequirements, softwareVersion, specialCommitments, sport, storageRequirements, streetAddress, strengthUnit, structuralClass, subStageSuffix, subtitleLanguage, subtype, suggestedGender, targetDescription, targetName, targetPlatform, targetPopulation, tickerSymbol, ticketNumber, ticketToken, tissueSample, title, trackingNumber, trainName, trainNumber, transcript, transmissionMethod, unitCode, unitText, urlTemplate, valueName, valuePattern, vehicleConfiguration, vehicleIdentificationNumber, vehicleInteriorColor, vehicleInteriorType, vehicleTransmission, videoFormat, videoFrameSize, videoQuality, volumeNumber, warning, workHours, itemscope, itemtype, itemprop',
            'allowed_inline_css_properties' => 'min-height, max-height, max-height, min-height, display, float',
        ),
        'product_filters'       => array(
            'black_list'     => array(
                'asin'        => '',
                'title'       => '',
                'description' => '',
            ),
            'white_list'        => array(
                'asin'        => '',
                'title'       => '',
                'description' => '',
            ),
            'case_sensitive' => 0,
            'no_duplicate'   => 0,    // in 2.0.5.1 changed to 0 from 1.
        ),
        'support' => array(
            'rate'   => 0,            // asked for the first load of the plugin admin page
            'ads'    => false,        // asked for the first load of the plugin admin page
            'review' => 0,            // not implemented yet
            'agreed' => false,        // hidden
        ),
        'cache'    =>    array(

            // 'caching_method'                   => 'database', // 3.12.0 Not implemented yet
            'caching_mode'                     => 'normal',
            
            // 3.4.0+
            'expired_cache_removal_interval'   => array(
                'size'  => 7,
                'unit'  => 86400,   // either 3600, 86400, or 604800
            ),

            // 3.8.12+
            'cache_removal_event_last_run_time' => '',

            // 3.7.3+
            'table_size' => array(
                'products' => '',   // (string|integer) blank string for unlimited. For integer values, mega bytes.
                'requests' => '',   // (string|integer) blank string for unlimited. For integer values, mega bytes.
            ),

            // 4.0.0+
            'compress'  => false, // (boolean) whether to compress caches
        ),
        'query' => array(
            'cloak' => 'productlink'
        ),
        'authentication_keys' => array(
            'access_key'                => '',   // public key
            'access_key_secret'         => '',   // private key
            'api_authentication_status' => false,
            'associates_test_tag'       => '',  // 3.6.7+
            'server_locale'             => 'US', // 3.4.4+
        ),            
        // Hidden options
        'template' => array(
            'max_column' => 1,
        ),
        'import_v1_options' => array(
            'dismiss' => false,
        ),
        // 2.2.0+
        'unit_preview'      => array(
            'preview_post_type_label' => AmazonAutoLinks_Registry::NAME,
            'preview_post_type_slug'  => '',
            'visible_to_guests'       => true,
            'searchable'              => false,
        ),
        
        // 3+
        'reset_settings'    => array(
            'reset_on_uninstall'    => false,
        ),
        
        // 3.1.0+
        'external_scripts'  => array(
            'impression_counter_script' => false,
        ),
        
        // 3+ Changed the name from `arrTemplates`.
        // stores information of active templates.   
        // 'templates' => array(),    
    
        // 3.3.0+
        'feed'  => array(
            'use_description_tag_for_rss_product_content' => false,
        ),

        // 3.8.0
        'convert_links' => array(
            'enabled'               => false,
            'where'                 => array(
                'the_content'  => 1,
                'comment_text' => 1,
            ),
            'filter_hooks'   => '',
        ),

        // 3.9.0+
        'widget'    => array(
            'register'    => array(
                'contextual'    => true,
                'by_unit'       => true,
            )
        ),

        // 3.11.0+
        'aalb'      => array(
            'support' => 0,
            'template_conversion_map' => array(),
        ),

        // 4.0.0
        'custom_oembed' => array(
            'enabled'               => true,
            'use_iframe'            => true,
            'external_provider'     => '',
            'override_associates_id_of_url' => false,
            'template_id'           => null,            // (string) will be set via the UI
        ),
    
        // 3.4.0+
        'unit_default'  => array(
            'unit_type'                     => null,
            'unit_title'                    => null,
            'cache_duration'                => 86400,  // 60*60*24
            
            'count'                         => 10,
            'column'                        => 4,
            'country'                       => 'US',
            'associate_id'                  => null,
            'image_size'                    => 160,      
            'ref_nosim'                     => false,
            'title_length'                  => -1,
            'description_length'            => 250,     // 3.3.0+  Moved from the search unit types.
            'link_style'                    => 1,
            'credit_link'                   => 0,   // 1 or 0   // 3.5.3+ disabled by default
            'credit_link_type'              => 0,   // 3.2.2+ 0: normal, 1: image

        // @todo not sure about this         
        'title'                 => '',      // won't be used to fetch links. Used to create a unit.
            
            'template'              => '',      // the template name - if multiple templates with a same name are registered, the first found item will be used.
            'template_id'           => null,    // the template ID: md5( dir path )
            'template_path'         => '',      // the template can be specified by the template path. If this is set, the 'template' key won't take effect.
            
            'is_preview'            => false,   // for the search unit, true won't be used but just for the code consistency. 
                        
            // stores labels associated with the units (the plugin custom taxonomy). Used by the RSS2 template.
            '_labels'               => array(),    
            
    // this is for fetching by label. AND, IN, NOT IN can be used
    'operator'              => 'AND',   

            
            // 3+
            'subimage_size'                 => 100,
            'subimage_max_count'            => 5,
            'customer_review_max_count'     => 2,
            'customer_review_include_extra' => false,
            
            'button_id'                     => null, // a button (post) id will be assigned
            // 3.1.0+
            'button_type'                   => 1,   // 0: normal link, 1: add to cart
            
            'product_filters'               => array(
                'white_list'    => array(
                    'asin'          => '',
                    'title'         => '',
                    'description'   => '',
                ),
                'black_list'    => array(
                    'asin'          => '',
                    'title'         => '',
                    'description'   => '',
                ),
                'case_sensitive'    => 0,   // or 1
                'no_duplicate'      => 0,   // or 1
            ),
            // 3.1.0+
            'skip_no_image'               => false,
           
           
            'width'         => null,
            'width_unit'    => '%',
            'height'        => null,
            'height_unit'   => 'px',
            
            // Whether to show an error message.
            // When an error occurs, the error message will be shown and the template is not applied.
            // Currently, no unit option meta box input field for this option.
            'show_errors'   => true,

            // 3.2.0+
            'show_now_retrieving_message'   => true,
     
            // 3.2.1+
            '_allowed_ASINs' => array(),
            
            // 3.3.0+
            'highest_content_heading_tag_level' => 5,
            
            // 3.3.0+ (boolean) Whether to fetch similar products. The background routine of retrieving similar products need to set this `false`.
            '_search_similar_products'      => true,        

            // @deprecated 3.9.0    PA-API 5 does not support similarity look-up
            'similar_product_image_size'    => 100,
            'similar_product_max_count'     => 10,
            
            'description_suffix'            => 'read more',

            // 3.5.0+
            '_force_cache_renewal'          => false,

            '_no_pending_items'             => false,
            '_filter_adult_products'        => false,   // 3.9.0+
            '_filter_by_rating'             => array(
                'enabled'   => false,
                'case'      => 'above',
                'amount'    => 0,
            ),
            '_filter_by_discount_rate'      => array(
                'enabled'   => false,
                'case'      => 'above',
                'amount'    => 0,
            ),

            // unknown+
            '_no_outer_container'           => false,

            // 3.6.0+
            'load_with_javascript'          => false,
            '_now_loading_text'             => 'Now loading...',
            /// for widget outputs - helps the output function know what to do with JavaScript loading
            '_widget_option_name'           => null,
            '_widget_number'                => null,

            // 3.7.0+ These are not options that change the output behavior but post meta to store relevant information based on the result.
            // 3.7.7  The default value became an empty string and become `normal` for loaded units without an error.
            '_error'                        => null,

            // 3.7.5+
            '_custom_url_query_string'      => array(),

            // 3.10.0
            '_filter_by_prime_eligibility'  => false,
            '_filter_by_free_shipping'      => false,
            '_filter_by_fba'                => false,
            'preferred_currency'            => null,
            'language'                      => null,

            // 4.0.0
            'output_formats'                => array(), // (array) holds item_format, image_format, title_format for each active template
        )
        
    );
       
    /**
     * Set up default options values.
     * 
     * Deals with default option items that need to call functions.
     */
    public function __construct( $sOptionKey ) {
                
        $this->aDefault[ 'unit_default' ][ 'description_suffix' ] = __( 'read more', 'amazon-auto-links' );
        
        // The `$this->aOptions` property will be established.
        parent::__construct( $sOptionKey );
        
        // After `this->aOptions` is created. Set the default Item Format option.
        $this->aOptions[ 'unit_default' ] = $this->aOptions[ 'unit_default' ] 
            + $this->getDefaultItemFormat();  // needs to check API is connected
        
    }
       
    /**
     * Returns the instance of the class.
     * 
     * This is to ensure only one instance exists.
     * 
     * @since      3
     * @return     AmazonAutoLinks_Option
     */
    static public function getInstance( $sOptionKey='' ) {
        
        $sOptionKey = $sOptionKey 
            ? $sOptionKey
            : AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ];
        
        if ( isset( self::$aInstances[ $sOptionKey ] ) ) {
            return self::$aInstances[ $sOptionKey ];
        }
        $_sClassName = apply_filters( 
            AmazonAutoLinks_Registry::HOOK_SLUG . '_filter_option_class_name',
            __CLASS__ 
        );
        self::$aInstances[ $sOptionKey ] = new $_sClassName( $sOptionKey );
        return self::$aInstances[ $sOptionKey ];
        
    }         
            
            
    /**
     * 
     * @remark      The array contains the concatenation character(.) 
     * so it cannot be done in the declaration.
     * @since       unknown
     * @since       3.4.0       Moved from `AmazonAutoLinks_UnitOption_Base`. Removed the static scope.
     * @return      array
     */
    public function getDefaultItemFormat() {

        $_bAPIConnected = $this->isAPIConnected();
        return array(
            'item_format' => $_bAPIConnected
                ? $this->getDefaultItemFormatConnected()
                : $this->getDefaultItemFormatDisconnected(),
            'image_format' => '<div class="amazon-product-thumbnail" style="max-width:%max_width%px; max-height:%max_width%px; ">' . PHP_EOL
                . '    <a href="%href%" title="%title_text%: %description_text%" rel="nofollow noopener" target="_blank">' . PHP_EOL
                . '        <img src="%src%" alt="%description_text%" style="max-height:%max_width%px;" />' . PHP_EOL
                . '    </a>' . PHP_EOL
                . '</div>',
                
            'title_format' => '<h5 class="amazon-product-title">' . PHP_EOL
                . '<a href="%href%" title="%title_text%: %description_text%" rel="nofollow noopener" target="_blank">%title_text%</a>' . PHP_EOL
                . '</h5>',    
                
        );
        
    }

    /**
     * @since   3.8.0
     * @return  string
     */
    public function getDefaultItemFormatConnected() {
        return '<div class="amazon-auto-links-product">' . PHP_EOL
            . '    <div class="amazon-auto-links-product-image">' . PHP_EOL
            . '        %image%' . PHP_EOL
            . '        %image_set%' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '    <div class="amazon-auto-links-product-body">' . PHP_EOL
            . '        %title%' . PHP_EOL
            . '        %rating% %prime% %price%' . PHP_EOL
            . '        %description%' . PHP_EOL
            . '        %disclaimer%' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '</div>';
    }
    /**
     * @since   3.8.0
     * @return  string
     */
    public function getDefaultItemFormatDisconnected() {
        return '<div class="amazon-auto-links-product">' . PHP_EOL
            . '    <div class="amazon-auto-links-product-image">' . PHP_EOL
            . '        %image%' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '    <div class="amazon-auto-links-product-body">' . PHP_EOL
            . '        %title%' . PHP_EOL
            . '        %rating% %prime% %price%' . PHP_EOL
            . '        %description%' . PHP_EOL
            . '        %disclaimer%' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '</div>';
    }
            
    /**
     * @return      boolean
     */
    public function isUnitLimitReached( $iNumberOfUnits=null ) {
        
        if ( ! isset( $iNumberOfUnits ) ) {
            $_oNumberOfUnits = AmazonAutoLinks_WPUtility::countPosts( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );
            $iNumberOfUnits  = $_oNumberOfUnits->publish 
                + $_oNumberOfUnits->private 
                + $_oNumberOfUnits->trash;
        } 
        return ( boolean ) ( $iNumberOfUnits >= 3 );
        
    }    
    public function getRemainedAllowedUnits( $iNumberOfUnits=null ) {
        
        if ( ! isset( $iNumberOfUnits ) ) {
            $_oNumberOfUnits   = AmazonAutoLinks_WPUtility::countPosts( 
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            );
            $iNumberOfUnits   = $_oNumberOfUnits->publish 
                + $_oNumberOfUnits->private 
                + $_oNumberOfUnits->trash;
        } 
        
        return 3 - $iNumberOfUnits;
        
    }
    public function isReachedCategoryLimit( $iNumberOfCategories ) {
        return ( boolean ) ( $iNumberOfCategories >= 3 );
    }    
    public function getMaximumProductLinkCount() {
        return 10;
    }    
    public function getMaxSupportedColumnNumber(){
        return apply_filters( 
            'aal_filter_max_column_number', 
            $this->aOptions[ 'template' ][ 'max_column' ]
        );                    
    }
    
    public function isAdvancedAllowed() {
        return false;
    }
    
    public function canExport() {
        return false;
    }
    public function isSupported() {
        return false;
    }
    
    /**
     * @since       3.3.0
     * @return      boolean
     */
    public function canCloneUnits() {
        return false;
    }

    /**
     * @since       3.7.5
     * @return      boolean
     */
    public function canAddQueryStringToProductLinks() {
        return false;
    }
    
    /**
     * Checks whether the API keys are set and it has been verified.
     * @since       3
     * @return      boolean
     */
    public function isAPIConnected() {
        return ( boolean ) $this->get( 
            'authentication_keys', 
            'api_authentication_status' 
        );
    }

    /**
     * Checks whether the API keys are set.
     *
     * This is not checking the connectivity as connectivity can vary even the keys are set
     * such as when too many requests are made.
     *
     * @param   string      $sLocale        The locale to check. If this value is given, the method checks if the PA-API keys are set for this locale
     * and returns false if the keys are not for that locale.
     * @return  boolean
     * @since   3.9.2
     * @since   4.0.1       Added the `$sLocale` parameter.
     */
    public function isAPIKeySet( $sLocale='' ) {

        $_sPublicKey = $this->get( 'authentication_keys', 'access_key' );
        $_sSecretKey = $this->get( 'authentication_keys', 'access_key_secret' );

        if ( ! $sLocale ) {
            return ( boolean ) ( $_sPublicKey && $_sSecretKey );
        }

        // At this point, the locale is specified.
        $_sStoredLocale = strtoupper( ( string ) $this->get( 'authentication_keys', 'server_locale' ) );
        $sLocale        = strtoupper( ( string ) $sLocale );
        return $sLocale === $_sStoredLocale;

    }
    
    /**
     * Checks whether the plugin debug mode is on.
     * @return      boolean
     */
    public function isDebug() {
        // @deprecated 3.10.0
//        if ( ! self::isDebugModeEnabled() ) {
//            return false;
//        }
        return ( boolean ) $this->get( 
            'debug', 
            'debug_mode' 
        );
    }
    
    /**
     * 
     * @since       2.2.0
     * @return      boolean
     */
    public function isCustomPreviewPostTypeSet()  {
        
        $_sPreviewPostTypeSlug = $this->get( 'unit_preview', 'preview_post_type_slug' );
        if ( ! $_sPreviewPostTypeSlug ) {
            return false;
        }
        return AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] !== $_sPreviewPostTypeSlug;
        
    }    
    
    /**
     * 
     * @since       2.2.0
     * @return      boolean
     */
    public function isPreviewVisible() {
        
        if ( $this->get( 'unit_preview', 'visible_to_guests' ) ) {
            return true;
        }
        return ( boolean ) is_user_logged_in();
        
    }

    /**
     * @return bool
     * @since   3.5.0
     */
    public function isAdvancedProductFiltersAllowed() {
        return false;
    }
    
}