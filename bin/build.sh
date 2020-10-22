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
