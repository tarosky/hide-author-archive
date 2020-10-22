#!/usr/bin/env bash

set -e

# Replace version.
if [ $1 ]; then
  VERSION=$1;
else
  VERSION=nightly
fi;
echo "Build package version $VERSION";
sed -i "s/%nightly%/$VERSION/" README.md
sed -i "s/%nightly%/$VERSION/" hide-author-archive.php

# Generate README
curl -L https://raw.githubusercontent.com/fumikito/wp-readme/master/wp-readme.php | php

# Remove unwanted files
echo "Remove unwanted files.";
rm -rf ./.git
rm -rf ./.github
rm -rf ./bin
rm -rf ./tests
rm -rf ./vendor
rm -rf ./wp
rm -rf ./.gitattributes
rm -rf ./.gitignore
rm -rf ./.wp-env.json
rm -rf ./composer.lock
rm -rf ./package-lock.json
rm -rf ./phpunit.xml.dist
rm -rf ./README.md
rm -rf ./.wordpress-org
