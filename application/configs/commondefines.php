<?php
/**Misc*/
define('GOOGLE_USER_IP', '117.5.182.45');
define('UPLOAD_AVATAR_LOG_TO_FILE', true);//log upload avatar?
define('TOKEN_PERSISTENT_LIMIT', 10); // limited number user token persistent.
//task
define('DATE_DELIMITER', '/');
define('TICKER_INTERVAL', 10000);
define('MAX_TIME_STAMP', 'm'); // .
define('MAX_TIME_STAMP1', 'm1'); // .


/**
 * Framework specifics
*/
define('HASH_ALGO', 'md5'); //password hash algorithm. Can be sha1
defined ('COOKIE_PREFIX') || define('COOKIE_PREFIX', '_cl_');
defined ('COOKIE_SALT') || define('COOKIE_SALT', 'dkmmhehehe'); //salt for _cl_uhash
define('GUEST_ID', 0);
defined ('COOKIE_SESSION_TIMEOUT') || define('COOKIE_SESSION_TIMEOUT', 60 * 60 * 24); //seconds . Logically must be > PHP session period
define('CL_NONE_SEARCH_KEY', 'cl_no_search'); //_cl_no_search?
define('NESTED_DOCUMENT_SEP', '__');

define('IMG_CONCATE_SIZE', 500);
define('WM_DIR', realpath(APPLICATION_PATH . "/../misc/watermark/"));
define('WM_FILE_NAME', 'medium.png');