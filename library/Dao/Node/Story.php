<?php
/**
 * scheme of Story 
 * Editor: Chungth (huychungtran@gmail.com)
 * **/
class Dao_Node_Story extends Dao_Node_Site
{
	public function renewUser($data)
	{
	    $lu = Zend_Registry::get('user');
		$crawler = get_conf("crawler","crawler");
		$nrOfRandomUser = get_conf("nrOfRandomUser","100");
		if($lu['lname'] == $crawler)
		{
			$u = Cl_Dao_Fake::getInstance()->getRandomUser($nrOfRandomUser);
			Zend_Registry::set('user', $u);
			$data['u']['id'] = $u['id'];
			$data['u']['iid'] = $u['iid'];
			$data['u']['lname'] = $u['lname'];
			$data['u']['name'] = $u['name'];
			$data['u']['avatar'] = $u['avatar'];
			$data['u']['ts'] = $u['ts'];
			$data['u']['counter'] = $u['counter'];
			if(get_conf('crawler_is_new') == '1')
			{
			    $data['status']= 'approved';
			}
			elseif(get_conf('crawler_is_hot') == '2')
			{
			    $data['status']= 'approved';
			    $storyPointFake = 2 + log10($lu['counter']['k'] + 1); 
			    $data['counter']=array('vote'=>0,
			            'point'=>$storyPointFake,
			            'c' => 0,
			            'fbc' => 0,
			            'tc' => 0,
			            'vd'=>0,
			            'hn'=>0,
			            'v' => 0,
			    );
			}
		}
		return $data;
	}
	public $hotnessRanking = true;
   
    public $nodeType = 'story';
    
    public $cSchema = array(
    		'id' => 'string',
    		'iid' => 'string',
    		'name' => 'string',
    		'avatar' => 'string',
    		//add other stuff u want
    );
        
    protected function relationConfigs($subject = 'user')
    {
    	if ($subject == 'user')
    	{
    		return array(
    				'1' => '1', //vote | like
    				'2' => '2', //follow
    				'3' => '3' , //flag as spam
    				'4' => '4' , //vote down
    				'5' => '5', // choose pair 1
    		        '6' => '6' ,// choose pair 2
    		);
    	}
    }
    
	protected function _configs(){
	    $user = Dao_User::getInstance()->cSchema;
	    $tag = Dao_Node_Tag::getInstance()->cSchema;
    	return array(
    		'collectionName' => 'story',
        	'documentSchemaArray' => array(
        		'iid' => 'int', //incremental sql-like uniq id
        		'name' => 'string', 
        		'avatar' => 'string',		
        		'content' => 'string',
    	        'content_uf' => 'string', //unfiltered content where <span class='item'> is converted to proper item links
    	        'by' => 'string', //quote author 
        		'tags' => array( 
    	            $tag
    	        ),
        		'type' => 'int', // 1 => story, 2 => image, 3 => video, 4 => flash-game, 5 => quiz
        		'u' => $user, //who posted this	
        		'counter'	=>	array(
        			'c' => 'int', // so comment
        			'fbc' => 'int', //facebook comment
    		        'tc' => 'int', //tong so comment
                    'vote' => 'int', //votes
    	        	'vd' =>'int', //votes down
    	        	'point'=>'float', // total point of story
    	        	'hn' =>'float', // hotness score of story
    	        	'v' => 'int', //view
        			'fbl' => 'int', //fb likes
                    'spam' => 'int',//spam
    		        'p1_vote' => 'int',// number vote for pair1
    		        'p2_vote' => 'int',// number vote for pair2 multi-choice question
                           			                
    	        ),
        		'comments' =>//'array.subDocument',
						array ( //comments can be embedded right into the node.
       						array(
        						'u'	=> $user,
                                //'content'	=>	'string',
       					        'content' => 'string', //filtered content
                                'pid' => 'string', //parent comment id   
                                'ts'	=>	'int',
                                'id'	=>	'string',
                               
                                'counter' => array(
        	        							'vote' => 'int',
        										'tw'   => 'float', // total weight of comment tree.
        	   				 					),
        	   				 	'child_comments'=> 'mixed',
                            )
                        ),
        		'ts' => 'int',
                'ats' => 'int',//approved timestamp
                'wm' => 'int', //watermark if wm = 1 => story watermarked, wm = 0 not watermarked 
        		'status' => 'string', //[queued,p,s] queue, approved,  spammed
				'slug' => 'string',
                'url'=>'string',
                'h' => 'float', //height image
                'w' => 'float', //width image
                'ytid'=>'string', //youtube video id
                'yt_duration' => 'string',
                'seo'=> array( //all seo field
                        'title'=>'string',
                        'desc'=>'string',
                        ),
                'images' => array(
                    array(
                        'id' => 'string',
                        'ext' => 'string',
                    	'url' => 'string',
                    	'path' => 'string',
                        'h' => 'float', //height image
                        'w' => 'float', //width image
                    )
                ),
    	        'pair1' => 'string', //pair1 and paor2 for multi-choice-question-like quyet.de
    	        'pair2' => 'string',
                'source' =>'string', //nguon goc cua strory
                'answer' => 'string', //for quizzes
                'img_concate' => 'string',
                'flash' => 'string',
        		'is_iphone' => 'int',
        		'game_category' => 'string',
        		'promote' => 'int',//promoted time
        		'weight' => 'float',
                'next'	=>	array(
        			'iid' => 'int', 
                    'slug' => 'string', 
                	'type' => 'int',
    	        ),
                'prev'	=>	array(
        			'iid' => 'int', 
                    'slug' => 'string',
    	        	'type' => 'int',
    	        ),
        	),
    		'indexes' => array(
    					array(
    							array('iid' => 1),
    							array("unique" => true, "dropDups" => true)
    					)
    			)
    	);
	}

	
    /**
     * Add new node
     * @param post data Array $data
     */
	public function beforeInsertNode($data)
	{
	    //die('die at beforeinsertNode');
		$data = $this->renewUser($data);
		if (!isset($data['iid']))
		{
			$redis = init_redis(RDB_CACHE_DB);
			$data['iid'] = $redis->incr($this->nodeType . ":iid"); //unique node id
		}
		
		//check if data-image-url not found
		if(isset($data['url']) && empty($data['images']) && $data['type'] == '2' && $data['is_iphone_fun'] == '0')
		{
    		$checkImage =  @getimagesize($data['url']);
    		if(!$checkImage){
    		     return array(
                    'success' => false,
                    'err' => "<div style='color :red'>Ảnh không tìm thấy</div>"
                );
    		}
		}
		
		if(isset($data['ts']) && $data['ts']){
		    $data['ats'] = $data['ts'];
		}
		//if success. Insert the newly added concepts if any
		
		if (isset($data['tags']) && count($data['tags']) > 0)
		{
			//unset  tag name 'featured' if user's not allowed
			foreach ($data['tags'] as $k => $tag){
				if($tag['name'] == featured_tag() && !has_perm('admin_story')){
					unset($data['tags'][$k]);
				}
			}
	
			$r = Dao_Node_Tag::getInstance()->insertNewTags($data['tags']);
			if (!$r['success'])
				return $r;
			else 
				$data['tags'] = $r['result'];
		}
		
		//set slug for story
		if (!isset($data['slug']))
		{
			$tempSlug = Cl_Utility::getInstance()->generateSlug($data['name']);
			$data['slug'] = $this->generateUniqueSlug(explode('-', $tempSlug));
		}
		
		if (!isset($data['seo']['title']) || empty($data['seo']['title']))
            $data['seo']['title'] = $data['name'];
        if(!isset($data['seo']['desc']) || empty($data['seo']['desc'])) {
            $data['seo']['desc']=$data['name'];
            if(!empty($data['content'])){
                $data['seo']['desc']= $data['seo']['desc'].' '.
                word_breadcumb($data['content'], 100);
            }
            else{
                //$user=Zend_Registry::get('user');
                $data['seo']['desc']= $data['seo']['desc'];
            }     
        }
       if(!isset($data['status'])) {
            $data['status'] = 'queued';
        }
        if(!isset($data['counter'])){
        	$data['counter']=array('vote'=>0,
        						   'point'=>0,
        	                       'c' => 0,
                        	       'fbc' => 0,
                        	       'tc' => 0,
        						   'vd'=>0,
        						   'hn'=>0,
        						   'v' => 0,
                        	       'p1_vote' => 0,// number vote for pair1
                        	       'p2_vote' => 0,// number vote for pair2 multi-choice question
        	        
        			);
        }
        
        if(!isset($data['wm'])){
            $data['wm'] = 0;
            /*
             * watermark: = 1 => story watermarked
             * 			  = 0 => story not watermarked
             */ 
        }
        //assume user votes his story right away
        // preprocess if user tick into iphone fun field
        
        if(isset($data['is_iphone_fun']) && $data['is_iphone_fun'] != 0){
        	//change text into imagec
        	$content = str_replace(array('<br />', '<br>', '<p>', '</p>', '<div>','</div>'), '|', 
        	                strip_tags(trim($data['content_uf']), '<br/><br><p></p><div></div>'));
        	$arrContent = explode('|', $content);
                  	
        	$newArrayContent = array();
        	
        	foreach($arrContent as $v){
        		//remove special nbsp;
        		//if v does not contain any alphabes or number , just white spaces, remove it
        		$v = str_replace("&nbsp;","",$v);
        		
        		$v = preg_replace("/\s\s+\n\r\t/", " ", $v);
        		$v = preg_replace("/[\r\n]+/", "", $v);
        		
        		
        		$v = trim($v);
        		
        		if ($v == '' || is_null($v) || strlen($v) == 0)
        		{
        		    continue;
        		}
        		else 
        		{
            		$newArrayContent[] = $v;
        		    
        		}

        	}
        	
        	$isDash = true; 
        		foreach ($newArrayContent as $k => &$v){
        			if($v[0] == '-'){
        				$isDash = !$isDash;
        			}
        			if(!$isDash){
        				if($v[0] == '-'){
        					$v[0] = ' ';
        				}
        			}else{
        				if($v[0] != '-'){
        					$v = '- '. $v;
        				}
        			}
        			$v = trim($v);
        			if($v[strlen($v)-1] != '\n'){
        				$v .= "\n";
        			}
        			
        		}
        	
        	$content = implode("", $newArrayContent);
        	
        	$configs = array(
        	        'text' => $content, 
        	       'time' => date('H:I A'), 
        	       'sender' => $data['sender_name'],
        	       'hide_sender'=>true
        	);
        	$Iphone = new HImage_Image($configs);
        	$img = $Iphone->render();
        	$filename  =  date('Y_m_d_H_i_s') . ".png";
        	$path = PUBLIC_FILES_UPLOAD_PATH . '/'. $filename;
        	imagepng($img,$path);
        	$data['url'] = $filename;
        	//die($data['url']);
        	$data['is_iphone'] = 1;
        	if(isset($data['images']))
        		unset($data['images']);
        }else{
        	$data['is_iphone'] = 0;
        	// crawl image, flash file from remote url
        	if(isset($data['url']) && ($data['type'] == 2 || $data['type'] == 4 ||$data['type'] == 8)) {
        		
        		$type = 'image';
        		$isCrawl = false;
        		
        		if($data['type'] == 4) {
        			$type = 'swf';
        		}
        			
        		if (strstr($data['url'], "http://") || strstr($data['url'], "https://"))
        		{
        			$isCrawl = true;
        			$client = Cl_Utility::getInstance();
        			$filename = $client->processRemoteFile($data['url'], $type);
        			// ex: $filename = "\/downloaded\/2013\/07\/29\/1375123542_3.jpg";
        		}
        		else{
        			$filename = $data['url']; //local file. Usually from robot
        		}
        		
        		if($filename) {
        			$file = realpath(PUBLIC_PATH) . '/' . $filename;
        			//TODO: Theo dinh dao cua $file ta thay thua fun vi the phai bo phan CODENAME
        			if($data['type'] == 2) {
        				
        				// resize image
        				$imgObj = Cl_Image::getInstance(array('file_name' => $file));
        				$ext = $imgObj->getFileType();
        			}
        			elseif($data['type'] == 4) {
        				$ext = 'swf';
        			}
        			$data['ext'] = $ext;
        			if($data['type'] == 2 || ($data['type'] == 4 && $isCrawl)) 
        			{
	        			$fileData = array(
	        					'u' => $data['u'],
	        					'name' => basename($file),
	        					'ext' => $ext,
	        					'path' => $file
	        			);
	        			$f = Cl_Dao_File::getInstance()->insert($fileData);
	        			if($f['success']) {
	        				$fileObj = $f['result'];
	        				//TODO: Them 'ufile' vao data['url']
	        				$data['url'] =  $fileObj['id'] . '.' . $fileObj['ext'];
	        				if($data['type'] == 2) {
	        					$absoluteFile = PUBLIC_FILES_UPLOAD_PATH .'/'. $fileObj['id'] . '.' . $fileObj['ext'];
	        					// TODO: add bg resize
	        					$this->uploadAndResizeImage($fileObj, true);
	        				}
	        				elseif($data['type'] == 4) { // flash file
	        					$absoluteFile = PUBLIC_FILES_UPLOAD_PATH .'/flash/'. $fileObj['id'] . '.' . $fileObj['ext'];
	        				}
	        				
	        				if($isCrawl)
	        					Cl_Utility::copyFile($file, $absoluteFile);
	        			}
        			}
        		}
        	}
        	
        	if(isset($data['images']) && !is_array($data['images'])) {
        		die($data['images']);
        		unset($data['images']);
        	}
        	elseif(isset($data['images'])) {
        		for($i=0;$i<count($data['images']);$i++){
        		    $data['images'][$i]['url'] = $data['images'][$i]['id'] . '.' . $data['images'][$i]['ext'];
        		}
        		$image = end($data['images']);
        		if($image['id']){
        			$data['url'] =  $image['id'] . '.' . $image['ext'];
        		}
        	}
        }
        
        //TODO: if user='crawler'
        $lu = Zend_Registry::get('user');
        if ($lu['lname'] == 'crawler')
        {
            //choose 1 fake user
            // $fakeUser = User_Dao::getInstance()->getRandomFakeUser();                     
            $tmp = Cl_Dao_Util::getUserDao()->getCacheObject($fakeUser);
            if ($tmp['success'])
                $data['u'] = $tmp['result'];
        }
        return array('success' => true, 'result' => $data);
	}
	
