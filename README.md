# ArenaCM

A versatile Content Management System written in PHP. Allows developers
to rapidly build interactive websites hosted on web servers like Apache
and Nginx.

## How to install

Run the installer, which will install ArenaCM into /usr/local/arena2/.

 * sh install.sh

After this, go into your designated web directory, e.g. /var/www/site/,
and run the following command:

 * sh /usr/local/arena2/init.sh

This will link the correct files and folders to your site directory.

You will need to modify your virtual host in order for ARENA to see your
.htaccess file:

<VirtualHost *:80>
    ServerName yoursite.no
    DocumentRoot /var/www/yoursite/html
    <Directory /var/www/yoursite/html>
        Options Indexes FollowSymLinks
        AllowOverride ALL
    </Directory>
</VirtualHost>


## History

The ArenaCM project was started in 2004, at Blest Reklamebyrå in Stavanger,
Norway. After six years of development, it was first open sourced back in
2010. Later, Arena Enterprise was developed as an extension to Arena CM in
Idéverket AS.

ArenaCM has been used to build web sites in Scandinavia for over a decade.
It is quite robust, fast and versatile.

ArenaCM is the basis of SubEther, an open source social network and 
server for building social applications.
