<?php
class Site_Form_Helper extends Cl_Form_NodeHelper
{
    public function getItemsPerPageList($params = NULL)
    {
    	$ret = array(
    		'10' => 10,
    		'20' => 20,
    		'30' => 30,	
    		'50' => 50,
    		'100' => 100,
    		'200' => 200,
    		'500' => 500
    	);
    	return array('success' => true, 'result' => $ret);
    }
	
}