	public function afterInsertNode($data, $row)
	{
	    //Update width and heiht for image
	    if(isset($data['images']) && count($data['images']) > 0){
	        $images = array();
	        foreach ($data['images'] as $image){
	            $newImg = $image;
	            $urlImg = STATIC_CDN . $image['url'];
	            //Get height and width image
                //list($width, $height, $type, $attr) = getimagesize($urlImg);    
	            list($width, $height, $type, $attr) = getimagesize(PUBLIC_FILES_UPLOAD_PATH .'/'. $image['url']);

                $newImg['h'] = (isset($height) && $height != null) ? $height : 0;
                $newImg['w'] = (isset($width) && $width != null) ? $width : 0;
	            $images[] = $newImg;
	        }
	        
	    }else{
	        $images = $data['images'];
	    }
		if($row['type'] == 2)
		{
		    $height = 0;
		    $width = 0;
		    if(isset($data['url']) && $data['url'] != ''){
		        //list($width, $height, $type, $attr) = getimagesize(STATIC_CDN. $data['url']);
		    	list($width, $height, $type, $attr) = getimagesize(PUBLIC_FILES_UPLOAD_PATH .'/'. $data['url']);
		    }
	
		    $height = (isset($height) && $height != null) ? $height : 0; 
		    $width = (isset($width) && $width != null) ? $width : 0;

		    if(!isset($data['images']) || $data['images'] == array()){
		    	preg_match('#\.([^\.]+)$#',$data['url'],$matches);
		    	
		    	$image = array(
		    			'id' => str_replace($matches[0],'',$data['url']),
		    			'ext' =>  str_replace('.','',$matches[0]),
		    			'url' => $data['url'],
		    			//'path' => 'string',
		    			'h' => $height, //height image
		    			'w' => $width, //width image
		    	);
		    	
		    	$images[] = $image;
		    }
		    
		    $where = array('id' => $row['id']);
		    $dataUpdate = array('$set'=>array(
	                            'images' => $images,
	                            'h' => $height,
	                            'w' => $width    
                            )
                     );
                           
	        $this->update($where, $dataUpdate);
	                            
	        // resize images
	        $data = $this->renewUser($data);
		    $this->resizeStoryImages($row, $data);
	        
		}
        if($data['type'] == 2 && isset($data['img_canvas'])) {
        	$imageData = substr($data['img_canvas'], strpos($data['img_canvas'], ",") + 1);
        	$decodedData = base64_decode($imageData);
        	if($decodedData) {
        		file_put_contents(PUBLIC_FILES_UPLOAD_PATH .'/' . $row['id'] . '.png', $decodedData);
        		$url = $row['id'] . '.png';
        		$dataUpdate = array('$set' => array('url' => $url));
        		$where = array('id' => $row['id']);
        		$this->update($where, $dataUpdate);
        	}
        }
        	
        /*Extend function afterInsertNode($data, $row) of Site.php*/
        /**
         * only update karma while upload story if user is administrator
         */
        if($row['status'] == 'approved')
            parent::afterInsertNode($data, $row);
        
    	// always refresh upcomming cache here.
    	$this->refreshUpcoming();
    	
    	/**
    	 * Haduc update next-iid and prev-idd of story after upload 12.8.2013
    	 * After upload story we excute:
    	 * 	- Set prev-iid of element is iid of element has max ts 
    	 * 	- Set next-idd of element is iid of element has min ts
    	 */
    	$this->updateNextandPrevIidAfterUpload($data,$row);
    	
    	/**
    	 * Delete cache vote or new after upload
    	 * 	if user upload is root(admin) , status = approved => story new change => delete cahce new
    	 *  if user upload is'nt root(admin) , status = queue => story vote change => delete cahce vote
    	 */
    	if($data['status'] == 'approved')
    		$type = 'new';
    	else if($data['status'] == 'queued')
    		$type = 'vote';
    	else 
    		$type = 'vote';
    	
    	//Recalculate new|vote cache redis
    	$this->initNewOrVoteCacheList($data['status'],$row['type']);
    	
    	$this->deleteStaticCacheOfType($type,1,10);
    	//Delete cache in widget story 
    	$this->deleteStaticCacheOfType("widget/".$type,1,2);
    	
    	//Delete cache vote or new at new/anh-vui ....... vote/video-hai.. vvv
    	$type2 = $type . '/' . type_slug($row['type']);
    	
    	$this->deleteStaticCacheOfType($type2,1,10); 
		//Delete cacche of api
		$this->deleteStaticCacheOfTypeApi('new',$type);
    	
        return array('success'=> true, 'result' => $row);
	}
	
    /******************************UPDATE****************************/
    public function beforeUpdateNode($where, $data, $currentRow)
    {
        /*
         * $data['$set'] 
         * You have $data['$set']['_cl_step'] and $data['$set']['_u'] available
         */
        //set approved timestamp
        if ($data['$set']['_cl_step'] == 'status' && $data['$set']['status'] == 'approved'){
            $data['$set']['ats'] = time();    
        }
        
        if(isset($data['$set']['type']) && $data['$set']['type'] == 2 && isset($data['$set']['images'])) {
            $newImgs = array();
            if(isset($currentRow['images'])) {
                foreach($currentRow['images'] as $oldImg) {
                    $currentImgs[$oldImg['id']] = $oldImg;
                }
                
                foreach($data['$set']['images'] as $img) {
                    if(!isset($currentImgs[$img['id']]))
                        $newImgs[] = $img;
                }
            }
            else {
                $newImgs = $data['$set']['images'];
            }
            
            if(count($newImgs) > 0) {
                $data['$set']['new_image_to_resize'] = $newImgs;
            }

            if(isset($data['$set']['img_canvas'])) {
                $imageData = substr($data['$set']['img_canvas'], strpos($data['$set']['img_canvas'], ",") + 1);
                $decodedData = base64_decode($imageData);
                if($decodedData) {
                    file_put_contents(PUBLIC_FILES_UPLOAD_PATH .'/' . $currentRow['id'] . '.png', $decodedData);
                    $data['$set']['url'] = $currentRow['id'] . '.png';
                }
            }
        }
        if ($data['$set']['_cl_step'] == 'feature')
        {
	        if(isset($data['$set']['is_feature'])){
	        	$currentTags = isset($currentRow['tags']) ? $currentRow['tags'] : array();
	        	if($data['$set']['is_feature'] == 1){
		        	
		        	//check if tag featured exist or not
		    		$where = array('name' => featured_tag());
		    		$r = Dao_Node_Tag::getInstance()->findOne($where);
		    		if($r['success']){
		    			 $c = Dao_Node_Tag::getInstance()->getCacheObject($r['result']);
		    			 if ($c['success'])
		    			 	$currentTags[] = $c['result'];
		    		}
		    		else  //insert tag 'feature'
		    		{
		    			$currentTags[] = array(
		    				'id' => (string) new MongoId(),
		    				'name' => featured_tag(),
		    				'slug' => featured_tag(),
		    				'_new' => true
		    			);
		    		}
		    	}
		    	elseif($data['$set']['is_feature'] ==0){
		    		
			    	foreach ($currentTags as $k => $tag){
						if($tag['name'] == featured_tag()){
							unset($currentTags[$k]);
						}
				
					}	
		    	}
		    	if (count($currentTags) > 0)
		    			$data['$set']['tags'] = $currentTags;
	        }
	    	
        }
        
        //if success. Insert the newly added concepts if any
        if (isset($data['$set']['tags']) && count($data['$set']['tags']) > 0)
        {
           //unset  tag name 'featured'
			foreach ($data['$set']['tags'] as $k => $tag){
				if($tag['name'] == featured_tag() && !has_perm('admin_story')){
					unset($data['$set']['tags'][$k]);
				}
				
			}
			
        	$r = Dao_Node_Tag::getInstance()->insertNewTags($data['$set']['tags']);
        	if (!$r['success'])
        	{
        		return $r;
        	}
        	else 
        	{
        		$data['$set']['tags'] = $r['result'];
        	}
        }
        return array('success' => true, 'result' => $data);
    }
    
