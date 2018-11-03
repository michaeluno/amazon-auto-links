#!/usr/bin/env bash

# Script information
SCRIPT_NAME="WordPress Plugin The Test Suite Installer"
SCRIPT_VERSION="1.0.1"

# Scripts defining custom functions
source $(dirname $0)/include/download.sh
source $(dirname $0)/include/info.sh
source $(dirname $0)/include/downloadWPCLI.sh
source $(dirname $0)/include/downloadCodeception.sh

# Parse arguments
CONFIGURATION_FILE_PATH="settings.cfg"
REINSTALL_PROJECT_FILES=0
while getopts “hpvt:c:” OPTION
do
    case $OPTION in
        h)
            printUsage
            exit 1
            ;;
        v)
            printVersion
            exit 1
            ;;                      
        p)
            echo "Reinstalling project files."
            REINSTALL_PROJECT_FILES=1
            ;;
        c)
            CONFIGURATION_FILE_PATH=$OPTARG
            ;;
        ?)
            echo option not found
            printUsage
            exit 1
            ;;
    esac
done

# Configuration File
if [ ! -f "$CONFIGURATION_FILE_PATH" ]; then
    echo The setting file could not be loaded.
    exit 1
fi
source "$CONFIGURATION_FILE_PATH"
echo "Using the configuration file: $CONFIGURATION_FILE_PATH"

# Variables
WORKING_DIR=$(pwd)
if [[ -z "$PROJECT_DIR" ]]; then
    PROJECT_DIR=$(cd "$WORKING_DIR/.."; pwd)
fi
# convert it to an absolute path
PROJECT_DIR="$(cd "$(dirname "$PROJECT_DIR")"; pwd)/$(basename "$PROJECT_DIR")"
cd "$WORKING_DIR"

TEMP=$([ -z "${TEMP}" ] && echo "/tmp" || echo "$TEMP")
WP_CLI="$TEMP/wp-cli.phar"
CODECEPT="$TEMP/codecept.phar"
C3="$TEMP/c3.php"
TEMP_PROJECT_DIR="$TEMP/$PROJECT_SLUG"

# Fix: Fatal error: Class 'WP_REST_Server' not found in .../wordpress-tests-lib/includes/spy-rest-server.php on line 3
if echo $WP_VERSION | grep -E '[0-9]+\.[0-9]+(\.[0-9]+)?' > /dev/null
then
  WP_TESTS_TAG="tags/$WP_VERSION"
else
	# http serves a single offer, whereas https serves multiple. we only want one
	download http://api.wordpress.org/core/version-check/1.7/ "$TEMP/wp-latest.json"
    if [[ $(uname -s) == 'Darwin' ]]; then
        SED_REGEXOPTION='-E' #For Max OSX
    else
        SED_REGEXOPTION='-r' #Other OSes
    fi    
	LATEST_VERSION=$(sed $SED_REGEXOPTION 's/.*"version":"([0-9]+\.[0-9]+(\.[0-9]+)?)".*$/\1/g' "$TEMP/wp-latest.json")
	if [[ -z "$LATEST_VERSION" ]]; then
		echo "The latest WordPress version could not be found. Script exiting."
		exit 1
	fi
	WP_TESTS_TAG="tags/$LATEST_VERSION"
fi

# convert any relative path or Windows path to linux/unix path to be usable for some path related commands such as basename
if [ ! -d "$WP_TEST_DIR" ]; then
  mkdir -p "$WP_TEST_DIR"
fi
cd "$WP_TEST_DIR"
WP_TEST_DIR=$(pwd)   
cd "$WORKING_DIR"

echo "Project Dir: $PROJECT_DIR"
echo "Working Dir: $WORKING_DIR"
echo "WP Test Dir: $WP_TEST_DIR"
echo "WP TESTS TAG: $WP_TESTS_TAG"

# Exit on errors, xtrace
# set -x
# set -ex
set -e

