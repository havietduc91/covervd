<?
function fake_levels()
{
    return array('advance' => 'Advance', 'inter' => 'Intermediate ', 'beginner' => "Beginner");
}

function default_source_value($type)
{
    if ($type == '1')
        return 'Image';
    elseif ($type == '2')
        return t('default_source');
    elseif ($type == '3')
        return 'Youtube.com' ;       
    else
     return t('default_source');
}
function get_cache_dir()
{
    $dir = PUBLIC_PATH. '/cache/';//add folder caches
    if (getenv('SITE'))
        $dir = $dir . getenv('SITE') . '/';
    else
        $dir = $dir . CODENAME . '/';
    return $dir;
}

function get_cache_file_fullpath($is_rest="")
{
    $dir = get_cache_dir();
    
	$file = $requestURI = $_SERVER['REQUEST_URI'];
	$tmp = explode('?', $file);
	$requestURI = $file = $tmp[0];
	$path_parts = pathinfo($file);
	$match = explode("/", $file);
	// Check basename is_numberic true or false
    $k = rand(1,20);
	
    if(!(string)(int)($path_parts['basename'])){
//	    if($match[1] == 'vote' && file_exists($dir . $requestURI . '/' . $k . '.html')){
//            $number = $k;
//	    }
//	    else 
	        $number = 1;
	        
		$file = $requestURI . '/' . $number;
	}
//	else{
//	    if($match[1] == 'vote' && file_exists($dir . $path_parts['dirname'] . '/' . $k . '.html')){
//           $file = $path_parts['dirname'] . '/' . $k;
//	    }
//	}
	 
	//Case REQUEST_URI is home page
	if($_SERVER['REQUEST_URI'] == '/')
	{
		$file = "new/1";
	}
	//api
	$con=explode("/", $requestURI);
	$arr = array('hot','best','vote','new');
	if($is_rest && in_array($con[1],$arr))
	{
		$file='api/' . $file. '/' .getNameForFileApi(get_value('type',array(1,2,3,4)));
	}
    $ret = $dir . $file;
    return $ret;
}

function getNameForFileApi($arr=""){
	if($arr == '')
		return "1_2_3_4";
	else
	{
		foreach ($arr as $row)
		{
			$a[]=(int)$row;
		}
		sort($a);
		$a =array_unique($a);
		$file= implode('_',$a);
		return $file;
	}
}


function date_x_days_ago($days)
{
    $unixts = time() - $days * 24 * 60 * 60 ;
    $date = Inventory_Form_Helper::getInstance()->dateToUnixTimestampInventory('','',$unixts,true);
    return $date;
}
function default_avatar($nodeType)
{
	return ASSETS_CDN . "/images/avatar.gif";
}

function per_page()
{
	return 10;
}

function featured_tag()
{
	return '_featured';
}
function category_link($type)
{
	if ($type == 'quiz')
		return '/cau-do-vui/c62/do-vui.htm';
	elseif ($type == 'story')
		return '/truyen-cuoi/c52/truyen-cuoi.htm';
	elseif ($type == 'image')
		return '/anh-vui/c19/anh-vui.htm';
	elseif ($type == 'video')
		return '/video-clip-hai/c20/clip-hai.htm';
	elseif ($type == 'link')
		return '/link-vui/c78/link-vui.htm';
	elseif ($type == 'flash_game')
		return '/flash-game/c63/flash-game.htm';
	
}
function display_tag($tag)
{
	if ($tag['slug'] == featured_tag())
		return '';
	$count = isset($tag['counter']['s']) ? $tag['counter']['s']: 1;
	if ($count == 0)
	    $count = 1;
	return "<a href='" . node_link('tag', $tag) . "'>" .
	    "<span class='tag label label-primary'><i class='" . get_icon('tag') . "'></i> " . $tag['name'] . "</span></a>" . "<font color='#C0C0C0'> × " . $count ."</font>";
}

