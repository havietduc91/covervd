<?php 
class Feedback_Form_Search extends Cl_Form_Search
{

	public function init()
	{
		parent::init();
		$this->method= "GET";
		$this->fieldList = array('name', 'status');
    	$this->setCbHelper('Feedback_Form_Helper');
    	//$this->setDisplayInline();
	}
	public function setStep($step, $currentRow = null)
	{
	    parent::setStep($step, $currentRow);
	}
	//protected $_fieldListConfig; @see Cl_Dao_Foo
	
	
    //we must have it here as separate from $_fieldListConfig
    //because some configs will be merged with another file
    protected $_formFieldsConfig = array(
    	'status' => array(
    		'type' => 'MultiCheckbox', /* 'MultiCheckbox', */
    		'options' => array(
				'label' => "Trạng thái",
    			'disableLoadDefaultDecorators' => true,
//    			'required' => true,
	    		'filters' => array('StringTrim', 'StripTags'),
    		),
    		'op' => '$in',
    		'multiOptionsCallback' => array(array('Feedback_Form_Helper', 'getStatus')),
    		'defaultValue' => array()
    	),
    	'name' => array(
    		'type' => 'Text',
    		'options' => array(
    			'label' => "Tên người phản hồi",
	    		'filters' => array('StringTrim', 'StripTags')
    		),
    		'op' => '$like',
    	),
		'items_per_page' => array(
        		'type' => 'Select', 
        		'options' => array(
    				'label' => "Hiển thị",
        			'disableLoadDefaultDecorators' => false,
        			'required' => true,
    	    		'filters' => array('StringTrim', 'StripTags')
        		),
        		//or you can implement getItemsPerPageList here
        		//'multiOptions' => array('getItemsPerPageList'),
        		'multiOptions' => array(
		    	    '-1' => "All",
            		'10' => "10/page",
            		'20' => "20/page",
            		'30' => "30/page",	
            		'50' => "50/page"
        		),
        		'defaultValue' => 10
    	),    	
    	'order_by_count' => array(
    		'type' => 'Select',
    		'options' => array(
    			'label' => "order_by_count",
    			'required' => true,
	    		'filters' => array('StringTrim', 'StripTags'),
    		),
    		'op' => '$eq',
    		'multiOptions' => array(
    			'ts' => 'created time',
    			'counter.c' => "comment count",
    		),
    	),
    );
}
