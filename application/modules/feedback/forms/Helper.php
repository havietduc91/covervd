<?php
class Feedback_Form_Helper extends Cl_Form_NodeHelper
{
    public function getStatus()
    {
    	$ret = array('approved' => 'Đã duyệt', 'queued' => 'Đang chờ');
    	return array('success' =>true, 'result' => $ret);
    }
    
    public function getStatusFeedback()
    {
    	$ret = array('excellent' => 'Rất tốt', 'good' => 'Tốt', 'avarage' => 'Trung bình',
    			'below_average' => 'Kém', 'poor' => 'Rất kém'
    	);
    	//Rất tốt, tốt, trung bình, kém, rất kém
    	return array('success' =>true, 'result' => $ret);
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
