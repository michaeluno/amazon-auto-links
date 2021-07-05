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
 * Provides utility methods that deals with the file system.
 * @since 4.3.9
 */
class AmazonAutoLinks_Utility_FileSystem extends AmazonAutoLinks_Utility_XML {

    /**
     * @remark  used upon plugin uninstall.
     * @param   string $sDirectoryPath
     * @return  bool|null
     * @since   3.7.10
     * @see     https://stackoverflow.com/questions/7497733/how-can-i-use-php-to-check-if-a-directory-is-empty/7497848#7497848
     */
    static public function isDirectoryEmpty( $sDirectoryPath ) {
        if ( ! is_readable( $sDirectoryPath ) ) {
            return null;
        }
        $_rDir = opendir( $sDirectoryPath );
        while ( false !== ( $_bsEntry = readdir( $_rDir ) ) ) {
            if ( $_bsEntry != "." && $_bsEntry != "..") {
                closedir( $_rDir );
                return false;
            }
        }
        closedir( $_rDir );
        return true;
        // @deprecated As said inefficient.
        // return ( count( scandir( $sDirectoryPath ) ) == 2 );
    }

    /**
     * Checks whether the path is a directory and it exists.
     * @remark `is_dir()` is not reliable by itself as it can return true on non-existing path.
     * @since  4.3.8
     * @param  string  $sPath
     * @param  boolean $bCheckWritable Whether to check if the directory is writable or not.
     * @return boolean
     */
    static public function doesDirectoryExist( $sPath, $bCheckWritable=true ) {
        $_bExistsAndDir = file_exists( $sPath ) && is_dir( $sPath );
        return $bCheckWritable
            ? $_bExistsAndDir && is_writable( $sPath )
            : $_bExistsAndDir;
    }

    /**
     * @param  string  $sPath
     * @param  integer $iMode
     * @return boolean true if set; otherwise, false.
     */
    static public function getCHMODApplied( $sPath, $iMode ) {
        $_iOldUmask = umask( 0 );
        $_bSet      = chmod( $sPath, $iMode ); // on a shared server, sometimes the permission fails to set with mkdir().
        umask( $_iOldUmask );
        clearstatcache( true, $sPath );
        return $_bSet;
    }

    /**
     * Retrieves paths between the given base directory and the subject directory.
     * @remark The paths must be normalized.
     * @param  string  $sDirPath         The subject directory path.
     * @param  string  $sDirPathAncestor The abase ancestor directory path that contains the subject directory path.
     * @param  boolean $bIncludeSelf     Whether to include the subject directory.
     * @param  boolean $bIncludeBase     Whether to include the most parent (base) directory.
     * @return array   An array holding nested directory paths in the order that parent comes first (with a lower index).
     * @since  4.3.8
     */
    static public function getNestedDirPaths( $sDirPathAncestor, $sDirPath, $bIncludeSelf=true, $bIncludeBase=true ) {
        $_aNestedDirPaths = array();
        $_sDirPth         = $bIncludeSelf ? $sDirPath : dirname( $sDirPath );
        $sDirPathAncestor = untrailingslashit( $sDirPathAncestor );
        if ( false === strpos( $_sDirPth, $sDirPathAncestor ) ) {
            return $_aNestedDirPaths;
        }
        while ( $sDirPathAncestor !== $_sDirPth ) {
            $_aNestedDirPaths[] = $_sDirPth;
            $_sDirPth = dirname( $_sDirPth );
        }
        if ( $bIncludeBase ) {
            $_aNestedDirPaths[] = $sDirPathAncestor;
        }
        return array_reverse( $_aNestedDirPaths );
    }