	public function afterUpdateNode($where, $data, $currentRow)
    {
        // image types
        if( isset($data['set']['type']) && $data['$set']['type'] == 2 && isset($data['$set']['images'])) {
            //resize new images if exists
            if(isset($data['$set']['new_image_to_resize'])) {
                // resize images
                foreach($data['$set']['new_image_to_resize'] as $img ) {
                    $this->uploadAndResizeImage($img);
                }
            }
            // delete images
            $imagesDeleted = explode(',', $data['$set']['images_deleted']);
            $idsDeleted = array_filter($imagesDeleted);
            if(count($idsDeleted) > 0) {
                foreach($idsDeleted as $id) {
                    $ids[] = new MongoId($id);
                }
                $dWhere = array('_id' => array('$in' => $ids));
                $f = Cl_Dao_File::getInstance()->find(array('where' => $dWhere));
                if($f['success'] && $f['count'] > 0) {
                    $fileObj = $f['result'];
                    $t = Cl_Dao_File::getInstance()->delete($dWhere);
                    if($t['success']) {
                        foreach ($fileObj as $img) {
                            //TODO: remove all images size.
                            $filename = PUBLIC_FILES_UPLOAD_PATH .'/' . $img['id'] . '.' . $img['ext'];
                            unlink($filename);
                        }
                    }
                }
            }
        }

    	//update user karma if change status to Approved
		if ($data['$set']['_cl_step'] == 'status')
		{
	    	if (isset($data['$set']['status']) && $data['$set']['status'] == 'approved'){	
	    		$where = array('id' => $currentRow['u']['id']);
				$update = array('$inc' => array('counter.p' => 1,
											'counter.k' => calculate_karma($currentRow['u'], 'new_story', $currentRow['u']),
											));
				Dao_User::getInstance()->update($where, $update);
				$this->initNewOrVoteCacheList('queued',$data['set']['type']);
				$this->initNewOrVoteCacheList('approved',$data['set']['type']);
				
            	//Update approved timestamp
            	$typeNode = type_slug($data['set']['type']);
                
                $this-> deleteStaticCacheOfType('new',1,10);
                $this-> deleteStaticCacheOfType('vote',1,10);
                
                //Delete cahce of widget story
                $this-> deleteStaticCacheOfType('widget/new',1,2);
                $this-> deleteStaticCacheOfType('widget/vote',1,2);
                
                $this-> deleteStaticCacheOfType('new'.$typeNode,1,10);
                $this-> deleteStaticCacheOfType('vote'.$typeNode,1,10);
                $this->deleteStaticCacheOfTypeApi('new',$type);
	    	}
		}
		else $this->deleteStaticCacheOfTypeApi('all',$type);
			    	
    	if ($data['$set']['_cl_step'] == 'feature'){
    		$this->refreshBest();
    	}
    	
    	$this->deleteStaticCache($currentRow);
    	
    	return array('success' => true, 'result' => $data);    
    }   

    public function deleteStaticCache($row)
    { 
    	if (is_string($row))
    	{
    		$where = array('id' => $row);
			$t = $this->findOne($where);
			if ($t['success'])
				$row = $t['result'];
			else 
				return false;    		
    	}
    	//Delete cache if exists
    	$dir = get_cache_dir();
    	$nodelinks = node_link_cache('story', $row);
    	foreach( $nodelinks as $node)
    	{
	    	$filename = $dir . $node;
	    	if(file_exists($filename)){
	    		unlink($filename);
	    	}
    	}
    	return array('success' => true);
    }
    
    /**
     * Removed folder by type = hot | new| best| vote
     * @param unknown_type $type
     */
	public function deleteStaticCacheOfType($type,$first,$last)
    {
    	//Delete cache if exists
    	$dir = get_cache_dir();
    	$dirname = $dir . '/' . $type;
    	for($i=$first;$i<=$last;$i++){
    	    if(file_exists($dirname."/".$i.'.html') && !is_dir($dirname."/".$i.'.html')){
    			unlink($dirname."/".$i.'.html');
    	    }
    	    if(file_exists($dirname."/".$i.".json")){
    			unlink($dirname."/".$i.".json");
    	    }
    	    if(is_dir($dirname."/".$i)){
    	        $this->rmdir_recursive($dirname."/".$i);
    	    }
    	}
    }
    public function deleteStaticCacheOfTypeApi($action,$type)
    {    	
    	$dir=get_cache_dir();
    	$dirname=$dir.'/api';
    	//new
    	if($action=='new')
    	{
    		if(is_dir($dirname."/vote"))
    			$this->rmdir_recursive($dirname.'/vote');
    		if(is_dir($dirname.'/new'))
    			$this->rmdir_recursive($dirname.'/new');
    	}
    	if($action=="all")
    	{
    		if(is_dir($dirname))
    			$this->rmdir_recursive($dirname);
    	}	
    }
   
	/******************************INSERT_COMMENT****************************/
    /**
     * You have $node = $data['_node'];
     */
	public function beforeInsertComment($data)
	{
	    $node = $data['_node'];
	    	        
        $data['node'] = array(
				'id'	=>	$data['nid'],
		);
        
		$data['status'] = 'queued';
        if(in_array('root', $data['_u']['roles']) || in_array('admin', $data['_u']['roles'])) {
            $data['status'] = 'approved';
        }
        if (isset($node['name']) && !empty($node['name']))
            $data['node']['name']	=	$node['name'];
        else if (isset($node['content']))
            $data['node']['name']	= word_breadcumb($node['content'], CACHED_POST_TITLE_WORD_LENGTH);
	    
        if(isset($data['attachments']) && (is_null($data['attachments']) || $data['attachments'] == ''))
        	unset($data['attachments']);
       
		return array('success' => true, 'result' => $data);
	}
		
	/**
     * You have $node = $data['_node'];
	 * Add new comment to a post
	 * @param POST data $data
	 */
	public function afterInsertComment($data, $comment)
	{
		//upate story comment couter and update point story after comment
		$actor = $data['u'];
		$action = 'new_comment';
		$node = $data['_node'];
		$pointDelta = parent::pointDelta($actor, $action, $node);
        
		$where = array('id'=> $comment['node']['id']);
		$update= array('$inc'=> array('counter.c'=>1,'counter.tc'=>1, 'counter.point'=>$pointDelta));
		
		Dao_Node_Story::getInstance()->update($where,$update);
		//update user karmar
		$where = array('id' => $comment['u']['id']);
		$update = array('$inc' => array('counter.c' => 1,
										'counter.k' => calculate_karma(Zend_Registry::get('user'), 
												'new_comment', Zend_Registry::get('user')),
										));
		Dao_User::getInstance()->update($where, $update);
		//cache comment to node 
		$order= array('counter.tw'=>-1 , 'ts'=>-1);
        Dao_Node_Story::getInstance()->cacheCommentsToNode($comment['node']['id'], $order);
        
        // refresh & reculate hotness
        $this->incActivityCountAndCheckHotnessCache(array('ts' => time()),$node['iid'],$node['counter']['hn'],$node['type']);
        
        //Delete cache if exists
        $this->deleteStaticCache($comment['node']['id']);
        
	    return array('success' => true, 'result' => $comment);
	}
	
	public function beforeUpdateComment($where, $data, $row)
	{
		if(has_role('admin') || has_role('root')){
			//$datais_spam =1;
			$data['$set']['is_spam'] =1;
		}
		else {
			//couter.spam++;
			$data['$inc'] = array('counter.spam' => 1);
		}
		return array('success' => true, 'result' => $data);
	}
	
	
	public function afterUpdateComment($where, $data, $row)
	{
	    //Delete cache if exists
		$this->deleteStaticCache($row['node']['id']);

		if(isset($data['$set']['status']) && $data['$set']['status'] =='approved'){
			$where= array('id'=> $row['u']['id']);
			$update= array('$inc' => array(
			 //TODO:update inf for user
			 'counter.c'=>1,
			 'counter.k'=>calculate_karma(Zend_Registry::get('user'),'comment_voted_up', $row['u']),
				));
			Dao_User::getInstance()->update($where,$update);
		}
		
		if(isset($data['$set']['is_spam']) && $data['$set']['is_spam'] == 1 
		    && $data['$set']['_cl_step']=='is_spam'){
			$where= array('id'=> $row['u']['id']);
			$update= array('$inc' => array(
			 //TODO:update inf for user
			 				'counter.c'=>-1,
			 				'counter.k'=>calculate_karma(Zend_Registry::get('user'),'del_comment', $row['u']),
							));
			Dao_User::getInstance()->update($where,$update);
		
		}
		//cache to node first
		$order= array('counter.tw'=>-1 , 'ts'=>-1);
            Dao_Node_Story::getInstance()->cacheCommentsToNode($row['id'], $order);
		
        //Update comment to story

        $where2 = array('id'=>$data['$set']['nid']);
        $cond['where'] = $where2;
        
        
        $story = $this->find($cond);
        
        $comments = array();
        if($story['success']){
            if($story['result'][0]['comments']){
                foreach ($story['result'][0]['comments'] as $comment){
                    if($comment['id'] == $data['$set']['id']){
                        $comment['content'] = $data['$set']['content'];
                    }
                    
                    $comments[] = $comment;
                }
            }
            $update = array('$set'=>array('comments'=>$comments));
            $this->update($where2,$update);
        }
        
		return array('success' => true, 'result' => $data);
	}
	
