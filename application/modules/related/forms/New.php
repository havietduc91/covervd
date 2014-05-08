<?php 
class Related_Form_New extends Cl_Form
{
	public function init()
	{
		parent::init();
		$this->fieldList = array(
    	        'note',
    	        'images',
    	        "name",
    	        "description",
    	        "meta_description",
    	        //"price",
    	        'origin_price',
		        'brand', 
				'avatar_brand',
				'status',
				'link',
				'images_deleted','img_canvas','upload_img',
				'product.id',
		);
		$this->setCbHelper('Related_Form_Helper');
		
	}
	
	
    public function setStep($step, $currentRow = null)
	{
			$this->fieldList = array(
	            'note',
    	        'images',
    	        "name",
    	        "description",
    	        "meta_description",
    	        //"price",
    	       'origin_price',
		        'brand', 
				'avatar_brand',
				'status',
				'link',
				'images_deleted','img_canvas','upload_img',
				'product.id',
			);
	}
	
    protected function _formFieldsConfigCallback()
    {
        $ret = array(
        	'product.id' => array(
        		'type' => 'Hidden',
        	),
            'note' => array(
                    'type' => 'Textarea',
                    'options' => array(
                            'label' => "Note",
                    		'class' => 'isEditor',
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
           'images' => array(
                    'type' => 'Hidden',
                    'options' => array(
                            'class' => 'cl_upload',
                            //TODO:
                            'data-upload-label' => 'upload product picture',
                            'data-upload-title' => 'Choose file',
                            'type' => 'avatar_image',
                            'attribs' => array('cl_upload_text' => 'Avatar')
                    )
            ),
            'description' => array(
                    'type' => 'Textarea',
                    'options' => array(
                            'label' => "Mô tả",
                    		'class' => 'isEditor',
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'meta_description' => array(
                    'type' => 'Textarea',
                    'options' => array(
                            'label' => "Meta description",
                    		'class' => 'isEditor',
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'name' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Tên",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
        	'link' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Link",
        			'filters' => array('StringTrim', 'StripTags'),
        			'prefixPath' => array(
        				"filter" => array (
        					"Filter" => "Filter/"
        				)
        			)
        		),
        	),
            'price' => array(
                    'type' => 'Giá',
                    'options' => array(
                            'label' => "price",
                            'filters' => array('StringTrim', 'StripTags','Digits'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'note' => array(
                    'type' => 'Textarea',
                    'options' => array(
                            'label' => "Nội dung",
                            'class' => 'isEditor',
                            'filters' => array('StringTrim'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
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
        	'origin_price' => array(
            	'type' => 'Text',
                'options' => array(
                	'label' => "Giá gốc",
                    'required' => true,
                    'filters' => array('StringTrim', 'StripTags','Digits'),
                    'prefixPath' => array(
                    	"filter" => array (
                        	"Filter" => "Filter/"
                        )
                    )
                	),
            ),
        	'brand' => array(
        			'type' => 'Text',
        			'options' => array(
        				'label' => "Tên Hãng",
        				'filters' => array('StringTrim', 'StripTags'),
        				'prefixPath' => array(
        					"filter" => array (
        						"Filter" => "Filter/"
        					)
        				)
        			),
        		),
        	'avatar_brand' => array(
        			'type' => 'Hidden',
        			'options' => array(
        					'class' => 'cl_upload',
        					'filters' => array('StringTrim', 'StripTags')
        			),
        	),
        	'upload_img' => array(
        		'type' => 'Hidden',
        		'options' => array(
        			'multiple' => true
        		)
        	),
        	'images_tmp' => array(
        		'type' => 'Hidden',
        		'options' => array(
        			'filters' => array('StringTrim', 'StripTags'),
        			'transformers' => array("tokensJSONStringToArray")
        		)
        	),
        	'images_deleted' => array(
        		'type' => 'Hidden',
        		'options' => array(
        			'filters' => array('StringTrim', 'StripTags')
        		)
        	),
        	'img_canvas' => array(
        		'type' => 'Hidden',
        	),
        );
        return $ret;
    }
}
