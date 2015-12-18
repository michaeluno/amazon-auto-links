<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
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
    // public $aDefault = array();
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
            'chaching_mode' => 'normal',
        ),
        'query' => array(
            'cloak' => 'productlink'
        ),
        'authentication_keys' => array(
            'access_key'                => '',  // public key
            'access_key_secret'         => '',  // private key
            'api_authentication_status' => false,
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
            'preview_post_type_slug' => '',
            'visible_to_guests'      => true,
            'searchable'             => false,
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
    
    );
         
    /**
     * Returns the instance of the class.
     * 
     * This is to ensure only one instance exists.
     * 
     * @since      3
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
            
    /* Plugin specific methods */    
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
     */
    public function canCloneUnits() {
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
     * Checks whether the plugin debug mode is on.
     * @return      boolean
     */
    public function isDebug() {
        if ( ! self::isDebugModeEnabled() ) {
            return false;
        }                
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
    
    
}