	/**
	 * Delete a single node by its Id
	 * @param MongoID $nodeId
	 */
	public function afterDeleteNode($row)
	{
		//Delete cache if exists
		$this->deleteStaticCache($row);

		//delete all comments
	    $commentDao = Dao_Comment_Story::getInstance();
	    $where = array('node.id' => $row['id']);
	    $commentDao->delete($where);
	    
	    // update karma of user
	    $where = array('id' => $row['u']['id']);
		$update = array('$inc' => array('counter.p' => -1,
										'counter.k' => calculate_karma(Zend_Registry::get('user'), 'del_story', $row['u']),
										));
		Dao_User::getInstance()->update($where, $update);
		
		/**
    	 * Haduc update next-iid and prev-idd of story after delete 12.8.2013
    	 * After upload story we excute:
    	 * 	- Set prev-iid of element is iid of element has max ts 
    	 * 	- Set next-idd of element is iid of element has min ts
    	 */
    	
	    $this-> updateNextandPrevIidAfterDelete($row);
	    
	    /**
	     * TODO: removed story => decrease counter.s of tag in tags this story in table story
	     * 		 				  decrease counter.s of tag in table tags
	     */
	    $tags = $row['tags'];
	    //Decrease all counter.s of tag in table story

	    if(isset($tags) && count($tags) > 0){
    	    foreach ($tags as $tag){
    	        //decrease counter.s of tag in table tags
    	        $where3 = array('id' => $tag['id']);
        		$update = array('$inc' => array('counter.s' => -1
											));
				Dao_Node_Tag::getInstance()->update($where3, $update);
				
				$cond['where'] = $where3;
				$tag = Dao_Node_Tag::getInstance()->find($cond);
				
    	        Dao_Node_Tag::getInstance()->updateCounterTagAllStory($tag['result'][0]); 
    	    }   
	    }
	    
	    return array('success' => true, 'result' => $row);
	}
	
	// set karma when delete comment
	public function  afterDeleteCommentById($Id, $row){
		//Delete cache if exists
		$this->deleteStaticCache($row['node']['id']);
		
		if(!isset($row['is_spam']) || $row['is_spam']!=1){
			//TODO: update karma here
			$where= array('id'=> $row['u']['id']);
			$update= array('$inc' => array(
			 //TODO:update inf for user
			 				'counter.c'=>-1,
			 				'counter.k'=>calculate_karma(Zend_Registry::get('user'),'del_comment', $row['u']),
							));
			Dao_User::getInstance()->update($where,$update);
		}
		//upate story comment couter and update point story after comment
		$actor = $row['u'];
		$action = 'del_comment';
		$node = $row['node'];
		$pointDelta = parent::pointDelta($actor, $action, $node);
        
		$where = array('id'=> $row['node']['id']);
		$update= array('$inc'=> array('counter.c'=>-1,'counter.tc'=>-1,'counter.point'=>$pointDelta));
		Dao_Node_Story::getInstance()->update($where,$update);
		//cache comment to Node
		$order= array('counter.tw'=>-1 , 'ts'=>-1);
            Dao_Node_Story::getInstance()->cacheCommentsToNode($row['node']['id'], $order);
            
        $cond['where'] = $where;
        $story = $this->findOne($where);
        
        $node = $story['result'];
        $this->incActivityCountAndCheckHotnessCache(array('ts' => time()),$node['iid'],$node['counter']['hn'],$node['type']);
		return array('success'=> true, 'result' =>$row);
		
	}
	
	/**
	 * Prepare data for new node insert
	 * @param Array $dataArray
	 */
	public function prepareFormDataForDaoInsert($dataArray = array(), $formName = "Story_Form_New")
	{
		return $dataArray;
	}	
	
	public function prepareCommentFormDataForDao($dataArray = array())
	{
		return $dataArray;
	}	

	/******************************RELATION*********************************/
	
	public function afterInsertRelation($data, $newRelations, $currentRow)
    {
    	//Delete cache if exists
    	parent::afterInsertRelation($data, $newRelations, $currentRow);
    	
	    /**
    	 *  Check status of story:
    	 *  	if status is approved
    	 *  		- remove cache home page hot
    	 *  		- remove cache detail page hot
    	 */
    	if($data['o']['status'] == 'approved'){
        	//Remove cache home page hot fun.local/hot 
        	$this->incActivityCountAndCheckHotnessCache(array('ts' => time()),$data['o']['iid'],$data['o']['counter']['hn'],0);
        	
            // Remove cache detail page hot fun.local/hot/anh-vui , fun.local/hot/clip-hai ,......................
           $this->incActivityCountAndCheckHotnessCache(array('ts' => time()),$data['o']['iid'],$data['o']['counter']['hn'],$data['o']['type']);    
    	}
    	
    	/**
    	 * Executed delete static cache vote|new
    	 */
    	
    	//TODO: GET id of story
    	$this->deleteNewOrVoteCache($data['o']['iid'],0);
    	$this->deleteNewOrVoteCache($data['o']['iid'],$data['o']['type']);
    	
    	$this->deleteStaticCache($currentRow['o']['id']);
    	//delete cache api
    	$this->deleteStaticCacheOfTypeApi('all', $type);
        return array('success' => true, 'result' => $data);
    }

	
	public function afterDeleteRelation($currentRow, $rt, $newRelations = array())
	{
        /*
        // decrease story.counter.vote
        $where = array('id' => $currentRow['o']['id']);
        $dataUpdate = array('$inc' => array('counter.vote' => -1));
        $this->update($where, $dataUpdate);
        */
	    
		
		$this->incActivityCountAndCheckHotnessCache(array('ts' => time()));
	    $this->deleteStaticCache($currentRow['o']['id']);
	    return array('success' => true);
	}
	
	
	public function filterNewObjectForAjax($obj, $formData)
	{
		return array('id' => $obj['id'] , 'slug' => $obj['slug'] , 'type' => $obj['type'], 'iid' => $obj['iid']);
	}
	
	
	public function filterUpdatedObjectForAjax($currentRow, $step, $data, $returnedData)
	{
		$keys = array(
				'counter', 'iid' , 'id' 
				);
		foreach ($keys as $key)
		$ret[$key] = $currentRow[$key];
		return $ret;
	}
    

    public function uploadAndResizeImage($img, $remote = false, $server = 'local')
    {
            $absoluteFile = PUBLIC_FILES_UPLOAD_PATH .'/'. $img['id'] . '.' . $img['ext'];
            // add bg resize
             $options = array(
                     'resize' => true,
                     'absolute_file' => $absoluteFile,
                     'file_id' => $img['id'],
                     'type' => 'image',
                     'local_destination_folder' => PUBLIC_FILES_UPLOAD_PATH . '/',
                     'remote' => $remote,
                     'extension' => $img['ext'],
                     'server' => $server
             );
             //TODO:
             Cl_File::getInstance($options)->upload();
             //Cl_File::getInstance($options)->runBackground('upload', array());
        return true;
    }
	/*************************** Calculate Hotness score*****************************************************/    
	//TODO: Use doBatchJobs();
	public function calculateHotness($data,$type)
	{
		$time = $data['ts'];
	    if($type > 0){
	        $where = array('status' => 'approved','type'=>$type);
		}else{
		    $where = array('status' => 'approved');    
		}
		$cond['where'] = $where;
		
		$r= $this->findAll($cond);
		
		foreach ($r['result'] as $k => $row)
		{
			$row['counter']['hn']= $row['counter']['point'] / pow((($time-$row['ts'])/3600 + 2),1.5);
			$r['result'][$k]['mau']=pow((($time-$row['ts'])/3600 + 2),1.5);
			$this->update(array('id'=>$row['id']), $row);
		}
		return array('success'=>true,'result'=>$this->findAll($cond));
	}
	  
	/**
	 * Calculate Hotness
	 *
	 * @param unknown_type $data
	 * @param unknown_type $currentHotIids
	 * @param unknown_type $iid
	 * @param unknown_type $type // 1 => story, 2 => image, 3 => video, 4 => flash-game, 5 => quiz
	 * @param unknown_type $typeRedisHotIids //hotness:top_iids_story , hotness:top_iids_image, hotness:top_iids_video...
	 */
	public function calculate_hotness($data,$currentHotIids,$iid,$type,$typeRedisHotIids){
		$time = $data['ts'];
		$ids = array();
		
	    foreach ($currentHotIids as $i => $v)
			{
				$ids[]= (int) $i;
			}
	
		//Add iid into array to calculate hotness
		$ids[] = $iid;
			
		$where = array('iid' => array('$in' => $ids));
		$cond['where'] = $where;
		$r= $this->findAll($cond);
		
		foreach ($r['result'] as $k => $row)
		{
			$row['counter']['hn']= $row['counter']['point'] / pow((($time-$row['ts'])/3600 + 2),1.5);
			$r['result'][$k]['mau']=pow((($time-$row['ts'])/3600 + 2),1.5);
			$this->update(array('id'=>$row['id']), $row);
		}
		
		$redis = init_redis(RDB_CACHE_DB);
	    if($type > 0){
	        $where = array('status' => 'approved','type'=>$type);
		}else{
		    $where = array('status' => 'approved');    
		}
	   
		$order = array('counter.hn' => -1);
		$limit = 100;
		
		$cond['where'] = $where;
		$cond['order'] = $order;
		$cond['limit'] = $limit;
		$r = $this->find($cond);

		if ($r['success'])
		{
			foreach ($r['result'] as $story)
			{
				$redis->zAdd($typeRedisHotIids, $story['counter']['hn'], $story['iid']);
			}
		}
		return array('success'=>true,'result'=>$this->find($where));
	}
	
	
	public function hasTag($tagName,$node){
		if(count($node['tags'])>0)
			foreach($node['tags'] as $tag){
					if($tag['name']==$tagName){
						return true;
					}
			}
		return false;
	}

