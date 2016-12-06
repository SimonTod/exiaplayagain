HOW TO LET'S ENCRYPT
====================

be a root
---------
```bash
sudo -H bash
```

install certbot
---------------
```bash
cd
mkdir certbot
cd certbot
wget https://dl.eff.org/certbot-auto
chmod a+x certbot-auto
```

run test-cert command 
--------------------
```bash
./certbot-auto certonly --test-cert --force-renewal --register-unsafely-without-email --agree-tos --no-self-upgrade --webroot -w [PATH_TO_WEB_ROOT] -d [WEBSITE_DNS]
```
- PATH_TO_WEB_ROOT -> ends with '/'
- first use will compile certbot application (wait)

if succeed -> redo without test-cert
------------------------------------
- same succeed display with donation link in addition

redirect http to https and config https apache server (take a look at exiaplayagain example) /etc/apache2/site-available
------------------------------------------------------------------------------------------------------------------------
```xml
<VirtualHost *:80>
ServerName exiaplayagain.tk
RedirectPermanent / https://exiaplayagain.tk/
</VirtualHost>
<VirtualHost *:443>
ServerName exiaplayagain.tk
DocumentRoot "/var/www/html/exiaplayagain/web"
<Directory "/var/www/html/exiaplayagain/web">
DirectoryIndex app.php
allow from all
Options -Indexes
AllowOverride All
Require all granted
</Directory>
SSLEngine on
SSLProtocol +TLSv1.2
SSLCertificateFile /etc/letsencrypt/live/exiaplayagain.tk/cert.pem
SSLCertificateKeyFile /etc/letsencrypt/live/exiaplayagain.tk/privkey.pem
SSLCACertificateFile /etc/letsencrypt/live/exiaplayagain.tk/chain.pem
</VirtualHost>
```

restart apache
--------------
```bash
service apache2 restart
```

automatic certificate renewal using crontab
-------------------------------------------
```bash
crontab -e
@weekly /root/certbot/certbot-auto renew --noninteractive --no-self-upgrade ; /usr/sbin/service apache2 restart
```
- update all certificates present on the server.
- details of renewal in /etc/letsencrypt/renewal/