// custom functions for codenamex
function display_avatar ($imgUrl, $size = 50, $atype = AS3_AVATAR_FOLDER)
{
	if(empty($imgUrl)) {
		return ($atype == AS3_AVATAR_FOLDER ? DEFAULT_AVATAR_URL : DEFAULT_ITEM_AVATAR_URL);
	}

	//remote url
	if(preg_match("/^(http|https)/", $imgUrl)) {
		return $imgUrl;
	}

	//local url
	if(strpos($imgUrl, dirname(APPLICATION_PATH)) !== false) {
		//local file, then strip the root dir
		$root = dirname(APPLICATION_PATH) . "/public";
		return str_replace($root, '', $imgUrl);
	}

	//$avatar = $atype . '/' . $size .'/'. $imgUrl;
	$avatar = AS3_ITEM_IMAGE_FOLDER . '/' . $size .'/'. $imgUrl;
	return AVATAR_PREFIX . '/' . str_replace("//", "/", $avatar);
}

function use_static_server($img)
{
	if (strpos($img , "http://") === 0 || strpos($img , "https://") === 0)
		return $img;
	$img = str_replace('/ustore/', '/ufiles/', $img);
	if (strpos($img, '/ufiles/') === 0)
		$img = str_replace('/ufiles/', '', $img);
		//$img = str_replace('/ufiles/', STATIC_CDN, $img);
	if(!(strpos($img , "http://") === 0 && strpos($img , "https://") === 0))
		$img = STATIC_CDN . $img;
	return $img;	
}

/**
 * Return array url images $imgUrl
 * 
 * @param unknown_type $row
 */
function story_image($row)
{
	//get story image url
	/*if (isset($row['images']) && isset($row['images'][0]['url']) && $row['images'][0]['url'] != '' 
			&& $row['images'][0]['url'] != STATIC_CDN)
	{
		$imgUrl = $row['images'][0]['url'];
	}*/
    $imgUrl = array();
    if (isset($row['images']) && count($row['images'])> 0)
	{
		for ($i=0;$i<count($row['images']);$i++){
		  if(isset($row['images'][$i]['url']) && $row['images'][$i]['url'] != STATIC_CDN && $row['images'][$i]['url'] != ''){
    		  if(isset($row['wm'])){
        		    if($row['wm'] == 1){
        		         //Image of story watermark
        		         $row['images'][$i]['url'] = '1000/' . $row['images'][$i]['url'];
        		     }    
    		    }
		      
		      $imgUrl[] = use_static_server($row['images'][$i]['url']);   
		  }   
		}
		
		if($imgUrl == null || count($imgUrl)==0){
		    if(isset($row['wm'])){
    		    if($row['wm'] == 1){
    		         //Image of story watermark
    		         $row['url'] = '1000/' . $row['url'];
    		     }    
		    }
		    
		    $imgUrl[] = use_static_server($row['url']);
		}
	}
	elseif (isset($row['url'])){
	    if(isset($row['wm'])){
    		    if($row['wm'] == 1){
    		         //Image of story watermark
    		         $row['url'] = '1000/' . $row['url'];
    		     }    
		    }
		
		$imgUrl[] = use_static_server($row['url']);
	}

	if (!isset($imgUrl) || $imgUrl == STATIC_CDN || $imgUrl == '')
		return '';
		
	return $imgUrl;	
}
function story_thumb_image($row)
{
	//get story image url
	/*if (isset($row['images']) && isset($row['images'][0]['url']) && $row['images'][0]['url'] != ''
	 && $row['images'][0]['url'] != STATIC_CDN)
	{
	$imgUrl = $row['images'][0]['url'];
	}*/
	$imgUrl = array();
	if (isset($row['images']) && count($row['images'])> 0)
	{
		for ($i=0;$i<count($row['images']);$i++){
			if(isset($row['images'][$i]['url']) && $row['images'][$i]['url'] != STATIC_CDN && $row['images'][$i]['url'] != ''){
				if(isset($row['wm'])){
					if($row['wm'] == 1){
						//Image of story watermark
						$row['images'][$i]['url'] = 'thumb/' . $row['images'][$i]['url'];
					}
				}

				$imgUrl[] = use_static_server($row['images'][$i]['url']);
			}
		}

		if($imgUrl == null || count($imgUrl)==0){
			if(isset($row['wm'])){
				if($row['wm'] == 1){
					//Image of story watermark
					$row['url'] = 'thumb/' . $row['url'];
				}
			}

			$imgUrl[] = use_static_server($row['url']);
		}
	}
	elseif (isset($row['url'])){
		if(isset($row['wm'])){
			if($row['wm'] == 1){
				//Image of story watermark
				$row['url'] = 'thumb/' . $row['url'];
			}
		}

		$imgUrl[] = use_static_server($row['url']);
	}

	if (!isset($imgUrl) || $imgUrl == STATIC_CDN || $imgUrl == '')
		return '';

	return $imgUrl;
}
function nav_link($link = NULL , $slug)
{
        //hot/images
        // 1234567
        return $link.'/'.node_type_from_slugs($slug);
}
function story_type($nodeTypeInt = null)
{
	$config = array(
			'1' => 'story',
			'2' => 'image',
			'3' => 'video',
			'4' => 'quiz',
			'5' => 'flash-game',
			'6' => 'link',
			'7' => 'quote',
	        '8' => 'multi-choice'
	);
	if ($nodeTypeInt)
		return isset($config[$nodeTypeInt]) ? $config[$nodeTypeInt] : 'story';
	else 
		return $config;
}

