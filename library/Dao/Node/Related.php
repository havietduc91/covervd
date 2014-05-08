<?php
class Dao_Node_Related extends Cl_Dao_Node
{
    public $nodeType = 'related';
    public $cSchema = array(
    		'id' => 'string',
    		'iid' => 'int',
    	    'note' => 'string',
    	    'images' => 'string',
    	    "name" => 'string',
    	    "description" => 'string',
    	    "meta_description" => 'string',
    	    "price" => 'float',
    	    'link' => 'string',
    	    'origin_price' => 'float',
        	'brand' => 'string', //eg: pc, trananh, phucanh
        	'avatar_brand' => 'string',
        	'status' => 'string',
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
	    $product = Dao_Node_Product::getInstance()->cSchema;
    	return array(
    		'collectionName' => 'related',
        	'documentSchemaArray' => array(
        		'ts' => 'int',
    	        'iid' => 'int',
    	        'note' => 'string',
    	        'images' => 'string',
    	        "name" => 'string',
    	        "description" => 'string',
    	        "meta_description" => 'string',
    	        "price" => 'float',
    	        'link' => 'string',
    	        'origin_price' => 'float',
        		'brand' => 'string', //eg: pc, trananh, phucanh
        		'avatar_brand' => 'string',
        		'status' => 'string',
        		'product' => $product,
        	),
	        'indexes' => array(
	                array(
	                        array(
	                                'iid' => 1
	                        ),
	                        array(
	                                "unique" => true,
	                                "dropDups" => true
	                        )
	                ),
	                array(
	                        array(
	                                'serialNumber' => 1
	                        ),
	                        array(
	                                "unique" => true,
	                                "dropDups" => true
	                        )
	                ),
	        )
    	);
	}
	
    /**
     * Add new node
     * @param post data Array $data
     */
	public function beforeInsertNode($data)
	{
		if($data['images'] != ''){
			$data['images'] = remove_ufiles_from_images_url($data['images']);
		}
		
		if($data['avatar_brand'] != ''){
			$data['avatar_brand'] = remove_ufiles_from_images_url($data['avatar_brand']);
		}
		
		if(isset($data['product']['id'])){
			$r = Dao_Node_Product::getInstance()->getCacheObject($data['product']['id']);
		}
		
		if(isset($r['success']) && $r['success']){
			$data['product'] = $r['result'];
		}else{
			$data['product'] = array();
		}
		
        return array('success' => true, 'result' => $data);
	}
	
	public function afterInsertNode($data, $row)
	{
		//TODO: update product into related
		if(isset($data['product']['id'])){
			$r = Dao_Node_Product::getInstance()->findOne(array('id'=>$data['product']['id']));
			if($r['success'] && count($r['result']) > 0){
				$relateds = isset($r['result']['related']) ? $r['result']['related'] : array();
				$relateds[] = $row;
				$update = array('$set' => 
							array('related' => $relateds)
				);
				$where = array('id' => $data['product']['id']);
				
				Dao_Node_Product::getInstance()->update($where,$update);
			}
		}
		
        return array('success'=> true, 'result' => $row);
	}
	
    /******************************UPDATE****************************/
    public function beforeUpdateNode($where, $data, $currentRow)
    {
        /*
         * You have $data['$set']['_cl_step'] and $data['$set']['_u'] available
         */
        return array('success' => true, 'result' => $data);
    }
    
	public function afterUpdateNode($where, $data, $currentRow)
    {
    	if(isset($currentRow['product']['id'])){
    		$r = Dao_Node_Product::getInstance()->findOne(array('id'=>$currentRow['product']['id']));
    		if($r['success'] && count($r['result']) > 0){
    			$relateds = isset($r['result']['related']) ? $r['result']['related'] : array();
    			$newRelateds = array();
    			$brand = (isset($data['$set']['string']) && $data['$set']['string'] != null) ? $data['$set']['string'] : '';
    			 
    			$currentUpdate = array(
    					'id' => (string)$data['$set']['id'],
    					'iid' => intval($data['$set']['iid']),
    					'note' => $data['$set']['note'],
    					'images' => $data['$set']['images'],
    					"name" => $data['$set']['name'],
    					"description" => $data['$set']['description'],
    					"meta_description" => $data['$set']['meta_description'],
    					"price" => (float)$data['$set']['price'],
    					'link' => $data['$set']['link'],
    					'origin_price' => (float)$data['$set']['origin_price'],
    					'brand' => $brand, //eg: pc, trananh, phucanh
    					'avatar_brand' => $data['$set']['avatar_brand'],
    					'status' => $data['$set']['status'],
    			);
    			
    			foreach ($relateds as $row){
    				if($row['id'] !== $currentRow['id']){
    					$newRelateds[] = $row;
    				}else{
    					$newRelateds[] = $currentUpdate;
    				}
    			}
    			
    			$update = array('$set' =>
    					array('related' => $newRelateds)
    			);
    			$where = array('id' => $currentRow['product']['id']);
    	
    			$r = Dao_Node_Product::getInstance()->update($where,$update);
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
            Site_Codenamex_Dao_Comment_Related::getInstance()->update($cWhere, $dataUpdate);
            
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
	    return array('success' => true, 'result' => $row);
	}
	
	
	/**
	 * Prepare data for new node insert
	 * @param Array $dataArray
	 */
	public function prepareFormDataForDaoInsert($dataArray = array(), $formName = "Related_Form_New")
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
	
}
