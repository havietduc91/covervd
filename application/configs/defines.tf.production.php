<?php
/**
 * SITE specifics
 */
//define('POST_PAGE_SIZE', 30);
define('SITE_URL', 'http://thaifun.net');
//define('SITE_URL', 'http://www.oakq.com');
//define('ASSETS_CDN', 'http://cdn.hoibi.net/');
define('SAND_ASSETS_CDN', 'http://sandassets.thaifun.net/'); //or http://assets1.sandphp.com
define('ASSETS_CDN', 'http://funassets.thaifun.net/'); //should have been assets.hoibi.net
define('STATIC_CDN', 'http://s.thaifun.net/');
define('STATIC_PATH', PUBLIC_PATH . '/ufiles/');
define('DESIGN_PATH', 'http://thaifun.net/design/');
define('NOREPLY_EMAIL', 'no-reply@thaifun.net');
define('DOMAIN', 'thaifun.net');

define('CONTACT_EMAIL', 'kongbt@gmail.com');
define('USE_AMAZON_SES', false); // true => send mail with amazon SES
define('FILES_UPLOAD_PATH', realpath(APPLICATION_PATH . "/../") . "/hidden/");
define('JSVERSION', '1340447153');
define('PAGE_RANGES', 7);//numbers pagination links at a time

//SOCIAL configs
define('NS_PREFIX', 'thaifunnet');
define('FB_APP_ID', '1416534745233225');//numbers pagination links at a time
define('FB_APP_SECRET', 'c01653896358a8527ea1e05ee802b664');//numbers pa');//numbers pagination links at a time
define('GA_ID', 'UA--46035898-1'); 

//Google
define('G_CLIENT_ID','139117437539-idk49ps0m0ms6od84km1qen888r2gkee.apps.googleusercontent.com');
define('G_CLIENT_SECRET','GUHKwEFitsf6rPVNIgtWAr9d');
define('G_APP_NAME','thaifun.net');
define('G_DEV_KEY','AIzaSyCngO57SDDi26Mu0RybA2xKBRVXnthKwsw');//is API Key

/**
 * DB connections
 */
//include_once('functions.php');
defined('MONGO_HOST') || define('MONGO_HOST', 'localhost');
//define('DICT_BACKEND', 'mongo');
define('DICT_BACKEND', 'redis');
define('REDIS_PORT', 6379);
define('REDIS_HOST','127.0.0.1');
define('REDIS_PASS', '');

define('RDB_DICT_DB', 9); //[0,1,2] is kept for eekip, [3,4,5] for school,, [6,7,8] for taxi
define('RDB_CACHE_DB', 10);
define('RDB_QUEUE_DB', 11);

define('RDB_DICT_PREFIX', 'd:'); //dict

define('DICT_RDB_USER_PREFIX', 'u:');
define('DICT_RDB_VOCABULARY', 'vocabulary'); // set of all words in the dict

/**
 * QUEUE
 */
//queue
define('RDB_QUEUE_PREFIX', 'resque:'); // prefix for all queue-related keys
define('QUEUE_ADMIN_PASS', '6f1a7662f70c9bb4d1b2a79baddab208');


/** Paginations & limits */
define('COMMENT_PAGE_SIZE',20);
define('POST_PAGE_SIZE',20);
define('ACTIVITY_PAGE_SIZE',5);
define('MAXIMUM_TAGS_PER_POST', 10);
define('MAXIMUM_USERS_PER_POST', 5);

define('FOLLOW_LIMIT', 10);
define('USER_ITEM_PER_PAGE', 10);
define('USER_FRIEND_CACHE_LIMIT', 10);
define('USER_NOTIF_CACHE', 10);///only cache 20 notifications
define('USER_NOTIF_READ_CACHE', 10);
define('USER_ITEM_CACHE_LIMIT', 10);
define('COMMENT_CACHE_IN_POST_LIMIT', 3);
define('GALLERY_ITEM_LIMIT', 50);
define('USER_FOLLOW_USER_LIMIT', 10);
define('USER_FOLLOW_ITEM_LIMIT', 10);
define('ASSOCIATE_LIMIT', 100);
define('OWN_LOWER_LIMIT', 2); // own = OWN_LOWER_LIMIT is ok
define('TEXT_POST_LIMIT', 300); //when to trim/breadcumb post content to "more.."
define('CACHED_POST_TITLE_WORD_LENGTH', 20); //number of words
define('CACHED_COMMENT_TITLE_WORD_LENGTH', 20); //number of words

/**
 * File upload & path
 */
define('DEFAULT_AVATAR_URL','http://d17yofrdipd1db.cloudfront.net/images/avatar.gif');
define('SYSTEM_AVATAR_URL', 'http://d17yofrdipd1db.cloudfront.net/images/warning.jpg');
//define('AVATAR_PREFIX', 'http://d3syq05o3krv6a.cloudfront.net');
define('AVATAR_PREFIX', '/ufiles');
// directory structure to upload avatar
define('AS3_BUCKET_NAME', 'stuffcdn');
define('IMG_STORE_PATH', '/ufiles/');
define('IMG_STORE_LOCAL', true); //true => store local || false => upload to AmazonS3
define('SET_UPLOAD_ITEM_AVATAR_NOW', false); // set upload avatar together insert items?
define("DEFAULT_ITEM_AVATAR_SIZE", 50);// avatar size to insert DB
define("DEFAULT_USER_AVATAR_SIZE", 50);// avatar size to insert DB
define('AWS_KEY', 'AKIAIQRQ3ZPTGZRYDY2Q');
define('AWS_SECRET', '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');
define('AS3_AVATAR_FOLDER', 'avatar');
define('AS3_ITEM_IMAGE_FOLDER', 'image');// 'item');
define('SECURE_FILES_UPLOAD_PATH', realpath(APPLICATION_PATH . "/../") . "/hidden");
define('PUBLIC_FILES_UPLOAD_PATH', realpath(APPLICATION_PATH . "/../") . "/public/ufiles");
define('SECURE_FILES_SERVER', 'local');
define('PUBLIC_FILES_SERVER', 'local');