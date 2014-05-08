<?php
class Dao_Node_Category extends Cl_Dao_Node
{
    public $nodeType = 'category';
    public $cSchema = array(
    		'id' => 'string',
            'iid' => 'int',
    		'name' => 'string',
    		'slug' => 'string',
    		'description' => 'string',
    		'avatar' => 'string',
    		//add other stuff u want
    );

    public $category_tree = array(
            //TODO cần có phân nhánh cây => 1 category có nhiều con, và có thể tạo tự động được category con             
    	
    );
    protected function relationConfigs($subject = 'user')
    {
    	if ($subject == 'user')
    	{
    		return array(
    				'1' => '1', //vote | like
    				'2' => '2', //follow
    				'3' => '3' , //flag as spam
    		);
    	}
    }
    
	protected function _configs(){
	    $user = Cl_Dao_Util::getUserDao()->cSchema;
	    $category = array(
    		'id' => 'string',
    		'iid' => 'int',
    		'name' => 'string',
    		'slug' => 'string',
    		'description' => 'string',
    		'avatar' => 'string',
	    );
	    
    	return array(
    		'collectionName' => 'category',
        	'documentSchemaArray' => array(
    	         'id' => 'string',
    	         'iid' => 'int',
    	         "category_id"	=>'string',
        	     "category_type" =>'int',
        		 'slug' => 'string',//eg: Điện lạnh => slug :: dienlanh
    	         "name" => 'string',
        		 'avatar' => 'string',
    	         "description" => 'string',
    	         "meta_description" => 'string',
			     "category_image" => 'string',
				 "parent_id" => 'string',
				 "top" => 'int',
				 "column" => 'int',
				 "sort_order" =>'int',
				 "status" => 'string',
				 "date_added" => 'float',
				 "date_modified" => 'float',
        		 "child_category" => array(
        		 	$category
        		 ),
        		 'child_cate_id' => 'array',//array child_cate_id
        		 'level' => 'int', //1, 2, 3
        	)
    	);
	}
	
    /**
     * Add new node
     * @param post data Array $data
     */
	public function beforeInsertNode($data)
	{
		$data['level'] = 1;
		
		if (!isset($data['iid']))
		{
			$redis = init_redis(RDB_CACHE_DB);
			$data['iid'] = $redis->incr($this->nodeType . ":iid"); //unique node id
		}
        return array('success' => true, 'result' => $data);
	}
	
	public function afterInsertNode($data, $row)
	{
		//update child_category
        return array('success'=> true, 'result' => $row);
	}
	
    /******************************UPDATE****************************/
    public function beforeUpdateNode($where, $data, $currentRow)
    {
    	if($data['$set']['_cl_step'] == 'is_level'){
    		$data['$set']['level'] = (int)$data['$set']['level'];
    	}
    	
    	if(isset($data['$set']['parent_category']) && $data['$set']['parent_category'] != ''){
    		$data['$set']['level'] = 2;
    		$data['$set']['parent_id'] = $data['$set']['parent_category'];
    	}
    	/*else{
    		$data['$set']['level'] = 1;
    	}*/
    	
        /*
         * You have $data['$set']['_cl_step'] and $data['$set']['_u'] available
         */
        return array('success' => true, 'result' => $data);
    }
    