# On Travis, the working directory looks like:
# /home/travis/build/michaeluno/sample-tdd-plugin

installWordPress() {

    # Remove the destination folder if exists to perform a clean install
    # If the project directory path is the test site directory, which is the case of on Travis CI, do not delete it.
    if [ ! "$PROJECT_DIR" == "$WP_TEST_DIR" ]; then  
        rm -rf "$WP_TEST_DIR"
    fi
    
    # We use wp-cli command
    php "$WP_CLI" core download --force --path="$WP_TEST_DIR"
  
    # Change directory to the test WordPres install directory.
    cd "$WP_TEST_DIR"    
    
    rm -f wp-config.php
    dbpass=
    if [[ $DB_PASS ]]; then
        echo 'db pass is not empty'
        dbpass=--dbpass="${DB_PASS}"
    fi    
    php "$WP_CLI" core config --dbname=$DB_NAME --dbuser="$DB_USER" $dbpass --extra-php <<PHP
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
\$table_prefix = '$WP_TABLE_PREFIX';
PHP
    
    # Renew the database table
    setup_database_table    

    # Create/renew the database - if the environment variable WP_MULTISITE is set, install multi site network Wordpress.
    if [[ $WP_MULTISITE = 1 ]]; then    
        php "$WP_CLI" core multisite-install --url="$WP_URL" --title="$WP_SITE_TITLE" --admin_user="$WP_ADMIN_USER_NAME" --admin_password="$WP_ADMIN_PASSWORD" --admin_email="$WP_ADMIN_EMAIL"
    else
        php "$WP_CLI" core install --url="$WP_URL" --title="$WP_SITE_TITLE" --admin_user="$WP_ADMIN_USER_NAME" --admin_password="$WP_ADMIN_PASSWORD" --admin_email="$WP_ADMIN_EMAIL"
    fi    
    
}
    setup_database_table(){
        
        # If the database table already exists, drop it.
        # if [[ -z "$DB_PASS" ]]; then
            # DB_PASS="\"\""
        # fi
        dbpass=
        if [[ $DB_PASS ]]; then
            echo 'db pass is not empty'
            dbpass="-p${DB_PASS}"
        fi           
        # RESULT=`mysql -u$DB_USER -p$DB_PASS --skip-column-names -e "SHOW DATABASES LIKE '$DB_NAME'"`
        RESULT=`mysql -u$DB_USER $dbpass --skip-column-names -e "SHOW DATABASES LIKE '$DB_NAME'"`
        if [ "$RESULT" == "$DB_NAME" ]; then
            php "$WP_CLI" db drop --yes
        fi
    
        # mysql -u $DB_USER -p$DB_PASS -e --f "DROP $DB_NAME"
        # mysqladmin -u$#DB_USER -p$DB_PASS drop -f $DB_NAME
        php "$WP_CLI" db create
        
    }
    