	public function isFeatured($node){
		return $this->hasTag(featured_tag(),$node);
	}
	
	public function getArrayHotnessTop(){
		$where = array('status' => 'approved');
		$order = array('counter.hn' => -1);
		$limit = 100;
		
		$cond['where'] = $where;
		$cond['order'] = $order;
		$cond['limit'] = $limit;
		$r = $this->find($cond);
		
		$hotNess = array();
		if ($r['success'])
		{
			foreach ($r['result'] as $story)
			{
				$hotNess[] = $story['iid'];
			}
		}
		
		return $hotNess;
	}
	/******************************** Cache homepage Improve 11.8**************************/
	
	/**
	 * 
	 * @param Int $type story type
	 */
	function redisHotIidKey($type)
	{
	    if(!$type || $type == 0)
	        return 'hotness:top_iids';
	    return 'hotness:top_iids_' . story_type($type);
	}
	
	/**
	 * @param unknown_type $status == 'vote' | 'new'
	 * @param unknown_type $type : truyen-vui|anh-vui|video-clip
	 */
    function redisNewOrVoteIidKey($status,$type)
	{
	   if($status == 'queued')
	       $dir = 'vote';
	   else
	       $dir = 'new';
	   if(!$type || $type == 0)
	        return $dir . 'cache:top_iids'; //eg: newcache:top_iids
	    return $dir . 'cache:top_iids_' . story_type($type);//eg: newcache:top_iids_video_clip
	}
	
	/**
	 * 
	 * Calculate hotness
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $iid  // iid of story relation
	 * @param unknown_type $hotNess
	 * @param unknown_type $type // 1 => story, 2 => image, 3 => video, 4 => flash-game, 5 => quiz
	 */
	public function incActivityCountAndCheckHotnessCache($data,$iid,$hotNess,$type)
	{    
		/** 
		 * 			Haduc update 11.8.2013
		 * Check exit array redis with key values: hotness:top_iids
		 *  If array redis with key values: hotness:top_iids already exits
		 *  	- Add iids into array redis with key values: hotness:top_iids
		 *  	- Calculate hotness of member in array redis with key values: hotness:top_iids
		 *  
		 *  Else if array redis with key values: hotness:top_iids not exits
		 *  	- Refresh all hotness cache
		 *  	- Calculate all hotness and add 100 member hotness with order -1 into array redis
		 *  
		 */
	    $typeRedisHotIids = $this->redisHotIidKey($type); //hotness:top_iid_story, hotness:top_iids_image, etc..
	    $dir = 'hot/' . type_slug($type); //hot/truyen-cuoi, hot/anh-vui....
	  
		$redis = init_redis(RDB_CACHE_DB);

		//array of top 100 hot iids
		$currentHotIids = $redis->zRange($typeRedisHotIids, 0, -1, true);
		if (count($currentHotIids) == 0)
		{ 
			//generate top 100 hotness Iids
			//recalculate all hotness
			$this->refreshHotnessAll(array('ts' => time()),$type,$typeRedisHotIids);
			$currentHotIids = $redis->zRange($typeRedisHotIids, 0, -1, true);
		}
		
		/**
		 *  Khi lĂ¡ÂºÂ¥y ra $currentHotIids bĂ¡Â»Å¸i hĂƒÂ m zRange thĂƒÂ¬ kĂ¡ÂºÂ¿t quĂ¡ÂºÂ£ Ă„â€˜ĂƒÂ£ bĂ¡Â»â€¹ Ă„â€˜Ă¡ÂºÂ£o ngĂ†Â°Ă¡Â»Â£, chĂƒÂ­nh vĂƒÂ¬ thĂ¡ÂºÂ¿ chĂƒÂºng
		 *  ta phĂ¡ÂºÂ£i Ă„â€˜Ă¡ÂºÂ£o lĂ¡ÂºÂ¡i mĂ¡ÂºÂ£ng.
		 */
		$currentHotIids = array_reverse($currentHotIids,true);
		//Get list hotness old before relation 
		foreach ($currentHotIids as $i => $v)
			{
				$hotnessOld[]= (int) $i;
			}
			
		$redis->zAdd($typeRedisHotIids, $hotNess, (int) $iid);	
		
		$currentHotIids = $redis->zRange($typeRedisHotIids, 0, -1, true);
		$currentHotIids = array_reverse($currentHotIids,true);
		
		//calculate hotness 100 story in array redis 
		$this->calculate_hotness($data,$currentHotIids,$iid,$type,$typeRedisHotIids);
		$currentHotIids = $redis->zRange($typeRedisHotIids, 0, -1, true);
		/**
		 *  Khi lĂ¡ÂºÂ¥y ra $currentHotIids bĂ¡Â»Å¸i hĂƒÂ m zRange thĂƒÂ¬ kĂ¡ÂºÂ¿t quĂ¡ÂºÂ£ Ă„â€˜ĂƒÂ£ bĂ¡Â»â€¹ Ă„â€˜Ă¡ÂºÂ£o ngĂ†Â°Ă¡Â»Â£, chĂƒÂ­nh vĂƒÂ¬ thĂ¡ÂºÂ¿ chĂƒÂºng
		 *  ta phĂ¡ÂºÂ£i Ă„â€˜Ă¡ÂºÂ£o lĂ¡ÂºÂ¡i mĂ¡ÂºÂ£ng.
		 */
		$currentHotIids = array_reverse($currentHotIids,true);
		//Get list hotness new after relation
		foreach ($currentHotIids as $i => $v)
			{
				$hotnessNew[]= (int) $i;
			}
		
		/**
		 * array_diff_assoc Ă¢â‚¬â€� Berechnet den Unterschied zwischen Arrays mit zusĂƒÂ¤tzlicher IndexprĂƒÂ¼fung
		 * http://php.net/manual/de/function.array-diff-assoc.php
		 */
	    $result = array_diff_assoc($hotnessOld, $hotnessNew);
		
		if($result){
            // Get index fisrt different
		    $k = 0;
    		foreach($result as $key => $value){
    			if($value){
    				$k = $key;
    				break;
    			}
    		}

    		$pageChange = (int) ($k/10);
	
    		// Removed cache hot from page $pageChange to page 10
		    $this->deleteStaticCacheOfType($dir, $pageChange+1,10);
		    
		    // Removed cahce widget story hot if pagechange + 1 = 1
		    if($pageChange == 0){
		        $this->deleteStaticCacheOfType('widget/hot', $pageChange+1,2);
		    }
		
		}
		
		
		$kRelation = array_search($iid, $hotnessOld);
		$situation = (int) ($kRelation/10);
		
		// Removed cache hot of page have element relation
		$this->deleteStaticCacheOfType($dir, $situation+1, $situation+1);
	    // Removed cahce widget story hot if situation + 1 = 1
		    if($situation == 0){
		        $this->deleteStaticCacheOfType('widget/hot', $situation+1,2);
		    }
	}
	
	/**
	 * Recalculate all hotness score & cache top 200 into redis cache key "hotness:top_iids"
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $type // 1 => story, 2 => image, 3 => video, 4 => flash-game, 5 => quiz
	 * @param unknown_type $typeRedisHotIids //hotness:top_iids_story , hotness:top_iids_image, hotness:top_iids_video...
	 */
	public function refreshHotnessAll($data,$type,$typeRedisHotIids)
	{	
		// reset hotness:activites
		$redis = init_redis(RDB_CACHE_DB);
		$redis->set("hotness:activites", 0);
	
		$keys = $redis->keys($typeRedisHotIids);
		foreach($keys as $key)
			$this->deleteRedisCache($key);
	
		// reculate hotness
		//$this->runBackground("calculateHotness", array($data),$type);
		$this->calculateHotness($data,$type);
		
		//cache top 100 into redis cache key "hotness:top_iids"
		if($type > 0){
	        $where = array('status' => 'approved','type'=>$type);
		}else{
		    $where = array('status' => 'approved');    
		}
		
		$order = array('counter.hn' => -1);
		$limit = 100;
		
		$cond['where'] = $where;
		$cond['order'] = $order;
		$cond['limit'] = $limit;
		
		$r = $this->find($cond);
		if ($r['success'])
		{
			foreach ($r['result'] as $story)
			{
				$redis->zAdd($typeRedisHotIids, $story['counter']['hn'], $story['iid']);
			}
		}
		
		return array('success' => true);
	}
	
	
	public function refreshBest()
	{
		$redis = init_redis(RDB_CACHE_DB);
		$keys = $redis->keys('best*');
		foreach($keys as $key)
			$this->deleteRedisCache($key);
	}
	
	
	public function refreshUpcoming()
	{
		$redis = init_redis(RDB_CACHE_DB);
		$keys = $redis->keys('upcomming*');
		foreach($keys as $key)
			$this->deleteRedisCache($key);
	}
	
	
	public function setVoted($r)
	{
		if ($r['success'] && $r['count'] > 0)
		{
			$ids = array();
			foreach($r['result'] as $k => $row) {
				$ids[] = $row['id'];
			}
			$lu = Zend_Registry::get('user');
			$options = array('subject' => 'user', 'object' => 'story');
			$relationDao = Cl_Dao_Relation::getInstance($options);
			$where = array('s.id' => $lu['id'], 'o.id' => array('$in' => $ids));
			$sr = $relationDao->findAll(array('where' => $where));
			if($sr['success'] && $sr['count'] > 0) {
				foreach($r['result'] as $k => $row) {
					foreach ($sr['result'] as $key => $l) {
						//$r['result'][$k]['isVoteUp']=false;
						//$r['result'][$k]['isVoteDown']=false;
						if(isset($l['s']['id']) && $row['u']['id'] == $l['s']['id'] && 
								$row['id'] == $l['o']['id']) 
						{
							if($l['r'][0]['rt']==1) {
								$r['result'][$k]['isVoteUp']=true;
								v($r);
							}
							elseif($l['r'][0]['rt']==4) {
								$r['result'][$k]['isVoteDown']=true;
								v($r);
							}
							break;
						}
					}
				}
			}
			else 
			{
			    return array(
                    'success' => false,
                    'err' => "<div style='color :red'>BĂ¡ÂºÂ¡n Ă„â€˜ĂƒÂ£ vote bĂƒÂ i nĂƒÂ y rĂ¡Â»â€œi :D</div>"
                );
			}
		}
		return $r;
	}
	
