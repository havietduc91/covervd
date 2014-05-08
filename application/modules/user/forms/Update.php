<?php 
class User_Form_Update extends Cl_Form_User_Update
{
    public function setStep($step, $currentRow = null) {
        if($step == 'adress')
        	$this->fieldList = array('phone', 'adress', 'street', 'city');
        else
	        $this->fieldList = $this->getFieldList($step);
    	parent::setStep($step, $currentRow);
    }
        
    public function init()
    {
    	parent::init();
    	$this->setCbHelper("User_Form_Helper");
    	if (method_exists($this, "_customFormFieldsConfig"))
    		$this->_formFieldsConfig = array_merge($this->_formFieldsConfig, $this->_customFormFieldsConfig());
    	 
    }
    
    protected function _customFormFieldsConfig()
    { 
        return array(
        	'phone' => array(
        			'type' => 'Text',
       				'options' => array(
       						'label' => 'Số điện thoại',
       						'placeholder' => 'Ví dụ: 0975996777',
       						//'required' => true,
       						'filters' => array('StringTrim', 'StripTags'),
       						'validators' => array (
       								'NotEmpty' ,
       								array('StringLength', false, array(0, 30))
        					)
        				),
        		),
        		'adress' => array(
        				'type' => 'Text',
        				'options' => array(
        						'label' => 'Địa chỉ nhà',
        						'placeholder' => 'Ví dụ: 48B',
        						//'required' => true,
        						'filters' => array('StringTrim', 'StripTags'),
        						'validators' => array (
        								'NotEmpty' ,
        								array('StringLength', false, array(0, 30))
        						)
        				),
        		),
        		'street' => array(
        				'type' => 'Text',
        				'options' => array(
        						'label' => 'Số đường',
        						'placeholder' => 'Đường Nguyễn Trãi',
        						//'required' => true,
        						'filters' => array('StringTrim', 'StripTags'),
        						'validators' => array (
        								'NotEmpty' ,
        								array('StringLength', false, array(0, 30))
        						)
        				),
        		),
        		'city' => array(
        				'type' => 'Text',
        				'options' => array(
        						'label' => 'Thành phố',
        						'placeholder' => 'Ví dụ: Hà Nội',
        						//'required' => true,
        						'filters' => array('StringTrim', 'StripTags'),
        						'validators' => array (
        								'NotEmpty' ,
        								array('StringLength', false, array(0, 30))
        						)
        				),
        		),
        /*
	    	'verify' => array(
	    		'type' => 'Text',
	    		'options' => array(
	    			'label' => "Verify",
	    			'class' => 'user-name',
	    			//'required' => true,
		    		'filters' => array('StringTrim', 'StripTags')
	    		),
	    	),
	    	*/
    	);
    }
}