function story_typeint($typeStr = 'story')
{
	$config = story_type();
	foreach ($config as $i => $type)
	{
		if ($type == $typeStr)
			return $i;
	}
	return array();
}


//nodetype is text
function story_icon($storyType)
{

	if ($storyType == 'image')
		$icon = 'picture';
	if ($storyType == 'story')
		$icon = 'edit';
	
	elseif ($storyType == 'video')
	$icon = 'facetime-video';
	elseif ($storyType == 'story')
	$icon = 'file-alt';
	elseif ($storyType == 'quiz')
	$icon = 'question';
	elseif ($storyType == 'flash-game')
	$icon = 'gamepad';
	elseif ($storyType == 'quote')
	$icon = 'quote-right';
	elseif ($storyType == 'quote')
		$icon = 'quote-right';
	elseif ($storyType == 'link')
		$icon = 'external-link';
	elseif ($storyType == 'multi-choice')
	   $icon = 'glyphicon-ok';
	return get_icon($icon);
}

//nodetype glyphicon is text
function story_glyphicon($storyType)
{

	if ($storyType == 'image')
		$icon = 'glyphicon glyphicon-picture';
	elseif ($storyType == 'video')
	$icon = 'glyphicon glyphicon-video-hd';
	elseif ($storyType == 'story')
	$icon = 'glyphicon glyphicon-alt';
	elseif ($storyType == 'quiz')
	$icon = 'glyphicon glyphicon-question';
	elseif ($storyType == 'flash-game')
	$icon = 'glyphicon glyphicon-gamepad';
	elseif ($storyType == 'quote')
	$icon = 'glyphicon glyphicon-quote-right';
	elseif ($storyType == 'quote')
		$icon = 'glyphicon glyphicon-quote-right';
	elseif ($storyType == 'link')
		$icon = 'glyphicon glyphicon-external-link';
	elseif ($storyType == 'multi-choice')
	$icon = 'glyphicon glyphicon-glyphicon-ok';
	return $icon;
}