	public function getStoryList($filter, $page = 1, $storyTypeInt = '', $tag = '', $uid = '', $limit = '')
	{
		$redis = init_redis(RDB_CACHE_DB);
		
		if ($filter == 'vote')
		{
			// find up comming story
			$where = array('status'=>'queued');
			//$order = array('counter.c' => -1 ,'ts' => -1);
			$order = array('ts' => -1);
		}
		elseif ($filter == 'picks')
		{				
			//find featured story
			$where = array('tags'=>array('$elemMatch'=>array('name'=> featured_tag())));
			$order = array('ts'=>-1);
		}
		elseif ($filter == 'hot')
		{
			//array of top 100 hot iids
			$key=$storyTypeInt;
			if(is_array($storyTypeInt) && is_rest())
			{
			
				foreach($storyTypeInt as $row)
				{
					$redisHotIidsKey = $this->redisHotIidKey((int)$row);
					$currentHotIids = $redis->zRange($redisHotIidsKey, 0, -1, true);
					if (count($currentHotIids) == 0)
					{
						//TODO: generate top 100 hotness Iids
						//recalculate all hotness
						$this->refreshHotnessAll(array('ts' => time()),0,$redisHotIidsKey);
						$currentHotIids = $redis->zRange($redisHotIidsKey, 0, -1, true);
					}
					foreach ($currentHotIids as $i => $v)
					{
						$arrHot[$i]= (int) $i;
					}
				}	
				
			}
			
			else{
				
				$redisHotIidsKey = $this->redisHotIidKey($key); 
				$currentHotIids = $redis->zRange($redisHotIidsKey, 0, -1, true);
				if (count($currentHotIids) == 0)
				{
					//TODO: generate top 100 hotness Iids
					//recalculate all hotness
					$this->refreshHotnessAll(array('ts' => time()),0,$redisHotIidsKey);
					$currentHotIids = $redis->zRange($redisHotIidsKey, 0, -1, true);
				}
				foreach ($currentHotIids as $i => $v)
				{
					$arrHot[$i]= (int) $i;
				}
			}
			$currentHotIids=$arrHot;
			
			$where = array(
					'status' => 'approved', 
					'iid' => array('$in' => $currentHotIids)
			);
			$order = array('counter.hn' => -1);	
		}
		elseif ($filter == 'best') //order by point
		{
			$where = array('status' => 'approved');
			$order = array('counter.point' => -1);
		}
		elseif ($filter == 'new') //order by point
		{
			$where = array('status' => 'approved');
			$order = array('ats' => -1);
		}
		
		if ($storyTypeInt == '')
			$cond['where'] = $where;
		else if($storyTypeInt !="" && !is_array($storyTypeInt))
		{
			$typeWhere = array('type' => $storyTypeInt);
			$cond['where'] = array('$and' => array($where, $typeWhere));
		}
		else {
			foreach ($storyTypeInt as $i => $v)
			{
				$arr[$i]= (int) $v;
			}
			$typeWhere = array('type' => array('$in'=>$arr));
			$cond['where'] = array('$and' => array($where, $typeWhere));
			
		}

		if ($tag != '')
		{
			$tagWhere = array('tags.slug' => $tag);
			$cond['where'] = array('$and' => array($where, $tagWhere));
		}
		
		if ($uid != '')
		{
			$uWhere = array('u.iid' => $uid);
			$cond['where'] = $uWhere;
		}
		if($limit != '')
		    $cond['limit'] = $limit;
		else
            $cond['limit'] = per_page();
		$cond['order'] = $order;
		$cond['page'] = $page;
		$cond['total'] = 1; //do count total
		
		//cache if $page is < 10;
		/*
		v($cond);
		if ($page < 10)
		{
			$r = $this->getRedisCache("find", "$filter:$page", 600, array($cond)); //300 seconds?
		}	
		else 
			$r = $this->find($cond);
		*/
		$r = $this->find($cond);
		return $r;
	}
	
	public function updateViewCounter($limit = '')
	{
	    /*
	     * @PARAM: $limit : if not set -> default =100;
	     *         set $limit = -1 for update all viewCounter of all Stories 
	     * */
	    //set limit for 
	    $filters = array('hot','new','vote','picks','best');
	    $redis = init_redis(RDB_CACHE_DB);
	    if($limit == '')
	       $limit = 100;
	    foreach ($filters as $filter)
	    {
	        $r = $this->getStoryList($filter, $page = 1,'','','',$limit);
	        if($r['success'])
	            $lists = $r['result'];
	        foreach($lists as $row)
	        {
	            // v($row);
	            $key = $redis->get('views_'.$row['iid']);
	            $where = array('iid' => $row['iid']);
	            $view = $row['counter']['v']+$key;
	            $dataUpdate = array('$set' => array('counter.v' => $view));
	            $r1 = Dao_Node_Story::getInstance()->update($where ,$dataUpdate);
	            if($r1['success'])
	                $redis->set('views_'.$row['iid'],0);
        	    if($limit == -1)
        	        $this->deleteStaticCache($row);
        	    else
        	        $this->deleteStaticCacheOfType($filter,0,$limit-1);
	        }
	    }
	    return array('success' => true);
	}
	
	//inc view counter of story
	function incViewCounter($id){
		$where = array('id' => $id);
		$update = array('$inc' => array('counter.v' => 1));
		Dao_Node_Story::getInstance()->update($where, $update);
		$result = Dao_Node_Story::getInstance()->find(array('where'=>$where));
		if(isset($result['result'][0]['counter']['v'])){
			return $result['result'][0]['counter']['v'];
		}else{
			return 0;
		}
	}
	
	/**
	 * Function update next and prev (iid , slug) of element 
	 */
	public function updateNextandPrevIidAfterUpload($data,$row){
		$cond['limit'] = 1;
		
		//Get iid of next element
		$order = array('ts'=>1);
		$cond['order'] = $order;
		$cond['where'] = array('iid' => array('$gt' => $data['iid']), 'type' => $data['type']);
		
		//$r is result of element has max ts
		$r = $this->find($cond);
		
		if(!(isset($r['result']) && (count($r['result']) > 0)))
		{
			
			/**
			 * Truong hop upload moi se khong co phan tu lon hon
			 * Vi the chung ta se lay phan tu thu nhat (iid dau tien) thoa man dieu kien
			 */
			$cond['where'] = array('iid' => array('$ne' => $data['iid']), 'type' => $data['type']);
			$r = $this->find($cond);
		}
		
		if (isset($r['result']) && (count($r['result']) > 0)){
			//Update next-iid of data
			$update = array('$set' => array('next.iid' => $r['result'][0]['iid'],
											'next.slug' => $r['result'][0]['slug'],
											'next.type' => $r['result'][0]['type'],
											));
	        $where = array('id' => $row['id']);
	        $this->update($where, $update);
	        	
			//Update prev-iid of next-element
			$update = array('$set' => array('prev.iid' => $data['iid'],
											'prev.slug' => $data['slug'],
											'prev.type' => $data['type']
											));
	        $where = array('id' => $r['result'][0]['id']);
	        $this->update($where, $update);	
		}
		
		//Get iid of prev element
		$order = array('ts'=>-1);
		$cond['order'] = $order;
		$cond['where'] = array('iid' => array('$lt' => $data['iid']), 'type' => $data['type']);
		
		//$r is result of element has min ts
		$r = $this->find($cond);
		
		if(!(isset($r['result']) && (count($r['result']) > 0)))
		{
			/**
			 * Truong hop la phan tu dau tien se khong co phan tu nho hon
			 * Vi the chung ta se lay phan tu lon nhat (iid cuoi cung) thoa man dieu kien
			 */
			$cond['where'] = array('iid' => array('$ne' => $data['iid']), 'type' => $data['type']);
			$r = $this->find($cond);
		}
		
		//Update prev-iid of data
		if(isset($r['result']) && (count($r['result']) > 0)){
			$update = array('$set' => array('prev.iid' => $r['result'][0]['iid'],
											'prev.slug' => $r['result'][0]['slug'],
											'prev.type' => $r['result'][0]['type']
											));
	        $where = array('id' => $row['id']);
	        $this->update($where, $update);
	        	
			//Update next-iid of next-element
			
			$update = array('$set' => array('next.iid' => $data['iid'],
											'next.slug' => $data['slug'],
											'next.type' => $data['type']
											));
	        $where = array('id' => $r['result'][0]['id']);
	        $this->update($where, $update);	
		}
			
	}
	
	//Chua test thu viec xoa mot node thi no co cap nhat lai khong
	public function updateNextandPrevIidAfterDelete($row){
		$cond['limit'] = 1;
		
		//Find element after row
		//Get iid of next element
		$order = array('ts'=>1);
		$cond['order'] = $order;
		$cond['where'] = array('iid' => array('$gt' => $row['iid']), 'type' => $row['type']);
		
		//$r is result of element has max ts
		$nt = $this->find($cond);
		
		//Find element before row
		///Get iid of prev element
		$order = array('ts'=>-1);
		$cond['order'] = $order;
		$cond['where'] = array('iid' => array('$lt' => $row['iid']), 'type' => $row['type']);
		
		//$r is result of element has max ts
		$pr = $this->find($cond);
		
		if(isset($pr['result']) && (count($pr['result']) > 0)){
			//Update prev-iid, prev-slug for next element
			$update = array('$set' => array('prev.iid' => $pr['result'][0]['iid'],
											'prev.slug' => $pr['result'][0]['slug'],
											'prev.type' => $pr['result'][0]['type']
											));
	        $where = array('id' => $nt['result'][0]['id']);
	        $this->update($where, $update);
		}
        
		if(isset($nt['result']) && (count($nt['result']) > 0)){
	        //Update next-iid, next-slug for pre element
	        $update = array('$set' => array('next.iid' => $nt['result'][0]['iid'],
											'next.slug' => $nt['result'][0]['slug'],
	        								'next.type' => $nt['result'][0]['type']
											));
	        $where = array('id' => $pr['result'][0]['id']);
	        $this->update($where, $update);
		}
        
	}
	
