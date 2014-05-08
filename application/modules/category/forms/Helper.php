<?php
class Category_Form_Helper extends Cl_Form_NodeHelper
{
    public function getStatus()
    {
    	$ret = array('approved' => 'Đã duyệt', 'queued' => 'Đang chờ');
    	return array('success' =>true, 'result' => $ret);
    }
    
    public function getLevel()
    {
    	$ret = array(1 => 'Level 1', 2 => 'Level 2');
    	return array('success' =>true, 'result' => $ret);
    }
    
    public function getParentCategory(){
    	$where = array('level' => 1);
    	$cond['where'] = $where;
    	$r = Dao_Node_Category::getInstance()->findAll($cond);
    	//v($r);
    	$cates = array();
    	if($r['success']){
    		foreach ($r['result'] as $ca){
    			$cate = array($ca['id'] => $ca['name']);
    			
    			$cates = array_merge($cate,$cates);
    		}
    		
    		$cates = array_merge(array(''=> 'Không chọn'),$cates);
    		return array('success' =>true, 'result' => $cates);
    	}else{
    		return array('success' =>true, 'result' => array());
    	}
    }
    
    /*
    public function getItemsPerPageList($params)
    {
    	$ret = array(
    	    '-1' => "All",
    		'10' => "10/page",
    		'20' => "20/page",
    		'30' => "30/page",	
    		'50' => "50/page");
    	return array('success' => true, 'result' => $ret);
    }
    */
}
