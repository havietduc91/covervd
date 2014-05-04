<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$url = (!empty($_SERVER['HTTPS'])) ? "https://". DOMAIN : "http://".DOMAIN;
$callback = $url . '/user/oauth-success' ;//currentUrl();
$ssoOptions =
    array(
    'twitter' => array(
                        'signatureMethod' => 'HMAC-SHA1',
                        'callbackUrl' => $callback . '?login=twitter',
                        'siteUrl' => 'http://twitter.com/oauth',
                        'requestTokenUrl' => "https://api.twitter.com/oauth/request_token",
                        'authorizeUrl' => "https://api.twitter.com/oauth/authorize",//authorize",
                        'accessTokenUrl' => "https://api.twitter.com/oauth/access_token",
                        'consumerKey' => 'IMXI3v1ikiCC7bbgEcHeug',
                        'consumerSecret' => 'GZ7jkh4fb7GwA9EmQqkaojwe515cBeNqfCduIj92c',
                        'uri' => 'http://twitter.com/account/verify_credentials.json',
    					'urlFriendIds' => 'https://api.twitter.com/1/friends/ids.json?cursor=-1&user_id=114981752',
    					'urlUserLookup' => 'http://api.twitter.com/1/users/lookup.json?user_id=36688537',
                        'statusUpdateUrl' => 'https://api.twitter.com/1/statuses/update.json',
                        'parameterPost' => array('trim_user' => true, 'include_entities'),
    					'profile_url' => 'https://twitter.com/account/redirect_by_id?id='
                    ),
    'facebook' => array(
                         'api'  => get_conf('fb:app_id',FB_APP_ID),
                         'secret' => get_conf('fb:app_secret',FB_APP_SECRET),
                         'cookie' => true,
                         'appId'  => get_conf('fb:app_id',FB_APP_ID),
                         'domain' => DOMAIN,
                         'callbackUrl' => $callback .'?login=facebook',
                         'permissions' => 'email,read_stream,publish_stream,offline_access,publish_actions',
                         'authorizeUrl' => "https://graph.facebook.com/oauth/authorize?client_id=%s&redirect_uri=%s&scope=%s",
                         'accessTokenUrl' => "https://graph.facebook.com/oauth/access_token?",
                         'dialogUrl' => 'https://www.facebook.com/dialog/oauth?client_id=%s&redirect_uri=%s&scope=%s',//&response_type=token', TODO: return via access_token?
                         'uri' => "https://graph.facebook.com/me?",
                         'postFeedUrl' => 'https://graph.facebook.com/me/feed?',
    		             'profile_url' => 'https://www.facebook.com/profile.php?id='
                    ),
		'yahoo' => array(
						'appID' => 'yYCFAh64',
                        'callbackUrl' => $callback . '?login=yahoo',
                        'requestTokenUrl' => "https://api.login.yahoo.com/oauth/v2/get_request_token",
				        'authorizeUrl' => "https://api.login.yahoo.com/oauth/v2/request_auth",
				        'accessTokenUrl' => "https://api.login.yahoo.com/oauth/v2/get_token",
                        'consumerKey' => 'dj0yJmk9Q2NVOVdYTmtBUGNsJmQ9WVdrOWVWbERSa0ZvTmpRbWNHbzlNVFEzTWpZMk1UYzJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD05Yg--',
                        'consumerSecret' => '8764a73748806c506d70a07cc37dba7ea4564dd8',
                        'uri' => 'http://social.yahooapis.com/v1/user/%s/profile',
                        'profile_url' => 'http://profile.yahoo.com/'
    	),
    	 'google'=>array(
    				  'client_id'=>get_conf('g:client_id',G_CLIENT_ID),
    				  'client_secret'=>get_conf('g:client_secret',G_CLIENT_SECRET),
    				  'app_name'=>get_conf('g:app_name',G_APP_NAME),
    				  'developer_key'=>get_conf('g:dev_key',G_DEV_KEY),//is API Key
    				  'dialogUrl'=>'https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=%s&client_id=%s&scope=%s',
	   				  'redirect_uri' => $callback .'?login=google',
	   				  'callbackUrl' => $callback . '?login=google',
	   				  'scope' => array(
	   				  				    					'https://www.googleapis.com/auth/userinfo.email',
	   				  				    				     'https://www.googleapis.com/auth/userinfo.profile',
	   				  				    			   ),
    				),
    	);
?>
