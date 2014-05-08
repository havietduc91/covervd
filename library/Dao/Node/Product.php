<?php
class Dao_Node_Product extends Cl_Dao_Node
{
    public $nodeType = 'product';
    public $cSchema = array('id' => 'string', 'iid' => 'int', 'name' => 'string', 'images' => 'string');
        
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
	    $category = Dao_Node_Category::getInstance()->cSchema;
	    //$related = Dao_Node_Related::getInstance()->cSchema;
	    $related = array(
	    		'id' => 'string',
	    		'iid' => 'int',
	    		'note' => 'string',
	    		'images' => 'string',
	    		"name" => 'string',
	    		"description" => 'string',
	    		"meta_description" => 'string',
	    		"price" => 'int',
	    		'link' => 'string',
	    		'origin_price' => 'float',
	    		'brand' => 'string', //eg: pc, trananh, phucanh
	    		'avatar_brand' => 'string',
	    		'status' => 'string',
	    );
	    
    	return array(
    		'collectionName' => 'product',
        	'documentSchemaArray' => array(
        		'ts' => 'int',
    	        'iid' => 'int',
    	        'supplierName' => 'string',
    	        'model' => 'string',
    	        'condition' => 'enum',
    	        'serialNumber' => 'string',
    	        'location' => 'string',
    	        'modifiedDate' => 'int',
    	        'receivedDate' => 'int', // unix timestamp , at 00:00:00 of that date
    	        'soldDate' => 'int', // unix timestamp , at 00:00:00 of that date
    	        'stockStatus' => 'string', // 0 NOTINStock, 1 => InStock, 2 => Missing
    	        'note' => 'string',
    	        'images' => 'string',
    	        'weight' => 'float',
    	        'type' => 'string',
    	        "name" => 'string',
    	        "description" => 'string',
    	        "meta_description" => 'string',
    	        "key_word" => 'string',
    	        'quantity' => 'int',
    	        'manufacturer_id' => 'string',
    	        'shipping' => 'int',
    	        "price" => 'int',
    	        'weight' => 'float',
    	        'length' => 'float',
    	        "width" => 'float',
    	        'height' => 'float',
    	        'slug' => 'string',
    	        'counter' => array(
    	                'saled' => 'int', // so luong hang ban duoc
    	                'viewed' => 'int', //so luot ghe tham san pham
    	                'instock' => 'int', //so luong hang ton kho
    	        		'queued' => 'int', //so luong hang da dat
    	        ),
    	        'saledate_start' => 'int',
    	        'saledate_end' => 'int',
    	        'deal_price' => 'float',
    	        'origin_price' => 'float',
    	        'gallery' => 'array',
        	    'parent_category_iid' => 'string',
        		'related' => array(
        				$related
        		),
    	        'u' => $user, //who posted this	
    	        'category' => $category
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
		if(!isset($data['ts'])){
			$data['ts'] = time();
		}
		
		if($data['images'] != ''){
			$data['images'] = remove_ufiles_from_images_url($data['images']);	
		}
		
		if (!isset($data['slug']))
		{
		    $tempSlug = Cl_Utility::getInstance()->generateSlug($data['name']);
		    $data['slug'] = $this->generateUniqueSlug(explode('-', $tempSlug));
		}
		
	    if (!isset($data['iid']))
	    {
	        $redis = init_redis(RDB_CACHE_DB);
	        $data['iid'] = $redis->incr($this->nodeType . ":iid"); //unique node id
	    }
	    
	    $where = array('iid' => $data['parent_category_iid']);
	    $r = Dao_Node_Category::getInstance()->findOne($where);
	    $category = array();
	    if($r['success']){
	    	$category = $r['result'];
	    	$data['category'] = $category;
	    }
	    
        return array('success' => true, 'result' => $data);
	}
	
	public function afterInsertNode($data, $row)
	{
        parent::afterInsertNode($data, $row);
        return array(
            'success' => true,
            'result' => $row
        );
	}
	
    /******************************UPDATE****************************/
    public function beforeUpdateNode($where, $data, $currentRow)
    {
    	if($data['$set']['parent_category_iid'] != $currentRow['parent_category_iid']){
	    	$where = array('iid' => $data['$set']['parent_category_iid']);
	    	$r = Dao_Node_Category::getInstance()->findOne($where);
	    	$category = array();
	    	if($r['success']){
	    		$category = $r['result'];
	    		$data['$set']['category'] = $category;
	    	}
    	}
    	
    	if(!isset($data['$set']['ts'])){
    		$data['$set']['ts'] = time();
    	}
    	
    	if($data['$set']['images'] != ''){
    		$data['$set']['images'] = remove_ufiles_from_images_url($data['$set']['images']);
    	}
        /*
         * You have $data['$set']['_cl_step'] and $data['$set']['_u'] available
         */
        return array('success' => true, 'result' => $data);
    }
    
	public function afterUpdateNode($where, $data, $currentRow)
    {
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
            Site_Codenamex_Dao_Comment_Product::getInstance()->update($cWhere, $dataUpdate);
            
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
	    $commentDao = Site_Codenamex_Dao_Comment_Product::getInstance();
	    $where = array('node.id' => $row['id']);
	    $commentDao->delete($where);
	    
	    return array('success' => true, 'result' => $row);
	}
	
	
	/**
	 * Prepare data for new node insert
	 * @param Array $dataArray
	 */
	public function prepareFormDataForDaoInsert($dataArray = array(), $formName = "Product_Form_New")
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
	
	public function ProductView($id){
	    $where = array('id' => $id);
	    $r = $this->findOne($where);
	    if($r['success'] && $r['count'] >0)
	       return $r;
	}
	
	public function getStandedProduct($product){
		$productNew = $product;
		$productNew['category']['name'] = isset($product['category']['name']) ? $product['category']['name'] : '';
		$productNew['counter']['buy'] = isset($product['count']['buy']) ? $product['count']['buy'] : 0;
		$productNew['origin_price'] = isset($product['origin_price']) ? $product['origin_price'] : 0;
		$productNew['deal_price'] = (isset($product['deal_price']) && $product['deal_price'] != 0) ? $product['deal_price'] : $productNew['origin_price'];
		$product['images'] = isset($product['images']) ? $product['images'] : default_avatar('product');
	
		return $productNew;
	}
	
	public function getHomePageProduct($iid){
		$where = array('iid' => $iid);
		//$where = array('id' => '532246490b08d1eb0c000000');
		$r = Dao_Node_Product::getInstance()->findOne($where);
		 
		if($r['success']){
			$hp_product = $r['result'];
		}else{
			$hp_product = array();
		}
		
		return $hp_product;
	}
	
	public function getRecommendProduct($recommend_products_iid){
		$iids = explode(',',$recommend_products_iid);
		$iidsNew = array();
		 
		if(count($iids) > 0){
			foreach ($iids as $iid){
				if(trim($iid) != '')
					$iidsNew[] = (int)trim($iid);
			}
		}
		 
		$reCWhere = array('iid' => array('$in' => $iidsNew));
		$cond['limit'] = 4;
		$cond['where'] = $reCWhere; //recommend where
		$r = Dao_Node_Product::getInstance()->find($cond);
		
		if($r['success']){
			$recommend_products = $r['result'];
		}else{
			$recommend_products = array();
		}
		
		return $recommend_products;
	}
	
	public function getProductsByCategoryIid($category_iid){
		$where = array('category.iid' => $category_iid);
		//$where = array();
		$cond['where'] = $where;
		$cond['limit'] = 4;
		//$order['ts'] = 1;
		//$cond['order'] = $order;
		
		$r = Dao_Node_Product::getInstance()->find($cond);
		
		if($r['success']){
			$style1_products = $r['result'];
		}else{
			$style1_products  = array();
		}
		
		return $style1_products;
	}
	
	public function getProductsByCategorysIids($category_iids, $limit){
		$cateIids = explode(',',$category_iids);
		$cateIidsNew = array();
			
		if(count($cateIids) > 0){
			foreach ($cateIids as $iid){
				if(trim($iid) != '')
					$cateIidsNew[] = (int)trim($iid);
			}
		}
		
		if(count($cateIidsNew)){
			$categories = array();
			foreach ($cateIidsNew as $cateIid){
				$where = array('category.iid' => $cateIid);
				//$where = array();
				$cond['where'] = $where;
				$cond['limit'] = $limit;
				//$order['ts'] = 1;
				//$cond['order'] = $order;
		
				$r = Dao_Node_Product::getInstance()->find($cond);
		
				if($r['success']){
					$products = $r['result'];
				}else{
					$products  = array();
				}
		
				$where = array('iid' => $cateIid);
				$r = Dao_Node_Category::getInstance()->findOne($where);
		        if($r['success'] && $r['count'] >0)
				    $detailCate = $r['result'];
		        else 
		            $detailCate = array();
				$categorie['detail'] = $detailCate;
				$categorie['products'] = $products;
				$categories[] = $categorie;
			}
		}
		
		return $categories;
	}
	
	public function getProductsByType($type, $cateId, $limit){
		if(isset($cateId) && $cateId !=''){
			$where = array('category.id' => $cateId);
			$cond['where'] = $where;
		}
		if($type == 'newest'){
	    	$order = array('ts' => -1);
		}elseif ($type == 'bestSelling'){
			$order = array('counter.saled' => -1);
		}else{//type dealest
			//TODO: Chua biet lay theo cong thuc nao
			$order = array('counter.saled' => -1);
		}
		$cond['order'] = $order;
		$cond['limit'] = $limit;
		//$cond['limit'] = 3;
		
		$r = Dao_Node_Product::getInstance()->find($cond);
		if($r['success']){
			$products = $r['result'];
		}else{
			$products = array();
		}
		
		return $products;
	}
	
	public function getRelatedProductsOfBrands($id){
		$where = array('product.id' => $id);
		$cond['where'] = $where;
		
		$r = Dao_Node_Related::getInstance()->findAll($cond);
		if($r['success']){
			return  $r['result'];
		}else{
			return array();
		}
	}
}
