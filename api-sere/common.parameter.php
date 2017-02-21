<?php
/*
* website urls paths normal and https
*/
define("WEBSITE_URL","localhost:8080/serenityphuket/"); /* need trailing  and do not add http/https here */
define("WEBSITE_HTTP_URL","http://" . WEBSITE_URL);
define("WEBSITE_HTTPS_URL","http://" . WEBSITE_URL);

/*
* database connection settings
*/
define("DB_HOST","127.0.0.1");
define("DB_USER","root");
define("DB_PASSWORD",'');
define("DB_NAME","");

$link_db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_query("SET NAMES UTF8");
mysql_select_db(DB_NAME, $link_db);


/*
* common meta data
*/
define('LANG_META_TITLE','Phuket Hotels| Luxury Hotels in Phuket | Serenity Resort &amp; Residences');
define('LANG_META_AUTHOR','Beachfront Apartments, Phuket | Serenity Terraces');
define('LANG_META_OWNER','Beachfront Apartments, Phuket | Serenity Terraces');
define('LANG_META_SUBJECT','Beachfront Apartments, Phuket | Serenity Terraces');
define('LANG_META_ABASTRACT','serenity terraces / rawai / phuket / thailand');
define('LANG_META_GOOGLEWEBMASTERTOOLS','IpG85VEJyA814OLIvjxdjW6kCRpoyNtz90xFHeofaME=');

/*
* new meta data
*/
define('LANG_META_ROBOTS','NOINDEX,NOFOLLOW');
define('LANG_META_Googlebot','NOINDEX,NOFOLLOW');
define('LANG_META_Cache_Control','no-cache');
define('LANG_META_Pragma','no-cache');
define('LANG_META_Expires','0');

/*
* language en
*/
 $lang="en";
?>