# Installs WordPress test suite utilities
installWPTestSuite() {

    # portable in-place argument for both GNU sed and Mac OSX sed
    if [[ $(uname -s) == 'Darwin' ]]; then
        local ioption='-i .bak'
    else
        local ioption='-i'
    fi
    
    # Download WordPress unit test suite library
    local WP_TEST_SUITES_TEMP_DIR="$TEMP/wordpress-tests-lib"
    svn export --force --quiet "https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/includes/" "$WP_TEST_SUITES_TEMP_DIR/includes"
    svn export --force --quiet "https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/data/" "$WP_TEST_SUITES_TEMP_DIR/data"
    
    # if [[ $WP_MULTISITE = 1 ]]; then
        # may download multisite.xml for phpUnit
    # fi
    
    # Set up WordPress testing suite library
    cd "$WP_TEST_DIR"
    
    # Some paths written in wp-tests-config.php needs to be modified and on Windows systems, it is difficult to modify them to absolute path.
    # so in order to use dirname(), the test suites library needs to be placed close to the test site directory.
    local WP_TEST_SUITES_DIR="$(pwd)/wordpress-tests-lib"
    
    # Copy the downloaded files to the test WordPress site directory
    cp -r "$WP_TEST_SUITES_TEMP_DIR/" "$WP_TEST_DIR"
    
    # Make sure the configuration file does not exist.
    if [ -f "$WP_TEST_SUITES_DIR/wp-tests-config.php" ]; then
        rm -f "$WP_TEST_SUITES_DIR/wp-tests-config.php"        
    fi    
    download https://develop.svn.wordpress.org/${WP_TESTS_TAG}/wp-tests-config-sample.php "$WP_TEST_SUITES_DIR/wp-tests-config.php"
    
    # Edit the tests configuration file.
    cd "$WP_TEST_SUITES_DIR"
    sed $ioption "s:dirname( __FILE__ ) . '/src/':dirname( dirname( __FILE__ ) ) . '/':" wp-tests-config.php
    sed $ioption "s/youremptytestdbnamehere/$DB_NAME/" wp-tests-config.php
    sed $ioption "s/yourusernamehere/$DB_USER/" wp-tests-config.php
    sed $ioption "s/yourpasswordhere/$DB_PASS/" wp-tests-config.php
    sed $ioption "s|localhost|${DB_HOST}|" wp-tests-config.php
        
    # Set the environment variable which is accessed from the unit test bootstrap script.
    export WP_TESTS_DIR="$WP_TEST_SUITES_DIR"
    
}
    
# Uninstalls default plugins    
uninstallPlugins() {
    cd "$WP_TEST_DIR"
    php "$WP_CLI" plugin uninstall akismet
    php "$WP_CLI" plugin uninstall hello
}

# Evacuates plugin project files.
# This needs to be done before installing the Wordpress files.
# When the test WordPress site needs to be placed under the project directory such as on Travis CI,
# simply copying the entire project files into the sub-directory of itself is not possible.
# so evacuate the project files to a temporary location first and then after installing WordPress, copy them back to the WordPress plugin directory.
evacuateProjectFiles() {
    
    # Make sure no old file exists.
    if [ -d "$TEMP_PROJECT_DIR" ]; then
        rm -rf "$TEMP_PROJECT_DIR"    
    fi
    
    # The `ln` command gives "Protocol Error" on Windows hosts so use the cp command.
    # The below cp command appends an asterisk to drop hidden items especially the .git directory but in that case, the destination directory needs to exist.
    mkdir -p "$TEMP_PROJECT_DIR"
    
    # Drop hidden files from being copied
    cp -r "$PROJECT_DIR/"* "$TEMP_PROJECT_DIR" 2>/dev/null
    
    
}

# Installs the project plugin
installPlugins() {
    
    # Install user specified plugins
    if [[ $WP_MULTISITE = 1 ]]; then    
        local OPTION_ACTIVATE="--activate-network"
    else
        local OPTION_ACTIVATE="--activate"
    fi         
    for _INSTALL_PLUGIN in "${INSTALL_PLUGINS[@]}" 
    do :
        php "$WP_CLI" plugin install "$_INSTALL_PLUGIN" $OPTION_ACTIVATE
    done    
    
    
}

## This Project Plugin
installProjectFiles() {
    # Make sure no old file exists.
    if [ -d "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG" ]; then
        
        # Directly removing the directory sometimes fails saying it's not empty. So move it to a different location and then remove.
        # mv -f "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG" "$TEMP/$PROJECT_SLUG"
        # rm -rf "$TEMP/$PROJECT_SLUG"
        
        # Sometimes moving fails so remove the directory in case.
        rm -rf "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG"
        
    fi    
    
    # The `ln` command gives "Protocol Error" on Windows hosts so use the cp command.
    # The below cp command appends an asterisk to drop hidden items especially the .git directory but in that case, the destination directory needs to exist.
    mkdir -p "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG"
    # drop hidden files from being copied
    # cp -r "$PROJECT_DIR/"* "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG"
 
    cp -r "$TEMP_PROJECT_DIR" "$WP_TEST_DIR/wp-content/plugins"
 
    # wp cli command
    cd $WP_TEST_DIR
    
    if [[ $WP_MULTISITE = 1 ]]; then    
        php "$WP_CLI" plugin activate --network $PROJECT_SLUG
    else
        php "$WP_CLI" plugin activate $PROJECT_SLUG
    fi     

    echo Installed project files.
}