function type_slug($nodeType = null)
{
    if(LANGUAGE == 'vn')
    {
    	$config = array(
    		'1' => 'truyen-cuoi',
    		'2' => 'anh-vui',
    		'3' => 'video-clip-hai',
    		'4' => 'cau-do-vui',
    		'5' => 'flash-game',
    		'6' => 'link-vui',
    		'7' => 'cau-noi-noi-tieng',
	        '8' => 'chon-quan-diem',
    	);
    }
    else //if(LANGUAGE == 'en')
    {
        $config = array(
            '1' => 'jokes',
            '2' => 'pics',
            '3' => 'videos',
            '4' => 'quiz',
            '5' => 'flash-game',
            '6' => 'links',
            '7' => 'quotes',
            '8' => 'multi-choice',
        );
    }
	if($nodeType)
		return isset($config[$nodeType]) ? $config[$nodeType] : 'story';
	else 
		return $config; 
}


function node_type_from_slug($nodeSlug ,$slug)
{
	$config = type_slug();
	foreach ($config as $i => $slug)
	{
		if ($slug == $nodeSlug)
			return $i;
	}
}

function node_type_from_slugs($slug)
{
    $config = type_slug();
    foreach ($config as $i => $val)
    {
        if ($slug == $i)
            return $val;
    }
}
/**return link of a story **/
function node_link($type = 'story', $row){
    
	if ($type == 'story')
	{
	    if (!getenv('SITE') || getenv('SITE') == 'stir' || getenv('SITE') == 'hoibinet')
		    $link = '/' . type_slug($row['type']) . '/' . $row['iid'].'-' . $row['slug'] .'.html';
	    else 
	        $link = '/' . type_slug($row['type']) . '/' . $row['iid'].'.html';
	}
	elseif ($type == 'tag')
		$link = "/tagged/" . $row['slug'];
	else 
		$link = "/$type/{$row['id']}";
	return $link;
}
//node link for remove staticache
function node_link_cache($type = 'story', $row){
	$link = array();
	if ($type == 'story')
	{
		$link[] = '/' . type_slug($row['type']) . '/' . $row['iid'].'.html';
		$link[] = '/' . type_slug($row['type']) . '/' . $row['iid'].'.html.json';
	}
	elseif ($type == 'tag')
	$link = "/tagged/" . $row['slug'];
	else
		$link = "/$type/{$row['id']}";
	return $link;
}
/**return link of a user**/
function user_link($u = null, $absolute_url = false)
{
    if ($u == null)
        $u = Zend_Registry::get('user');
    $url = "/user/" . $u['iid'];
    if ($absolute_url)
    {
        return SITE_URL . $url;
    }
    else 
        return $url;
}

/**calculate user's karma factor based on number of stories, number of comments and number of votes
 * @param:$action = 'vote_comment', 'new_comment', 'new_story',....
 * @param:$content = comment if action = 'vote_comment', etc...
 * @param:$actor = user who triggers the action 
 * @param:$subjectUser = user whose karma will be affected
 * notes:sometimes $actor == $subjectUser (for actions like 'new_story')
 * */
function calculate_karma($actor, $action, $subjectUser){
	$karma = 0;
	switch ($action){
		case 'new_story':   // update when approve post 
			$karma = 5;
			break;
		case 'del_story':   //update when delete post
			$karma = -5.5;
			break;
		case 'new_comment':
			$karma = 1;
			break;
		case 'del_comment':
			$karma = -2;
			break;
		case 'story_voted_up':
			$karma = 2 + log10($actor['counter']['k']+1);
			if($karma <0) $karma =0;
			break;
		case 'story_voted_down':
			if($actor['counter']['k'] >= 0)
				$karma = -2 - log10($actor['counter']['k']+1);
			else $karma = -2;
			break;
		case 'comment_voted_up':
			if($actor['counter']['k'] >= 0)
				$karma = log10($actor['counter']['k'] + 1);
			else $karma = 0 ;
			break;
		case 'comment_voted_down':
			if($actor['counter']['k'] >= 0)
				$karma= - log10($actor['counter']['k'] + 1); // tranh bi am
			else $karma = 0;
			break;
		default:
			break;

	}
	return $karma;
} 
function calculate_node_point($actor,$action){
	$point=0;
	switch($action){
		case 'story_voted_up':
			$point = 2 + log10($actor['counter']['k']+1);
			break;
		case 'story_voted_down':
			$point = -2 - log10($actor['counter']['k']+1);
			break;
		case 'comment_voted_up':
			$point = log10($actor['counter']['k'] + 1);
			break;
		case 'comment_voted_down':
			$point= - log10($actor['counter']['k'] + 1); // tranh bi am		
		default:
			break;
	}
	return $point;
	
}
/**Get img link tu DB*/
function img_link($name, $size = null){
	if($size)
		return '/ufiles/' . $size . '/' .$name;
	return '/ufiles/' . $name;
	
}

