<?php
/**
    Decodes images.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @since        2.0.0
*/

if ( ! class_exists( 'IXR_Message' ) ) require_once( ABSPATH . WPINC . '/class-IXR.php' );
class AmazonAutoLinks_ImageHandler extends IXR_Message {

    /*
     * Caches images in the database and the browser. The supported image types are jpeg, png, and gif.
     * This class does not use the option object. The passed encoded image contains all the necessary information inside the decoded string.
     * So the all options should be set up in the encoder.
     * */
     
    // Properties for reference
    public $arrMimeTypesWithHex = array( 
        "image/jpeg" => "FFD8", 
        "image/png" => "89504E470D0A1A0A", 
        "image/gif" => "474946",
        "image/bmp" => "424D", 
        "image/tiff" => "4949",
        "image/tiff" => "4D4D"
    );
    public $arrMimeTypesWithExtension = array( 
        "jpg"     => "image/jpeg" ,
        'jpeg'    => "image/jpeg" ,
        "png"     => "image/png",
        "gif"    => "image/gif",
        "bmp"    => "image/bmp",
        "tiff"    => "image/tiff",
    );    
    protected $arrDefaultImageData = array(
        'size'             => null,        // stores the size of the image
        'mime_type'     => null,        // stores the image type
        'data'            => null,        // stores the image data
        'mod'            => null,        // stores the modified data of the transient
        'url'            => null,        // stores the source image url
        'gzcompress'    => null,
    );
    // protected $arrSupportedFileTypes = array(
        // 'png',
        // 'jpeg',
        // 'jpg',
        // 'gif',        
    // );
    
    // Dynamic properties
    protected $strImageURL;        // stores the image url to deal with
    protected $strMimeType;        // stores the image's mime type. Storing it as object property enables the user to manually set the mime type.
    protected $numRenewTime;    // stores the renewal interval for registering the cache renewal schedule event.

    protected $strTransientPrefix_Image = '_IMG_';            // sets the transient string for the image data.
    protected $strTransientPrefix_ImageInfo = '_IMGINFO';        // sets the transient string for the image file info array.
    protected $strCacheRenewEventActionName = '_action_renew_image_cache';
    protected $numCacheExpirationInterval = 43200;    // in seconds, equals to one day. Sets the cache expiration interval.
    protected $numMaxSizeToCache = 2600000;    // in bytes. 2600 kilobytes, 2.6 mb.
    
    // Dynamic properties - Flags
    protected $bAllowGZCompress; // stores whether gzcompress is allowed or not.
    
    function __construct( $strTransientPrefix ) {
        
        $this->strTransientPrefix_Image = $strTransientPrefix . '_IMG_';            // sets the transient string for the image data.
        $this->strTransientPrefix_ImageInfo = $strTransientPrefix. '_IMGINFO';        // sets the transient string for the image file info array.
        $this->strCacheRenewEventActionName = $strTransientPrefix . '_action_renew_image_cache';
        
    }    
    /*
     * Property handlers
     * */
    public function SetMaxSizeToCache( $numBytes ) {
        
        $this->numMaxSizeToCache = $numBytes;
        
    }
    public function EnableGZCompress( $bEnable ) {
        
        $this->bAllowGZCompress = ( ! function_exists('gzcompress') ) ? false : $bEnable;
        
    }
    public function SetTransientPrefix( $strTransientPrefix ) {
        
        // The string length must be upto 4 characters or less (45 minus 32 minus 8) 
        // because a md5 hash uses 32 characters and the option table column allows maximim of 45 character length
        // Moreover, this class uses the IMG_ and IMGINFO_ prefixes and IMGINFO_ has 8 characters.
        
        // allows up to 4 characters
        $this->strTransientPrefix_Image = substr( $strTransientPrefix, 0, 4 ) . 'IMG_';        
        $this->strTransientPrefix_ImageInfo = substr( $strTransientPrefix, 0, 4 ) . 'IMGINFO_';
        
    }
    public function SetExpirationInterval( $numSeconds ) {
        
        $this->numCacheExpirationInterval = $numSeconds;
    
    }
    