installCodeception() {
        
    # Make sure no old test files exist
    # if [ -d "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/tests" ]; then
        # rm -rf "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/tests"
    # fi

    # Rename `codeception.dist.yaml` temporarily to avoid an error.
    # @see https://github.com/Codeception/Codeception/issues/5253
    CONFIG_DIST_FILE="$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/codeception.dist.yml"
    CONFIG_DIST_FILE_BACKUP="$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/codeception.dist.yml.bak"
    mv "$CONFIG_DIST_FILE" "$CONFIG_DIST_FILE_BACKUP"

    # Run the bootstrap to generate necessary files.
    echo Creating Codeception configuration files.
    php "$CODECEPT" bootstrap "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/"

    # Restore the global configuration distribution file. (codeception.dist.yml)
    mv "$CONFIG_DIST_FILE_BACKUP" "$CONFIG_DIST_FILE"

    # @deprecated This seems to be redundant as the entire `tests` directory is copied.
    # Copy bootstrap scripts.
#    cp -r "$PROJECT_DIR/test/tests/acceptance/_bootstrap.php" "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/tests/acceptance/_bootstrap.php"
#    cp -r "$PROJECT_DIR/test/tests/functional/_bootstrap.php" "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/tests/functional/_bootstrap.php"
#    cp -r "$PROJECT_DIR/test/tests/unit/_bootstrap.php" "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/tests/unit/_bootstrap.php"

    # Create an acceptance setting file.
    FILE="$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/tests/acceptance.suite.yml"
    cat <<EOM >$FILE
modules:
    config:
        PhpBrowser:
            url: '$WP_URL'
EOM

   # Create a Codeception global setting file
   FILE="$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/codeception.yml"
   cat <<EOM >$FILE
modules:
    config:
        Db:
            dsn: 'mysql:host=$DB_HOST;dbname=$DB_NAME'
            user: '$DB_USER'
            password: '$DB_PASS'
EOM

    # Make it load c3.php
    # cp -r "$C3" "$WP_TEST_DIR/c3.php"
    # cd "$WP_TEST_DIR"
    # sed -i "s:<?php:<?php require( dirname( __FILE__ ) . '/c3.php' );:" index.php   
    
    # Create sub-directories used by c3
    # mkdir -p "$WP_TEST_DIR/report"
    # mkdir -p "$WP_TEST_DIR/c3tmp"   
    
    # Dump sql database file.
    cd "$WP_TEST_DIR"
    mkdir -p "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/tests/_data/"
    php "$WP_CLI" db export "$WP_TEST_DIR/wp-content/plugins/$PROJECT_SLUG/test/tests/_data/dump.sql"
    
}

# Download necessary applications
downloadWPCLI "$WP_CLI"
downloadCodeception "$CODECEPT"
evacuateProjectFiles

# Install components
if [[ REINSTALL_PROJECT_FILES -ne 1 ]]; then    
    installWordPress
    echo Installed WordPress
    installWPTestSuite
    echo Installed WordPress test suite utilities
    uninstallPlugins
    echo Installed
    installPlugins
fi
installProjectFiles
installCodeception

# Let the user know it's finished
if [[ REINSTALL_PROJECT_FILES -eq 1 ]]; then    
    echo Reinstallation has been completed!
else
    echo Installation has been completed!
fi 
