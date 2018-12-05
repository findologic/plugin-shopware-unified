#!/usr/bin/env bash

# Get current directory of the script
ROOT_DIR=$( cd "$(dirname "${BASH_SOURCE[0]}")" ; pwd -P )

# Extract version from the plugin.xml file
VERSION_RAW="$(sed -n 's|<version>\(.*\)</version>|\1|p' ./FinSearchUnified/plugin.xml)"

# Trim the whitespaces from the version otherwise it would cause problems
# in creating the archive zip file
VERSION="$(echo -e "${VERSION_RAW}" | tr -d '[:space:]')"
echo "Version: ${VERSION}"

# Copying plugins files
echo "Copying files ... "
cp -rf ./FinSearchUnified/ /tmp/FinSearchUnified

# Get into the created directory for running the archive command
cd "/tmp/FinSearchUnified/"

# Install dependencies
composer install --no-dev

# Run archive command to create the zip in the root directory
composer archive --format=zip --file=FinSearchUnified-${VERSION} --dir=${ROOT_DIR}

# Delete the directory after script execution
rm -rf "/tmp/FinSearchUnified"