	/**
	 * Excute:
	 * 		+ loop all user with key iid
	 * 			- Compare karma user in table user with karma user has max ts in table snapshot 
	 * 				+ if change, update karma in table user into table snapshot
	 * 				+ nochange, no activity
	 * 			- Excute re base
	 * 				- Compare karma user has min ts in table snapshot with karma user in table baseKarma
	 * 					+ if karma user has min ts in table snapshot larger than karma user in table baseKarma
	 * 							then update karma into baseKarma
	 * 			- Delete record has min ts of user in table snapshot if time stamp over 30 day ago
	 */
	public function takeSnapshot($timeStamp){
	    // Get list user
	    $u = Dao_User::getInstance()->findAll(array('return_cursor' => true));
	    
	     // Get time stamp want to rebase karma user
	    $order = array('ts' => -1);
		$cond['order'] = $order;
		$cond['limit'] = 1;
		
		//Loop all user
		if($u['success']){
		    foreach ($u['result'] as $user){
		        $doInsert = false;
		        $data['uiid'] = $user['iid'];
		        $data['karma'] = $user['counter']['k'];
		        $data['ts'] = $timeStamp;
		        
		        $where = array('uiid' => $user['iid']);
    		    $cond['where'] = $where;
    		    
    		    //Get row has uiid = $user['iid']) and max ts
    	        $r = Dao_Node_Snapshot::getInstance()->find($cond);
    	        
    	        //$doInsert is true in 2 cases:
    	        // 1. khong co entry nao trong snapshot , base karmar
    	        // 2. khong co entry nao trong snapshot & base karma != $user['karma']
    	        // 3. $user['karma'] khac voi latest karma trong snapshot
    	        
    	        if(!$r['success'] || $r['count'] == 0){
    	            //sang baseKarma lay karma cua user & insert vao snapshot
    	            $r = Dao_Node_BaseKarma::getInstance()->find($cond);
    	            if ($r['count'] == 0 || //new user. case #1
    	                ($r['result'][0]['karma'] != $data['karma']) // case #2
    	            )
    	                $doInsert = true;
    	        }
    	        else if($r['result'][0]['karma'] != $user['counter']['k']){ // case #3
        	            $doInsert = true;
    	        }
                if ($doInsert)
    	            Dao_Node_Snapshot::getInstance()->insert($data);
		    }
		}
		$this->rebaseKarma();
		$this->deleteOldSnapshot($timeStamp - 30 * 86400);
	}
	
	public function deleteOldSnapshot($ts)
	{
	    $where = array('ts' => array('$lte' => $ts));
        Dao_Node_Snapshot::getInstance()->delete($where);
	}
	
	/**
	 * - ReScore baseKarma
	 * - get row in table snapshot has min time stamp(ts)
	 * 		+ if it change, update karma into base with uiid
	 * 
	 * @param int $k is day want to rebase karma user
	 */
	public function rebaseKarma(){
	    // Get list user
	    $u = Dao_User::getInstance()->findAll(array('return_cursor' => true));
		
		$order = array('ts' => 1);
		$cond['order'] = $order;
		$cond['limit'] = 1;
		
		if($u['success']){
    		foreach ($u['result'] as $user){
    		    $where = array('uiid' => $user['iid']);
    		    $cond['where'] = $where;

    	        $r = Dao_Node_Snapshot::getInstance()->find($cond);	
    	        if($r['success'] && $r['count'] > 0){
    	            $karma = $r['result'][0]['karma'];
    	            
    	            // now update to base karma
    	            
    	            $where = array('uiid' => $user['iid']);
    	            $data = array(
    	                'uiid' => $user['iid'],
    	                'karma' => $karma,
    	                'ts' => $r['result'][0]['ts']        
    	            );
    	            //upsert
    	            Dao_Node_BaseKarma::getInstance()->update($where, $data, array('upsert' => true));
    	        }    
    		}    
		}
	}
	
	public function topScoreKarma(){
	    $this->topScoreKarmaForDay(7);
	    $this->topScoreKarmaForDay(30);
	    //Removed cache topuser
	    $this->deleteStaticCacheOfType('site/topuser',1,2);
	}
	
	/**
	 * - Input: day you want to score topuser
     *  - loop all user in table user
     *  	- get value karma of user with iid at now
     *  	- get value karma of user with iid n day ago
     *  		+ If value karma null, get value in table base karma
     *  	- Score change karma of user in n day 
     *  	- Add change karma into table topKarmaUserNDay
     *  	- Sort table desc
     *  	- Save table in redis:
     *  		- top_user:7
     *  		- top_user:30
     *     
	 *	@param $k is day want to score top karma user
	 */
	public function topScoreKarmaForDay($k){
	    $u = Dao_User::getInstance()->findAll(array('return_cursor' => true));
	    if($k){
	        $timeStamp = time() - $k * 86400;
	        $topUserQueueRedis = 'top_queue_user:' . $k;   
	        $topUserRedis = 'top_user:' . $k;   
	    }else{
	        $timeStamp = time() - 7*86400;
	        $topUserQueueRedis = 'top_queue_user:7';   
	        $topUserRedis = 'top_user:7';
	    }
	        
	    $topScoreKarma = array();
	    
	    $order = array('karma' => -1);
		$cond['order'] = $order;
		$cond['limit'] = 1;
		
	    if($u['success']){
	        foreach ($u['result'] as $user){
	            $where = array('uiid' => $user['iid'],'ts' => array('$lt' => $timeStamp));
	            $cond['where'] = $where;
	            $r = Dao_Node_Snapshot::getInstance()->find($cond);

	            if(!$r['result']){
	                $where = array('uiid' => $user['iid']);
	                $r = Dao_Node_BaseKarma::getInstance()->find($where);
	            }
	            
	            $topScore['k'] = ($user['counter']['k']-$r['result'][0]['karma']);
	            $topScore['uiid'] = $user['iid'];
	            $topScoreKarma[] = $topScore;
	        }
	    }
	    
       sort($topScoreKarma);
       
       $redis = init_redis(RDB_CACHE_DB);

       if($topScoreKarma){
           foreach ($topScoreKarma as $row){
               $redis->zAdd($topUserQueueRedis, $row['k'], $row['uiid']);  
           }    
       }
       
       $redis->rename($topUserQueueRedis, $topUserRedis);
       $topUserQueue = $redis->zRange($topUserQueueRedis, 0, -1, true);
       $topUserQueue = array_reverse($topUserQueue,true);
	}
	
	public function resizeImage($img){
	    $folderImage = array('50','170','370','500','1000','thumb');
	    $sizeImage = array('50','170','370','500','1000','560');
	    $sizeImageWM = array('20','40','120','160','180','180');
	    
	    for ($i=0;$i<count($sizeImage);$i++){
	        if(!is_dir(PUBLIC_FILES_UPLOAD_PATH. '/' . $folderImage[$i]))
			mkdir(PUBLIC_FILES_UPLOAD_PATH. '/' . $folderImage[$i], 0777, true);
			
	        $filename = PUBLIC_FILES_UPLOAD_PATH .'/'. $img['id'] . '.' . $img['ext'];
	        $filename_thumb = $img['id'] . '.' . $img['ext'];
            $outputFile = PUBLIC_FILES_UPLOAD_PATH. '/'. $folderImage[$i] . '/' . $img['id'] . '.' . $img['ext'];
            
            $outputFile_thumb = PUBLIC_FILES_UPLOAD_PATH. '/'. $folderImage[$i] ;
            
            $image = new Bedeabza_Image($filename);
            if ($sizeImage[$i]=='560')
            {
            	$this->resizeAndCropThumbnail($filename,560,292,$outputFile);
            }
            else 
            {
            	$image->resize($sizeImage[$i],NULL, Bedeabza_Image::RESIZE_TYPE_CROP);   
            	
            	if(getenv('SITE')){
            	    $wm = WM_DIR. '/' . getenv('SITE'). '/medium.png';
            	}else{
            		$wm = WM_DIR . '/fun/medium.png';
            	}
            	f($wm);
            	if (file_exists($wm))
        		    $image->watermark($wm, Bedeabza_Image::WM_POS_BOTTOM_RIGHT,$sizeImageWM[$i],NULL);
            	
                $image->save($outputFile, 100);
            }

	    }
	   
	}
	
	
	
	/**
	 * Resize all images of a story
	 * @param Story $row
	 * @param PostData $data
	 */
	
	public function resizeAndCropThumbnail($filename,$thumb_width,$thumb_height,$outputFile_thumb){
	    $type = exif_imagetype($filename);
	    if($type == IMAGETYPE_JPEG){
	        $format = 'image/jpeg';
			$image = imagecreatefromjpeg($filename);
	    }else if ($type == IMAGETYPE_PNG){
	        $format = 'image/png';
			$image = imagecreatefrompng($filename);
	    }else if ($type == IMAGETYPE_GIF){
	        $format = 'image/gif';
			$image = imagecreatefromgif($filename);
	    }
	    
        $width = imagesx($image);
        $height = imagesy($image);
        
        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;
        
        if ( $original_aspect >= $thumb_aspect )
        {
           // If image is wider than thumbnail (in aspect ratio sense)
           $new_height = $thumb_height;
           $new_width = $width / ($height / $thumb_height);
        }
        else
        {
           // If the thumbnail is wider than the image
           $new_width = $thumb_width;
           $new_height = $height / ($width / $thumb_width);
        }
        
        $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
	    if(($type == IMAGETYPE_PNG) || ($type == IMAGETYPE_GIF)){
            imagealphablending($thumb, false);
            $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
            imagefilledrectangle($thumb, 0, 0, $thumb_width, $thumb_height, $transparent);
        }
        // Resize and crop
        imagecopyresampled($thumb,
                           $image,
                           0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                           0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                           0, 0,
                           $new_width, $new_height,
                           $width, $height);

       //Save image
    	switch($format)
			{
				case 'image/jpeg':
				    header( "Content-type: image/jpg" );
					imagejpeg($thumb, $outputFile_thumb, 100);
					break;
				case 'image/gif';
				    header( "Content-type: image/gif" );
				    imagegif($thumb, $outputFile_thumb, 100);
				    break;
				case 'image/png':
				    header( "Content-type: image/png" );
					imagepng($thumb, $outputFile_thumb, 100);
					break;
			}
			
	    if(($type == IMAGETYPE_PNG) || ($type == IMAGETYPE_GIF)){
            imagesavealpha($thumb,true);
        }
    }
	