    /**
     * @param  string  $sDirPath      The subject directory path to create.
     * @param  string  $sBaseDirPath  The path of an ancestor base directory that contains the subject directory.
     * @param  integer $iCHMODMode    The CHMOD mode.
     * @param  boolean $bMakeWritable Attempt to make the directory writable.
     * @return boolean true: the directory is writable, false: cannot create or not writable.
     * @see    https://stackoverflow.com/questions/18352682/correct-file-permissions-for-wordpress
     */
    static public function getDirectoryCreatedRecursive( $sDirPath, $sBaseDirPath, $iCHMODMode=0755, $bMakeWritable=true ) {

        if ( self::doesDirectoryExist( $sDirPath, false ) ) {
            if ( is_writable( $sDirPath ) ) {
                return true;
            }
            if ( ! $bMakeWritable ) {
                return false;
            }
            foreach( self::getNestedDirPaths( $sBaseDirPath, $sDirPath ) as $_sNestedDirPath ) {
                if ( is_writable( $_sNestedDirPath ) ) {
                    continue;
                }
                if ( ! self::getCHMODApplied( $sDirPath, $iCHMODMode ) ) {
                    return false;
                }
            }
            return true;
        }

        // At this point, the directory does not exist.

        // Ancestor directories can have the wrong file permissions so check each.
        foreach( self::getNestedDirPaths( $sBaseDirPath, $sDirPath ) as $_sNestedDirPath ) {
            if ( ! self::getDirectoryCreated( $_sNestedDirPath, $iCHMODMode ) ) {
                return false;
            }
        }
        return true;

    }

    /**
     * @param  string  $sDirPath
     * @param  integer $iCHMODMode
     * @return boolean true if the created directory is writable; otherwise, false.
     * @since  4.3.8
     * @remark Recursive option is not supported as parent/ancestor directories can miss a specified CHMOD and causes troubles. So use getDirectoryCreatedRecursive() for that purpose.
     */
    static public function getDirectoryCreated( $sDirPath, $iCHMODMode=0755 ) {
        if ( ! is_writable( dirname( $sDirPath ) ) ) {
            return false;
        }
        if ( self::doesDirectoryExist( $sDirPath, false ) ) {
            return is_writable( $sDirPath );
        }
        $_iOldUmask = umask( 0 );
        $_bCreated  = mkdir( $sDirPath, $iCHMODMode, false );
        umask( $_iOldUmask );
        if ( ! $_bCreated ) {
            return false;
        }
        self::getCHMODApplied( $sDirPath, $iCHMODMode );
        return is_writable( $sDirPath );
    }

    /**
     * @param  string $sPath
     * @return string
     * @since  4.3.8
     */
    static public function getReadableCHMOD( $sPath ) {
        return self::getPaddedOctal( fileperms( $sPath ) );
    }

    /**
     * Converts integer to octal representation like '0666' for 493, used to check CHMOD values.
     * @remark Not same as decoct()
     * @see    decoct()
     * @param  integer $iInteger
     * @return string
     */
    static public function getPaddedOctal( $iInteger ) {
        $_bsOctal = substr( sprintf( '%o', $iInteger ), -4 );
        return false === $_bsOctal ? '' : $_bsOctal;
    }

    /**
     * @remark  Used upon plugin uninstall.
     * @since   3.7.10
     * @param   string  $sDirectoryPath
     * @return  boolean true if removed; otherwise, false.
     */
    static public function removeDirectoryRecursive( $sDirectoryPath ) {

        if ( ! self::doesDirectoryExist( $sDirectoryPath, true ) ) {
            return false;
        }
        $_bEmptied = self::emptyDirectory( $sDirectoryPath );
        return $_bEmptied
            ? rmdir( $sDirectoryPath )
            : false;

    }

    /**
     * Makes a given directory empty.
     * @param  string  $sDirectoryPath
     * @return boolean true if the directory becomes empty; otherwise, false.
     * @since  4.4.0
     */
    static public function emptyDirectory( $sDirectoryPath ) {

        if ( ! self::doesDirectoryExist( $sDirectoryPath, true ) ) {
            return false;
        }
        $_aItems = scandir( $sDirectoryPath );
        $_aItems = is_array( $_aItems ) ? $_aItems : array();
        foreach( $_aItems as $_sItem ) {
            if ( $_sItem === "." || $_sItem === ".." ) {
                continue;
            }
            if ( is_dir($sDirectoryPath . "/" . $_sItem ) ) {
                self::removeDirectoryRecursive($sDirectoryPath . "/" . $_sItem );
                continue;
            }
            unlink($sDirectoryPath . "/" . $_sItem );
        }
        return self::isDirectoryEmpty( $sDirectoryPath );

    }

}