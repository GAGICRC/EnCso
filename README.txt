EnCso
Free, open-source software for crowdsourcing creative projects
http://encso.gagicrc.com


REQUIREMENTS:

- Apache HTTP Server
- PHP 5
- MySQL
-*The Chat feature can only be used in non-commercial applications (More information below)


INSTALLATION:

1. Upload all Pipeline files to your web server.
2. Create a new MySQL database and run the queries in "database.sql" (located in the root directory).
3. Copy "config_sample.php" to a new file, "config.php", and put it in the root directory.
4. Follow the instructions in "config.php" to configure your Pipeline instance. Remember to save the file when you're finished. 
5. Make Apache the owner of the "upload" folder and its subfolders (chown -R apache ./upload)
6. Under the "apache/conf" folder, open "httpd.conf". Ensure that "LoadModule rewrite_module modules/mod_rewrite.so" is uncommented.
7. If your Pipeline instance is in a subfolder of your domain, you may need to modify the ErrorDocument path in ".htaccess" (located in the root directory).
8. If you are installing on Windows, make sure the following line is uncommented in your "php.ini" file:
    extension=php_fileinfo.dll


CREDITS:

The EnCso team, based at OSIKMCN, includes:

Fernando Teodósio - Project Lead

Hugo Rodrigues - Community Manager

Frederico Dias - Developer

Pipeline /EnCso makes use of many open-source software components and media assets, including:

Google Charts and Google Libraries
http://code.google.com/apis/chart/
http://code.google.com/apis/libraries/

jQuery and jQueryUI
http://jquery.com/
http://jqueryui.com/

FFmpeg
http://ffmpeg.org/

Flowplayer
http://flowplayer.org/

Fugue icons by Yusuke Kamiyamane
http://p.yusukekamiyamane.com/

SWFObject
http://code.google.com/p/swfobject/

SWFTools
http://www.swftools.org/

**Chat Feature**
The included Chat feature is built off of  Anant Garg's chat program (anantgarg.com). This was released for non-commercial purposes. If you want to use it in
a commercial capacity, please contact Anat for permission. In order to turn off the chat feature, set the following line in the config.php file to false: 
define('ENABLE_CHAT', true);

LICENSE:

Pipeline/EnCso is open-source software released under the GNU General Public License (GPL), version 3.0.
http://www.gnu.org/licenses/gpl-3.0.txt
