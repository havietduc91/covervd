<?php 
class Bill_Form_New extends Cl_Form
{
	public function init()
	{
		parent::init();
		$this->fieldList = array('uname', 'umail', 'uage', 'uadress', 'ucity', 'status', 'uphone', 
				'note', 'product.id', 'product.iid'
		);
		$this->setCbHelper('Bill_Form_Helper');
		
	}
	public function setStep($step, $currentRow = null)
	{
		parent::setStep($step, $currentRow);
	}
	
    protected function _formFieldsConfigCallback()
    {
        $ret = array(
        	'uname' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Tên khách hàng",
        			'required' => true,
    	    		'filters' => array('StringTrim', 'StripTags'),
                    'validators' => array('NotEmpty'),
        			'placeholder' => 'Nhập tên của bạn'
        		),
        	),
        	'uage' => array(
        			'type' => 'Text',
       				'options' => array(
       						'label' => "Tuổi của bạn",
       						'required' => true,
       						'filters' => array('StringTrim', 'StripTags'),
       						'validators' => array('NotEmpty'),
       				),
       		),
        	'umail' => array(
        			'type' => 'Text',
        				'options' => array(
       						'label' => "Email",
       						'required' => true,
       						'filters' => array('StringTrim', 'StripTags'),
       						'validators' => array('NotEmpty'),
		        			'placeholder' => 'Bạn có thể để trống nêu không có'
       				),
       		),
       		'uadress' => array(
        			'type' => 'Text',
        			'options' => array(
        					'label' => "Địa chỉ",
        					'required' => true,
       						'filters' => array('StringTrim', 'StripTags'),
       						'validators' => array('NotEmpty'),
		       				'placeholder' => 'Ghi rõ địa chỉ để tiện nhận hàng'
       				),
       		),
       		'ucity' => array(
       				'type' => 'Text',
        			'options' => array(
        					'label' => "Thành phố",
        					'required' => true,
        					'filters' => array('StringTrim', 'StripTags'),
       						'validators' => array('NotEmpty'),
       				),
       		),
        	'uphone' => array(
        			'type' => 'Text',
        			'options' => array(
        					'label' => "Số điện thoại",
        					'required' => true,
       						'filters' => array('StringTrim', 'StripTags'),
       						'validators' => array('NotEmpty'),
		        			'placeholder' => 'Ghi số điện thoại để tiện liên lạc'
       				),
       		),
        	'product.id' => array(
        			'type' => 'Hidden',
        	),
        	'product.iid' => array(
        			'type' => 'Hidden',
       		),
        	'note' => array(
        		'type' => 'Textarea',
        		'options' => array(
        	        'label' => "Chú thích",
        	        'class' => 'isEditor',
    	    		'filters' => array('StringTrim', 'NodePost'),
        			'prefixPath' => array(
        				"filter" => array (
        					"Filter" => "Filter/"
        				)
        			),
        			'placeholder' => 'Ví dụ: Màu đen'
        		),
        	),
            'status' => array(
            		'type' => 'Select',
            		'options' => array(
            				'label' => 'Trạng thái',
            				'required' => true,
            		),
            		'multiOptionsCallback' => array('getStatus')
            ),
        	'product.model' => array(
        			'type' => 'Text',
       				'options' => array(
       						'label' => "Model sản phẩm",
       						'required' => true,
       						'filters' => array('StringTrim', 'StripTags'),
       						'validators' => array('NotEmpty'),
       				),
       		),
        	'product.serialNumber' => array(
        			'type' => 'Text',
       					'options' => array(
       						'label' => "SerialNumber Sản phẩm",
       						'required' => true,
       						'filters' => array('StringTrim', 'StripTags'),
       						'validators' => array('NotEmpty'),
       				),
       		),
        );
        return $ret;
    }
    /**TODO: hook here if needed
    public function customIsValid($data)
    {
        return array('success' => false, 'err' => "If customIsValid exist. You must implement it");
    }
    */
}
