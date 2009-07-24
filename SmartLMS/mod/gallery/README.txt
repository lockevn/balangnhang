$Id: README.txt,v 1.5 2007/01/05 03:17:38 mark-nielsen Exp $

Welcome to Moodle Gallery Module (MGM) by Mark Nielsen.

Development Environment:
    Moodle:   Moodle  1.6.3
    Database: MySQL   4.1.16
    Gallery:  Gallery 2.1.2

1.  Install Gallery2.1.2 from http://codex.gallery2.org/index.php/Gallery2:Download
    IMPORTANT: do NOT turn on url_rewrite in Gallery2.  This will cause errors and MGM will NOT work.

Existing Moodle Installations (Upgrade or install of MGM):

    2. Backup your Database.
    3. Login as admin to your Moodle Site.
    4. Move MGM's gallery directory into your Moodle's /mod directory (if /mod/gallery already exists, delete it first).
    5. Visit your site's admin screen (http://yourmoodlesite.com/admin/).
    6. Configure Gallery by visiting http://yourmoodlesite.com/admin/module.php?module=gallery

OR Pre Moodle Install:

    2. Move MGM's gallery directory into your Moodle's /mod directory.
    3. Follow Moodle's standard install procedure.
    4. Configure Gallery by visiting http://yourmoodlesite.com/admin/module.php?module=gallery

Optional:

Edit your Gallery2's config.php file and make the following edit:
    Change:
        $gallery->setConfig('mode.embed.only', false);
    To:
        $gallery->setConfig('mode.embed.only', true);
    
    This will make Gallery2 only accessible through embedded applications (aka MGM).


Version Notes (latest changes on top):

Version 2006102801:
    - Can disable permission handling by MGM

Version 2006102800:
    - Now supports Gallery2.1.2
    - Moved Gallery2's breadcrumb into Moodle's
    - In MGM's settings, one can change the Linked album to another existing Gallery2 album or create a new one.

Version 2005091600:
    - First version of MGM

Cheers,
Mark Nielsen