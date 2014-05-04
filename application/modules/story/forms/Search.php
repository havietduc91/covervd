<?php 
class Story_Form_Search extends Cl_Form_Search
{

	public function init()
	{
		parent::init();
		$this->method= "GET";
		$this->fieldList = array('iid', 
		    'name', 'status','type', 'order_ts',
		    'order_counter.v',
		    'order_counter.point',
		    'order_counter.c',
		    'order_counter.hn',
		    );
    	$this->setCbHelper('Story_Form_Helper');
    	//$this->setDisplayInline();
	}
	public function setStep($step)
	{
	  
	    parent::setStep($step);
	}
	//protected $_fieldListConfig; @see Cl_Dao_Foo
	
	
    //we must have it here as separate from $_fieldListConfig
    //because some configs will be merged with another file
    protected function _formFieldsConfig()
    {
    	$type = story_type();
    	$type[''] = 'all';
    	return array(
	    	'status' => array(
	    		'type' => 'MultiCheckbox', /* 'MultiCheckbox', */
	    		'options' => array(
					'label' => "",
	    			'disableLoadDefaultDecorators' => true,
	//    			'required' => true,
		    		'filters' => array('StringTrim', 'StripTags'),
	    		),
	    		'op' => '$in',
	    		'multiOptionsCallback' => array(array('Story_Form_Helper', 'getStatus')),
	    		'defaultValue' => array()
	    	),
    		'iid' => array(
    					'type' => 'Text',
    					'options' => array(
    							'label' => "Story Id",
    							'filters' => array('StringTrim', 'StripTags')
    					),
    					'op' => '$eq',
    		),
    			 
	    	'name' => array(
	    		'type' => 'Text',
	    		'options' => array(
	    			'label' => "Story name",
		    		'filters' => array('StringTrim', 'StripTags')
	    		),
	    		'op' => '$like',
	    	),
	    	'type' => array(
	    				'type' => 'Select',
	    				'options' => array(
	    						'label' => t('story_type', 1),
	    						'disableLoadDefaultDecorators' => false,
	    						'required' => true,
	    						'filters' => array('StringTrim', 'StripTags')
	    				),
	    				'multiOptions' => type_slug()
   			),
			'items_per_page' => array(
	        		'type' => 'Select', 
	        		'options' => array(
	    				'label' => "Display",
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
    	    'order_ts' => $this->generateFormElement('_cl_order', 'Order by Create time', -1),
    	    'order_counter.v' => $this->generateFormElement('_cl_order', 'Order by Views', -1),
    	    'order_counter.point' => $this->generateFormElement('_cl_order', 'Order by Point', 1),
    	    'order_counter.c' => $this->generateFormElement('_cl_order', 'Order by Comments', 1),
    	    'order_counter.hn' => $this->generateFormElement('_cl_order', 'Order by Hn', 1),
    	    	
	    );
    }
}
