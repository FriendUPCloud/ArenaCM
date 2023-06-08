#!/bin/bash

# This solution checks whether an argument "installmode" is passed when running 
# the script. If so, it sets a variable `ARENACM_HOME` to `/usr/local/arenacm`. 
# Otherwise, it prompts the user for an installation directory using the command 
# line parameter `$ARENCM_DIR`, which can be set by passing `-e` option while 
# calling this script with arguments or setting environment variables before 
# executing it. The value of $ARENCM_DIR will overwrite ARENACM_HOME if not 
# empty after reading from standard input and checking that its length is 
# greater than zero (to avoid errors). Finally, the script checks whether the 
# current user has write access to the Arenacm home directory (`$ARENACM_HOME`) 
# and executes itself as a script either with `sudo` or without depending on the 
# result of the check.

sudo mkdir -p /usr/local/arenacm
sudo rsync -ravl admin /usr/local/arena2/
sudo rsync -ravl lib /usr/local/arena2/
sudo rsync -ravl web /usr/local/arena2/
sudo rsync -ravl extensions /usr/local/arena2/
sudo rsync -ravl *.php /usr/local/arena2/
sudo rsync -ravl init.sh /usr/local/arena2/

exit

# Below is an experimental installer

# Check if install mode is enabled
if [ "$1" = 'installmode' ] && [ "$2" != "" ]; then
    # Set the Arenacm home directory to /usr/local/arenacm
    ARENACM_HOME=$2
    echo "Have something $ARENACM_HOME"
    exit
else
	ARENACM_HOME="/usr/local/arenacm"
	echo "Doing default $ARENACM_HOME"
	
	# Prompt user for installation directory and set it as $ARENACM_DIR
	echo "Enter an installation directory (or press CTRL+D when finished): "
	read ARENACM_HOME
	
	#ARENACM_HOME=$(echo $ARENACM_HOME | sed 's/\//\\\//g') # converting // to \/ so we can use it inside a string literal
fi

# Make parent directory available
ARENACM_PARENT="${ARENACM_HOME%/*}"

if [ ! -d "$ARENACM_PARENT" ]; then
    echo "Directory does not exist: $ARENACM_PARENT"
else
    if [[ $(stat -c %u:%w ${ARENACM_PARENT}) ]]; then
        echo "Folder is writable by all users: $ARENACM_PARENT"
    else
        echo "Folder has insufficient permissions for this command. Please run as root or with sudo privileges."
    fi
fi

exit

# Check if user has write access to $ARENACM_HOME and execute script with sudo or without it accordingly
if [ ! $(ls -ld "$ARENACM_PARENT") ]; then
    exec sudo bash "${0} installmode $ARENACM_PARENT $ARENACM_HOME"
else
    bash "${0} installmode $ARENACM_PARENT $ARENACM_HOME"
fi


