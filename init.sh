#!/bin/sh

# The contents of this file are subject to the Mozilla Public License
# Version 1.1 (the "License"); you may not use this file except in
# compliance with the License. You may obtain a copy of the License at
# http://www.mozilla.org/MPL/
#
# Software distributed under the License is distributed on an "AS IS"
# basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
# License for the specific language governing rights and limitations
# under the License.
#
# The Original Code is (C) 2004-2010 Blest AS.
#
# The Initial Developer of the Original Code is Blest AS.
# Portions created by Blest AS are Copyright (C) 2004-2010
# Blest AS. All Rights Reserved.
#
# Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
# Rune Nilssen

echo "Creating symlink:"
ln -s /usr/local/arena2/admin
ln -s /usr/local/arena2/lib
ln -s /usr/local/arena2/web
ln -s web/index.php
ln -s admin/admin.php
ln -s lib/htaccess .htaccess
echo "Done."
echo "Now creating upload folder:"
echo "upload/"
mkdir upload
echo "upload/images-master/"
mkdir upload/images-master
echo "upload/images-cache/"
mkdir upload/images-cache
echo "permissions..."
chmod -R 777 upload
echo "extensions folder"
mkdir "extensions"
echo "editor extension"
ln -s /usr/local/arena2/extensions/editor extensions/editor
echo "Done."
echo "Setting up config file"
echo "<?php define ( SITE_ID, 'My site' ); define ( NEWEDITOR, 'true' ); ?>" > config.php
chmod 777 config.php
echo "Making installer available."
cp /usr/local/arena2/install.php .