    /*
     * Renderer
     * */
    public function draw( $strEncodedURL ) {
        
        // $this->SetMaxSizeToCache( 2600 * 1000 );    // bytes
        // Get the actual url
        $strURL = $this->getSourceImageLocation( $strEncodedURL );

        $this->SetExpirationInterval( 60*60*24 );    // 60 seconds ( one minite ) x 60 times x 24 times; one day
        
        $this->SetImageURL( $strURL );
        // if ( $arrQuery['extension'] )
            // $this->SetMimeType( $arrQuery['extension'] ); // SetTransient() will automatically look for the mime type if not set.
        // $this->EnableGZCompress( $arrGeneralOptions['allow_gzcompress'] );    // if the value is false, it will disable.
            
        $arrImageData =  $this->GetTransient();
        if ( ! $arrImageData ) {
            
            if ( ! $arrImageData = $this->SetTransient() ) 
                die( __( 'The image data is empty.', 'amazon-auto-links' ) );
            
        }
        
        // if ( $arrQuery['showcode'] && !empty( $arrGeneralOptions['debug_mode'] ) )
            // die( '<pre>' . print_r( $arrImageData ). '</pre>' );
            
        $this->RenderImageBuffer( $arrImageData );
                            
        
    }
    function SetImageURL( $strURL, $bIsBase64Encoded=false ) {
                
        $this->strImageURL = $bIsBase64Encoded ? $this->getSourceImageLocation( $strURL ) : $strURL;
        
    }
    function SetMimeType( $strExtension ) {
        
        if ( array_key_exists( strtolower( $strExtension ), $this->arrMimeTypesWithExtension ) )
            $this->strMimeType = $this->arrMimeTypesWithExtension[ $strExtension ];
        
    }
    function RenderImageBuffer( $arrImageData, $bRenew=False ) {
            
        // Avoid undefined keys warnings.
        $arrImageData = $arrImageData + $this->arrDefaultImageData;    
            
        // Suppress the PHP error for the GD library related functions.
        if ( function_exists('ini_set') )
            ini_set( 'gd.jpeg_ignore_warning', 1 );
        
        // Prepare the image resource
        switch ( $arrImageData['mime_type'] ) {
            case 'image/jpeg':
            case 'image/gif':
            case 'image/png':
                // Decode the image data
                $arrImageData['data'] = $this->alterBase64( $arrImageData['data'] );
                $arrImageData['data'] = $arrImageData['gzcompress'] ? gzuncompress( $arrImageData['data'] ) : $arrImageData['data'];    // the function existence check has been performed in GetTransient() at this point
                $hImage = @imagecreatefromstring( $arrImageData['data'] );
               if ( error_reporting() === 0 ) {
                    // do something if an error occures
                    die( 'Error occurred while loading the image.' );
               }                
            break;
            default: 
                echo 'Not supported mime type: ' . $arrImageData['mime_type'] . '<br />';
                echo 'Image url: ' . $arrImageData['mime_type'];
            exit;
        }    
        
        // We'll capture the output buffer into a varible so that the lenglth can be measured.
        // Othersize, since the output buffer will be alpha-modified, for gif and png, the size is unknown ( could be manually calculated ).
        ob_start(); 
        switch ( $arrImageData['mime_type'] ) {
            case 'image/gif':
                // Support the alpha channel.
                imageAlphaBlending( $hImage, true );
                imageSaveAlpha( $hImage, true );    
                imagegif( $hImage );
            break;
            case 'image/png':
                imageAlphaBlending( $hImage, true );
                imageSaveAlpha( $hImage, true );        
                imagepng( $hImage );    // third param: compressing level 0 to 9
                // imagepng( $hImage, null, 8 );    // third param: compressing level 0 to 9
            break;
            case 'image/jpeg':
                // imagejpeg( $hImage );    // third param: quality in percentage 0 to 100%
                imagejpeg( $hImage, null, 90 );    // third param: quality in percentage 0 to 100%
            break;        
        }    
        $strOutputBuffer = ob_get_contents();
        ob_end_clean(); //clear buffer        

        $numSize = strlen( $strOutputBuffer );
                
        // Send the header 
        // Cache the image in the browser 
        header( "Content-Type: {$arrImageData['mime_type']}" );
        header( "Last-Modified: " . gmdate( 'D, d M Y H:i:s',  $arrImageData['mod'] ) . " GMT" );
        
        // header( "Content-Length: " . $arrImageData['size'] ); // <-- the GD library compresses the iamge and the size depends on the compress level. So it cannot tell the size before creating it.
        header( "Cache-Control: max-age=" . ( $bRenew ? 0 : $this->numCacheExpirationInterval ) );
        $numExpires = $bRenew ? time() : $arrImageData['mod'] + $this->numCacheExpirationInterval;
        header( "Expires: " . gmdate( "D, d M Y H:i:s", $numExpires ) . "GMT" );
        
        // if gzcompress is allowed        
        $strEncoding = $this->GetGzipEncodingType();
        if ( $numSize >= 2048 && $strEncoding ) {
    
            header( 'Content-Encoding: ' . $strEncoding ); 
            print( "\x1f\x8b\x08\x00\x00\x00\x00\x00" ); 
            $strOutputBuffer = gzcompress( $strOutputBuffer, 9 ); 
            $numSize = strlen( $strOutputBuffer );
            // $strOutputBuffer = substr( $strOutputBuffer, 0, $numSize ); 
    
        }        
        @header( "Content-Length: " . $numSize );    // sometimes the header already sent error occurs. So disable the error report.
        
        // Send the contents
        echo $strOutputBuffer;

        imagedestroy( $hImage );        
        
    }    
    protected function GetGzipEncodingType() {
        
        if ( ! $this->bAllowGZCompress ) return false;
        
        if ( strpos( $_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip' ) !== false ) 
            return 'x-gzip'; 
            
        if( strpos( $_SERVER["HTTP_ACCEPT_ENCODING"],'gzip' ) !== false ) 
            return 'gzip'; 
            
    }
    
    /*
     * Cache renewal events
     * */
    public function RenewCache( $strURL ) {
        /*
         * Hook this method with add_action( $this->strCacheRenewEventActionName, array( new PseudoImage_Encoder, 'RenewCache' ) );
         * where $this->strCacheRenewEventActionName is the action name set in this class.
         * And the line MUST be loaded automatically by WordPress when one one the page of the site gets loaded; do not place the line
         * in classes which are called by the autoloader or something. It's recommended to place the line in the plugin's main file to be safe.
         * 
         * */
        $strURL = $this->IsBase64( $strURL ) ? $this->getSourceImageLocation( $strURL ) : $strURL;
        $this->SetTransient( $strURL );
        
        
    }
    function SetCacheRenewalEventName( $strActionName ) {
        
        $this->strCacheRenewEventActionName = $strActionName;
        
    }     
    public function ScheduleCacheRenewal() {
        
        // Giving a random delay prevents multiple tasks from running at the same time and causing the page load slow down.
        // WP Cron runs in the background; however, if the registered tasks takes the server resources too much such as CPU usage, the loading page takes some time to complete.
        // + rand( 5, 20 )
        if ( wp_next_scheduled( $this->strCacheRenewEventActionName, array( $this->strImageURL ) ) ) 
            return;
        
        // Delete the transient so that the event method can check whether it really needs to be renewed or not.
        // foreach( ( array ) $this->vSetURL as $strURL ) 
            // AmazonAutoLinks_WPUtilities::deleteTransient( $this->strRealCacheModTimePrefix . md5( $strURL ) );
        
        wp_schedule_single_event( time() + $this->numRenewTime, $this->strCacheRenewEventActionName, array( $this->strImageURL ) );

    }     

    /*
     * Transients
     * */
    public function GetTransient( $strURL='' ) {
        
        /*
         *  Retrieves the cached image from the database and returns the data array.
         */
        
        $strURL = empty( $strURL ) ? $this->strImageURL : $strURL;
        $strURL = $this->sanitizeScheme( $strURL );
        
        // Get the image data array.
        $arrImageData = AmazonAutoLinks_WPUtilities::getTransient( $this->strTransientPrefix_Image . md5( $strURL ) );
        if ( 
            ! is_array( $arrImageData )
            ||   ! isset( $arrImageData['mime_type'] ) 
            || ! isset( $arrImageData['size'] ) 
            || ! isset( $arrImageData['mod'] ) 
            || ! isset( $arrImageData['data'] ) 
            || ! isset( $arrImageData['url'] )
            || ! isset( $arrImageData['gzcompress'] )
        ) 
            return null;
        
        // Check if the transient expires soon 
        $numRemaindedSeconds = $arrImageData['mod'] + $this->numCacheExpirationInterval - time();
        if ( round( $this->numCacheExpirationInterval * 0.2 ) > $numRemaindedSeconds ) {
            
            // The remained seconds exeeded more than 80% of the expiration interval seconds.
            // Set the scheduling task at the end of the script.
            add_action( 'shutdown', array( $this, 'ScheduleCacheRenewal' ) );
            $this->SetImageURL( $strURL );
            $this->numRenewTime = round( ( $arrImageData['mod'] + $this->numCacheExpirationInterval - time() ) * 0.9 );
        
        }
            
        // In case the user's server setting for gzcompress has been changed and become not available, 
        // yet the data is compressed, renew ( refetch and recreate ) the data.
        if ( ! function_exists( 'gzuncompress' ) && $arrImageData['gzcompress'] ) 
            $arrImageData = $this->SetTransient( $strURL );        
            
        // Return the image data array.
        return $arrImageData;
        
    }
    public function SetTransient( $strURL='' ) {
                
        // Set up the url
        $strURL = empty( $strURL ) ? $this->strImageURL : $strURL;
        $strURL = $this->getSourceImageLocation( $strURL, $this->IsBase64( $strURL ), True );
        
        // Fetch the imaga data.
        $arrRequest = wp_remote_request( 
            $strURL, 
            array( 
                'sslverify' => false,
                'timeout' => 15,
            ) 
        );
        // Error checking to prevent PHP Fatal error:  Cannot use object of type WP_Error as array 
        if ( is_wp_error( $arrRequest ) ) {
            
            $strData = @file_get_contents( $strURL );
            $numSize = strlen( $strData );
            $strMimeType = $this->strMimeType ? $this->strMimeType : $this->GetImageMimeType( $strURL, $strData );
            
        } else {
            
            $strData = $arrRequest['body'];
            $numSize = isset( $arrRequest['headers']['content-length'] ) && $arrRequest['headers']['content-length'] ? $arrRequest['headers']['content-length'] : strlen( $strData );                
            $strMimeType = isset( $arrRequest['headers']['content-type'] ) && $arrRequest['headers']['content-type'] ? $arrRequest['headers']['content-type'] : ( $this->strMimeType ? $this->strMimeType : $this->GetImageMimeType( $strURL, $strData ) );
            
        }
        // $strData = is_wp_error( $arrRequest ) ? @file_get_contents( $strURL ) : $arrRequest['body'];
        // $numSize = isset( $arrRequest['headers']['content-length'] ) && $arrRequest['headers']['content-length'] ? $arrRequest['headers']['content-length'] : strlen( $strData );
        // $strMimeType = isset( $arrRequest['headers']['content-type'] ) && $arrRequest['headers']['content-type'] ? $arrRequest['headers']['content-type'] : ( $this->strMimeType ? $this->strMimeType : $this->GetImageMimeType( $strURL, $strData ) );
        if ( ! $strData ) return null;

        // Encode the data 
        $numCompressLevel = 0;
        if ( $this->bAllowGZCompress && function_exists( 'gzcompress' ) ) {
            $numCompressLevel = 9;
            $strData = gzcompress( $strData , $numCompressLevel );    // nine is the maximum compress level.
        }
        
        // Set up the image data array
        $arrData = array( 
            'data' => base64_encode( $strData ),
            'mod' => time(), 
            'mime_type' => $strMimeType,
            'size' => $numSize,
            'url'    => $strURL,
            'gzcompress' => $numCompressLevel,
        );
        
        // If the size is greater than the allowed size, do not cache and return the result.
        if ( strlen( $arrData['data'] ) > $this->numMaxSizeToCache ) 
            return $arrData;
            
        // Store the transients.
        $bSucceed = AmazonAutoLinks_WPUtilities::setTransient( 
            $this->strTransientPrefix_Image . md5( $strURL ), 
            $arrData,
            $this->numCacheExpirationInterval * 100        // 100 times greater than the original duration so that it won't varnish by itself.
        );
        if ( ! $bSucceed ) return null;
        
        return $arrData;
        
    }
    
    /*
     * String <--> Binary Decoder
     * */
    function IsBase64( $str ) {
        
        // by Denis Casanuova
        // http://stackoverflow.com/questions/4278106/how-to-check-if-a-string-is-base64-valid
        return (bool) preg_match( '/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $str );

    }     
    function alterBase64( $bin ) {

        // Some over-sensitive users have hysterical allergy against the base64 decode function so avoid using that. 
        // Instead, use the code of the core. I don't get why we should not use it in plugins while the core is using it. 
    
        $this->params = array();    // make sure it's empty
        $this->_currentTagContents = $bin;
        $this->tag_close( '', 'base64' );
        return $this->params[0];
        
    }
    public function getSourceImageLocation( $strLocation, $bIsEncoded=True, $bForceHttp=True ) {

        // The location may be a url or a path.
        $strDecodedLocation = $bIsEncoded ? $this->alterBase64( $strLocation ) : $strLocation;
        
        if ( filter_var( $strDecodedLocation , FILTER_VALIDATE_URL ) )    // if it is a url
            return $bForceHttp ? $this->sanitizeScheme( $strDecodedLocation ) : $strDecodedLocation;
            
        if ( file_exists( $strDecodedLocation ) )
            return $strDecodedLocation;

    }    
    
    function sanitizeScheme( $strURL ) {
        
        // if ( $this->IsHttpsSupporeted() ) return $strURL
        return preg_replace( "/^https:/", "http:", $strURL );
        
    }    

    /*
     * Image manipulation
     * */
    function GetImageMimeType( $strURL, $binData=null )    {

        // Check from the buffer 
        if ( $binData ) {
                    
            // Try reading it directly
            foreach( $this->arrMimeTypesWithHex as $strMime => $hexBytes ) {
                
                $bytes = $this->GetBytesFromHexString( $hexBytes );
                if ( substr( $binData, 0, strlen( $bytes ) ) == $bytes )
                    return $strMime;
                
            }    
            
            // Try with finfo_open
            if ( function_exists( 'finfo_open' ) ) {
                
                $f = finfo_open();
                $strContentType = finfo_buffer( $f, $binData, FILEINFO_MIME_TYPE );
                if ( ! empty( $strContentType ) ) return $strContentType;
                
            }            
            
        }

        // Try check from the header
        $arrHeaders = @get_headers( $strURL , 1 );
        $strContentType = $arrHeaders['Content-Type'];
        if ( ! empty( $strContentType ) ) return $strContentType;

        // Copy it to local
        $strTmpPath = tempnam( sys_get_temp_dir() , "IMG_" );
        file_put_contents( $strTmpPath, $binData );
        
        // Credit: Gordon
        // http://stackoverflow.com/questions/1965689/php-gd-finding-image-resource-type
        
        // Try with finfo
        if ( class_exists( 'finfo' ) ) {
            
            $fi = new finfo( FILEINFO_MIME );
            $strContentType = $fi->file( $strTmpPath );
            if ( ! empty( $strContentType ) ) {
                unlink( $strTmpPath );
                return $strContentType;
            }
            
        }
        
        // Try with getimagesize()
        $arrImageInfo = function_exists( 'getimagesize' ) ? getimagesize( $strTmpPath ) : null;
        $strContentType = isset( $arrImageInfo['mime'] ) ? $arrImageInfo['mime'] : null;
        if ( ! empty( $strContentType ) ) {
            unlink( $strTmpPath );
            return $strContentType;
        }

        // Try with exif_imagetype()
        $strContentType = function_exists( 'exif_imagetype' ) ? exif_imagetype( $strTmpPath ) : null;
        if ( ! empty( $strContentType ) ) {
            unlink( $strTmpPath );
            return $strContentType;
        }
        
    }
    function GetBytesFromHexString( $hexdata ) {
        
        // by Aaron Murgatroyd
        // http://stackoverflow.com/questions/6061505/detecting-image-type-from-base64-string-in-php
        for( $count = 0; $count < strlen( $hexdata ); $count+=2 )
            $bytes[] = chr( hexdec( substr( $hexdata, $count, 2 ) ) );

        return implode($bytes);
        
    }        
}