	public function afterUpdateNode($where, $data, $currentRow)
    {
    	$currentRow['parent_id'] = isset($currentRow['parent_id']) ? $currentRow['parent_id'] : '';
    	if(isset($data['$set']['parent_category']) && $data['$set']['parent_category'] != '' 
    			&& $data['$set']['parent_category'] != $currentRow['parent_id']){
    		$id = $data['$set']['parent_category'];
    		$where = array('id' => $id);
    		$r = $this->findOne($where);
    			
    		if($r['success']){
    			$cate = $r['result'];
    			$child_category = (isset($cate['child_category']) && $cate['child_category']) ? $cate['child_category'] : array();
    	
    			$childCateNew = array();
    			$childCateIdNew = array();
    			$boolen = false;
    			$count = 0;
    			
    			if(count($child_category) > 0){
	    			foreach ($child_category as $ca){
	    				if($ca['id'] != $currentRow['id']){
	    					$childCateNew[] = $ca;
	    					$childCateIdNew[] = $ca['id'];
	    				}else{
	    					$count ++;
	    				}
	    			}
	    			
	    			if($count > 0){
	    				$boolen = true;
	    				$childCateNew[] = $currentRow;
	    				$childCateIdNew[] = $currentRow['id'];
	    			}
    			}else{
    				$boolen = true;
    				$childCateNew[] = $currentRow;
    				$childCateIdNew[] = $currentRow['id'];
    			}
    			
    			if($boolen){
    				$update = array(
    						'$set' => 
	    						array(
	    						'child_cate_id' => $childCateIdNew,
	    						'child_category' => $childCateNew
	    					)
	    				);
    				
    				$r = $this->update($where,$update);
    			}
    			
    			
    		}
    		
    		//Xoa category khoi danh sach child_category
    		if($currentRow['parent_id'] != ''){
    			$where2 = array('id' => $currentRow['parent_id']);
	    		$r = $this->findOne($where2);
	    		if($r['success']){
		    		$cate = $r['result'];
	    			$child_category = (isset($cate['child_category']) && $cate['child_category']) ? $cate['child_category'] : array();
	    	
	    			$childCateNew2 = array();
	    			$childCateIdNew2 = array('');
	    			$boolen = false;
	    			$count = 0;
	    			
	    			if(count($child_category) > 0){
		    			foreach ($child_category as $ca){
		    				if	($ca['id'] != $currentRow['id']){
		    					$childCateNew2[] = $ca;
		    					$childCateIdNew2[] = $ca['id'];
		    				}else{
		    					$count ++;
		    				}
		    			}
		    			
		    			if($count > 0){
		    				$boolen = true;
		    			}
	    			}
	    			
	    			if($boolen){
	    				$update2 = array(
	    						'$set' => 
		    						array(
		    						'child_cate_id' => $childCateIdNew2,
		    						'child_category' => $childCateNew2
		    					)
		    				);
	    				
	    				$r = $this->update($where2,$update2);
	    			}
	    		}
    		}
    	}
    	
        return array('success' => true, 'result' => $data);    
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
	    return array('success' => true, 'result' => $comment);
	}
	
	public function beforeUpdateComment($where, $data, $row)
	{
        if($data['$set']['_cl_step'] == 'is_spam') {
            // incresase counter.spam
            $data['$inc'] = array('counter.spam' => 1);
        }
		return array('success' => true, 'result' => $data);
	}
	
	
	public function afterUpdateComment($where, $data, $row)
	{
        if(($data['$set']['_cl_step'] == 'is_spam') && 
                (in_array('admin', $data['$set']['roles']) || in_array('root', $data['$set']['roles']))
           )
        {
            // mark is_spammer
            $dataUpdate = array('$set' => array('is_spam' => 1));
            $cWhere = array('id' => $row['id']);
            Site_Codenamex_Dao_Comment_Category::getInstance()->update($cWhere, $dataUpdate);
            
            // TODO: 
        }
        
		return array('success' => true, 'result' => $data);
	}
	
	/**
	 * Delete a single node by its Id
	 * @param MongoID $nodeId
	 */
	public function afterDeleteNode($row)
	{
	    //delete all comments
	    //$commentDao = Site_Codenamex_Dao_Comment_Category::getInstance();
	    //$where = array('node.id' => $row['id']);
	    //$commentDao->delete($where);
	    
	    //return array('success' => true, 'result' => $row);
		return array('success' => true);
	}
	
	
	/**
	 * Prepare data for new node insert
	 * @param Array $dataArray
	 */
	public function prepareFormDataForDaoInsert($dataArray = array(), $formName = "Category_Form_New")
	{
		return $dataArray;
	}	
	
	public function prepareCommentFormDataForDao($dataArray = array())
	{
		return $dataArray;
	}	

	/******************************RELATION*********************************/
	public function beforeInsertRelation($data)
	{
		return array('success' => true, 'result' => $data);
	}
	public function afterInsertRelation($data, $newRelations, $currentRow)
	{
		return array('success' => true, 'result' => $data);
	}
	public function afterDeleteRelation($currentRow, $rt, $newRelations = array())
	{
	    return array('success' => true);
	}
	
	public function filterNewObjectForAjax($obj, $formData)
	{
		return array('id' => $obj['id'] /*, 'slug' => $obj['slug'] */);
	}
	
	public function filterUpdatedObjectForAjax($currentRow, $step, $data, $returnedData)
	{
		$ret = array('id' => $currentRow['id']);
		return $ret;
		/*
		 if (isset($data['slug']))
			$ret['slug'] = $data['slug'];
		elseif (isset($currentRow['slug']))
		$ret['slug'] = $currentRow['slug'];
		return $ret;
		*/
	}
	
	public function getCategoryLevelOne(){
		$where = array('level' => 1,
					   'status' => 'approved'
		);
		$cond['where'] = $where;
		$r = $this->findAll($cond);
		
		if($r['success']){
			return $r['result'];
		}else{
			return array();
		}
	}
	
	public function getCategoryLevel($level){
	    if(isset($level) || $level != '')
	       $level = $level;
	    else
	        $level = 1;
	    
	    $where = array('level' => $level);
	    $cond['where'] = $where;
	    $r = $this->findAll($cond);
	
	    if($r['success']){
	        return $r['result'];
	    }else{
	        return array();
	    }
	}
}