function get_default_perms()
{
	return array('new_story', 'vote_story', 'vote_story_comment', 'new_story_comment');
}

//return a random image
function not_found()
{
	$images = array(ASSETS_CDN . "/images/404-man.jpg", 
			ASSETS_CDN . "/images/404-cut.jpg",
	);
	return $images[array_rand($images)];
}
function render_flash_game($row, $width = 420, $height = 315){
	$swfFile = "/ufiles/flash/" . $row['url']; 
	return 
	'<object width="' . $width . '" height="' .$height . '">' . 
	'<param name="movie" value="' . $swfFile . '">' .
	'</param>' .
	'<param name="allowFullScreen" value="true"></param>' .  
	'<param name="allowscriptaccess" value="always"></param>' .
	'<embed src="' . $swfFile . '" type="application/x-shockwave-flash"' .
		'width="' . $width . '" height="' . $height . '" allowscriptaccess="always" allowfullscreen="true"></embed></object>';
	
}
function render_yt_embed_video($vid, $width = 420, $height = 315)
{
	return "<object width='$width' height='$height'>" . 
	"<param name='movie' value='http://www.youtube.com/v/{$vid}?version=3&amp;hl=en_US'>".
	"</param>" .
	"<param name='allowFullScreen' value='true'></param>" .
	"<param name='allowscriptaccess' value='always'></param>" .
	"<embed src='http://www.youtube.com/v/" . "{$vid}?version=3&amp;hl=en_US' type='application/x-shockwave-flash'".
		"width='$width' height='$height' allowscriptaccess='always' allowfullscreen='true' wmode='transparent'
	></embed></object>";
}

//facebook parse int . comment,like,share
function fb_counter($url,$type)
{
	/**
     * Add new node
     * @param $url : url of site that we need count likes,comments,shares
     * @param $type : string 'like' | 'comment' | 'share' | 'commentsbox'  
     * @return number of likes,comments,shares   
     * */
	if($type == 'like')
	{
		$like="<like_count>";
		$like1="</like_count>";
		$pos = 18;
	}
	if($type == 'share')
	{
		$like="<share_count>";
		$like1="</share_count>";
		$pos = 19;
	}
	if($type == 'commentsbox')
	{
		$like="<commentsbox_count>";
		$like1="</commentsbox_count>";
		$pos = 25;
	}
	if($type == 'comment')
	{
		$like="<comment_count>";
		$like1="</comment_count>";
		$pos = 21;
	}
    $addr="http://api.facebook.com/restserver.php?method=links.getStats&urls=".$url;
    //$page_source=file_get_contents($addr);
    $client = new Zend_Http_Client();
    $client->setMethod(Zend_Http_Client::GET);
    $client->setUri($addr);
    $response = $client->request();
    $page_source=$response->getBody();
    $page = htmlentities($page_source);
    $lik=strpos($page,htmlentities($like));
    $lik1=strpos($page,htmlentities($like1));
    $fullcount=strlen($page);
    $a=$fullcount-$lik1;
    $aaa=substr($page,$lik+$pos,-$a);
    $aaa1=substr($page,605,610);
    return $aaa;
}
require_once('lang.php');

