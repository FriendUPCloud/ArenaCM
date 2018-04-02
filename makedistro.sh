#!/bin/sh

# Create a distribution

ARENA="arena`cat version.txt`"

php updatefiles.php > log.txt
mkdir arena
cp -r admin arena/
cp -r lib arena/
cp -r web arena/
cp -r extensions arena/
cp -r upload arena/
cp -r friend arena/
cp index.php arena/
cp admin.php arena/
cp config.php.example arena/
cp MPL.txt arena/
cp README arena/
cp -d .htaccess arena/
tar -czf ../${ARENA}.tgz arena
rm -fr arena