	public function resizeStoryImages($row, $data = array()){
	    if(isset($row['images']) && count($row['images'])) {
        	foreach ($row['images'] as $img){
        	    if(in_array($img['ext'], array('jpg', 'gif', 'png', 'bmp', 'ico', 'tiff'))){
        	        if($img['url'] == ''){
        	            $img['url'] = $img['id'] . '.' . $img['ext'];
        	        }
                        $this->resizeImage($img);
        	    }
        	}
        	//TODO: update field watermark = 1
        	$where = array('id'=>$row['id']);
        	$update= array('$set'=> array('wm'=>1));
        	$this->update($where,$update);
        }else if(isset($data['url'])){
            $img['url'] = $data['url'];
            preg_match('#\.([^\.]+)$#',$img['url'],$matches);
    		$img['id'] =  str_replace($matches[0],'',$img['url']);
            $img['ext'] = str_replace('.','',$matches[0]);
            
            if(in_array($img['ext'], array('jpg', 'gif', 'png', 'bmp', 'ico', 'tiff'))){
                $this->resizeImage($img);
    			
    			//TODO: update field watermark = 1
    			$where = array('id'=>$row['id']);
		        $update= array('$set'=> array('wm'=>1));
		        $this->update($where,$update);
            }
        }
	}
	
	/**
	 * Executed delete cache story in new|vote
	 * 	If(status == approved)
	 * 		delete page(new) include story 
	 * If(status == queue)
	 * 		delete page(vote) include story
	 * 
	 * @param int $iid is iid of story
	 */
	public function deleteNewOrVoteCache($iid,$type){
	    /**
	     * ThĂ¡Â»Â±c hiĂ¡Â»â€¡n viĂ¡Â»â€¡c xĂƒÂ³a cache cho truyĂ¡Â»â€¡n trong mĂ¡Â»Â¥c vote hoĂ¡ÂºÂ·c new
	     * TrĂ†Â°Ă¡Â»ï¿½ng hĂ¡Â»Â£p xĂƒÂ³a: 
	     * 	  NĂ¡ÂºÂ¿u trĂ¡ÂºÂ¡ng thĂƒÂ¡i cĂ¡Â»Â§a truyĂ¡Â»â€¡n lĂƒÂ  queue thĂƒÂ¬ xĂƒÂ³a Ă¡Â»Å¸ phĂ¡ÂºÂ§n vote
	     * 	  NĂ¡ÂºÂ¿u trĂ¡ÂºÂ¡ng thĂƒÂ¡i cĂ¡Â»Â§a truyĂ¡Â»â€¡n lĂƒÂ  approved thĂƒÂ¬ xĂƒÂ³a Ă¡Â»Å¸ phĂ¡ÂºÂ§n new
	     * ThĂ¡Â»Â±c hiĂ¡Â»â€¡n:
	     * 	  LĂ¡ÂºÂ¥y ra danh sĂƒÂ¡ch cache new|vote trong redis
	     * 		NĂ¡ÂºÂ¿u chĂ†Â°a cĂƒÂ³: tĂ¡ÂºÂ¡o ra danh sĂƒÂ¡ch 200 phĂ¡ÂºÂ§n tĂ¡Â»Â­ new|vote
	     * 	    NĂ¡ÂºÂ¿u cĂƒÂ³ rĂ¡Â»â€œi:
	     * 			Xem phĂ¡ÂºÂ§n tĂ¡Â»Â­ hiĂ¡Â»â€¡n tĂ¡ÂºÂ¡i nĂ¡ÂºÂ±m Ă¡Â»Å¸ vĂ¡Â»â€¹ trĂƒÂ­ thĂ¡Â»Â© bao nhiĂƒÂªu trong danh sĂƒÂ¡ch
	     * 		    Eg: NĂ¡ÂºÂ¿u Ă¡Â»Å¸ vĂ¡Â»â€¹ trĂ¡Â»â€¹ thĂ¡Â»Â© 52 -> xĂƒÂ³a trang thĂ¡Â»Â© 6 
	     */
	    
	    //Get story
	    $where = array('iid'=>$iid);
        $r = $this->findOne($where);
        
        if($r['success']){
            $story = $r['result'];
            $redis = init_redis(RDB_CACHE_DB);
            //Get list depend status
            $nameRedisIids = $this->redisNewOrVoteIidKey($story['status'],$type);
            
            if($story['status'] == 'queued'){
                if($type != '' && $type != 0)            
                    $dir = 'vote/' . type_slug($type); //vote/truyen-cuoi, vote/anh-vui....
                else
                    $dir = 'vote/'; 
            }
            else if($story['status'] == 'approved'){
                if($type != '' && $type != 0)
                    $dir = 'new/' . type_slug($type); //new/truyen-cuoi, new/anh-vui....
                else 
                    $dir = 'new/';
            }
            //array of top 200 hot iids
            $currentRedisIids = $redis->zRange($nameRedisIids, 0, -1, true);
            if($currentRedisIids == null || count($currentRedisIids) == 0){
               $this->initNewOrVoteCacheList($story['status'],$type);
               //array of top 200 hot iids
               $currentRedisIids = $redis->zRange($nameRedisIids, 0, -1, true);
            }
            /**
    		 *  Khi lĂ¡ÂºÂ¥y ra $currentRedisIids bĂ¡Â»Å¸i hĂƒÂ m zRange thĂƒÂ¬ kĂ¡ÂºÂ¿t quĂ¡ÂºÂ£ Ă„â€˜ĂƒÂ£ bĂ¡Â»â€¹ Ă„â€˜Ă¡ÂºÂ£o ngĂ†Â°Ă¡Â»Â£c, 
    		 *  chĂƒÂ­nh vĂƒÂ¬ thĂ¡ÂºÂ¿ chĂƒÂºng ta phĂ¡ÂºÂ£i Ă„â€˜Ă¡ÂºÂ£o lĂ¡ÂºÂ¡i mĂ¡ÂºÂ£ng.
    		 */
    		$currentRedisIids = array_reverse($currentRedisIids,true);
		
    		$newCurrentRedisIids = array();
    	    foreach ($currentRedisIids as $i => $el){
    	        $newCurrentRedisIids[] =  $i; 
    	    }
    	    
    	    //Check situation of story in cache story list
    	    /**
    	     * http://us1.php.net/array_search
    	     * array_search Ă¢â‚¬â€� Searches the array for a given value and returns the corresponding key if successful
    	     */
    		$key = array_search($iid, $newCurrentRedisIids);
    		$situation = (int) ($key/10);
    	    //Delete story page
    	    $this->deleteStaticCacheOfType($dir, $situation+1, $situation+1);
        }
	}
	
	/**
	 * Khoi tao danh sach cache redis cho muc new hoac vote
	 * 
	 * @param story_type $type == 1 => anh-vui .....
	 */
	public function initNewOrVoteCacheList($status,$type){
	    $redis = init_redis(RDB_CACHE_DB);
        //Get list depend status
        $nameRedisIids = $this->redisNewOrVoteIidKey($status,$type);
            
	    if($type != 0 && $type != '')
            $where = array('status' => $status, 'type' => $type);
        else 
            $where = array('status' => $status);
	
        if($status == 'queued'){
    		$order = array('ts' => -1);        
        }elseif($status == 'approved'){
    		$order = array('ats' => -1);//approved timestamp
        }
		$limit = 200;
		
		$cond['where'] = $where;
		$cond['order'] = $order;
		$cond['limit'] = $limit;
		
		$r = $this->find($cond);
		if ($r['success'])
		{
		    $keys = $redis->keys($nameRedisIids);
		    foreach($keys as $key)
			    $this->deleteRedisCache($key);
			
			foreach ($r['result'] as $story)
			{
				$redis->zAdd($nameRedisIids, $story['ts'], $story['iid']);
			}
		}
		
		return array('success' => true);
	}
	public function fb_like_inc_point($iid,$rt)
	{
	    $where = array('iid' => $iid);
		$r = Dao_Node_Story::getInstance()->findOne($where);
		$lu = Zend_Registry::get('user');
		if ($r['success'] && $r['count'] > 0)
		{
    		$where1 = array('id' => $r['id']);
			//$actor, $action, $node, $subjectUser = array()
		    if ($rt == 1 )//vote up
		    {
		        parent::updateUserKarmaAndNodePoint($lu,'story_voted_up',$r['result'],$r['result']['u']);
		        //if voted up => increase 1 more vote
		        $storyUpdate = array('$inc' => array('counter.vote' => 1));
		        $r2 = $this->update($where, $storyUpdate);
		    }
		    else if ($rt == 4 )//vote down
		    {
	            parent::updateUserKarmaAndNodePoint($actor, 'story_voted_down', $node);
		        $storyUpdate = array('$inc' => array('counter.vd' => -1));
		        $r2 = $this->update($where, $storyUpdate);
		    }
		}	
	}
	
    function rmdir_recursive($dir) {
        foreach(scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) 
                continue;
            if (is_dir("$dir/$file")) 
                $this->rmdir_recursive("$dir/$file");
            else 
                unlink("$dir/$file");
        }
        rmdir($dir);
    }
    
    public function updateSizeImages(){
    	//get list images
    	$where = array('type' => 2);
    	$cond['where'] = $where;
    	
    	$r = $this->findAll();
    	if($r['success']){
    		$stories = $r['result'];
    		foreach ($stories as $s){
    			$newStory = $s;
    			$url = $s['url'];
    			$images = isset($s['images']) ? $s['images'] : array();
    			
    			list($width, $height, $type, $attr) = getimagesize(PUBLIC_FILES_UPLOAD_PATH .'/'. $s['url']);
    			
    			$newStory['h'] = (isset($height) && $height != null) ? $height : 0;
    			$newStory['w'] = (isset($width) && $width != null) ? $width : 0;
    			if(count($images) > 0){
    				$newImages = array();
    				foreach ($images as $img){
    					$newImg = $img;
    					list($width, $height, $type, $attr) = getimagesize(PUBLIC_FILES_UPLOAD_PATH .'/'. $img['url']);
    					 
    					$newImg['h'] = (isset($height) && $height != null) ? $height : 0;
    					$newImg['w'] = (isset($width) && $width != null) ? $width : 0;
    					
    					$newImages[] = $newImg;
    				}
    				
    				$newStory['images'] = $newImages;
    			}
    			 
    			
    			$update = array('$set' => array(
    					'h' => $newStory['h'],
    					'w' => $newStory['w'],
    					'images' => $newStory['images'],
	    			)
    			);
    			
    			$where = array('id'=>$s['id']);
    			
    			$this->update($where, $update);
    		}
    	}
    }
}
