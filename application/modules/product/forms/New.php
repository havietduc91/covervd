<?php 
class Product_Form_New extends Cl_Form
{
	public function init()
	{
		parent::init();
		$this->fieldList = array(
    	        'supplierName',
    	        'model',
    	        'condition',
    	        'serialNumber',
    	        'location',
    	        'modifiedDate',
    	        'receivedDate', // unix timestamp , at 00:00:00 of that date
    	        'soldDate', // unix timestamp , at 00:00:00 of that date
    	        'StockStatus', // 0 NOTINStock, 1 => InStock, 2 => Missing
    	        'note',
    	        'images',
    	        'category',
    	        'type',
    	        "name",
    	        "description",
    	        "meta_description",
    	        "key_word",
    	        'quantity',
    	        "price",
    	        'weight',
    	        'length',
    	        "width",
    	        'height',
    	        'viewed',
    	        'saled',
    	        'counter',
				'status',
		        'saledate_start',
    	        'saledate_end',
    	        'deal_price',
    	        'origin_price',
    	        'gallery',
		        'parent_category_iid'
		);
		$this->setCbHelper('Product_Form_Helper');
		
	}
	
	
    public function setStep($step, $currentRow = null)
	{
			$this->fieldList = array(
	            'supplierName','name', 'model','serialNumber', 'receivedDate',
	            'images', 'images_deleted','img_canvas','upload_img',
	            'note','description','saledate_start','saledate_end',
	            //'price',
				'parent_category_iid','deal_price',
    	        'origin_price',
				'status',
			);
	}
	
    protected function _formFieldsConfigCallback()
    {
        $ret = array(
        	'supplierName' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Tên người up",
        			'required' => true,
    	    		'filters' => array('StringTrim', 'StripTags'),
                    'validators' => array('NotEmpty'),
        		),
                //'permission' => 'update_task'
        	),
            'model' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Model",
                            'required' => true,
                            'filters' => array('StringTrim', 'StripTags'),
                            'validators' => array('NotEmpty'),
                    ),
                    //'permission' => 'update_task'
            ),
            'condition' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Condition",
                            'required' => true,
                            'filters' => array('StringTrim', 'StripTags'),
                            'validators' => array('NotEmpty'),
                    ),
                    //'permission' => 'update_task'
            ),
        	'serialNumber' => array(
        		'type' => 'Text',
        		'options' => array(
        	        'label' => "SerialNumber",
    	    		'filters' => array('StringTrim', 'StripTags'),
        			'prefixPath' => array(
        				"filter" => array (
        					"Filter" => "Filter/"
        				)
        			)
        		),
        	),
            'location' => array(
                    'type' => 'Select',
                    'options' => array(
                            'label' => "Địa chỉ",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'category' => array(
                    'type' => 'Select',
                    'options' => array(
                            'label' => "Chuyên mục",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'receivedDate' => array(
                'type' => 'Text',
                'options' => array(
                    'label' => "Ngày nhận",
                    'class' => 'datetimepicker',
                    'autocomplete'=>'off',
                    'placeholder'=>'click here to pick a date',
                    'disableLoadDefaultDecorators' => false,
                    'required' => true,
                    'transformers' => array(
                        'dateToUnixTimestampProduct'
                    )
                ),
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
            ),
            'saledate_start' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Ngày bắt đầu bán",
                            'class' => 'datetimepicker',
                            'autocomplete'=>'off',
                            'placeholder'=>'click here to pick a date',
                            'disableLoadDefaultDecorators' => false,
                            'required' => true,
                            'transformers' => array(
                                    'dateToUnixTimestampProduct'
                            )
                    ),
                    'filters' => array(
                            'StringTrim',
                            'StripTags'
                    ),
            ),
            'saledate_end' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Ngày kết thúc",
                            'class' => 'datetimepicker',
                            'autocomplete'=>'off',
                            'placeholder'=>'click here to pick a date',
                            'disableLoadDefaultDecorators' => false,
                            'required' => true,
                            'transformers' => array(
                                    'dateToUnixTimestampProduct'
                            )
                    ),
                    'filters' => array(
                            'StringTrim',
                            'StripTags'
                    ),
            ),
            'modifiedDate' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "ModifiedDate",
                    		'class' => 'datetimepicker',
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'soldDate' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "SoldDate",
                    		'class' => 'datepicker',
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
           'stockStatus' => array(
                    'type' => 'Select',
                    'options' => array(
                            'label' => "Stock Status",
                            'disableLoadDefaultDecorators' => false,
                            'required' => false,
                            'filters' => array('StringTrim', 'StripTags')
                    ),
                    'multiOptionsCallback' => array(array('Product_Form_Helper', 'getStockStatus')),
                    'defaultValue' => '1'
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
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Ảnh",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'weight' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Cân nặng",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'length' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Dài",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'width' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Rộng",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'height' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Cao",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'viewed' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Số người xem",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'saled' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "product saled",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'counter' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "counter",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'type' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "type",
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
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
            'key_word' => array(
                    'type' => 'Textarea',
                    'options' => array(
                            'label' => "Key word",
                    		'class' => 'isEditor',
                            'filters' => array('StringTrim', 'StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'quantity' => array(
                    'type' => 'Textarea',
                    'options' => array(
                            'label' => "Quantity",
                    		'class' => 'isEditor',
                            'filters' => array('StringTrim', 'Digits'),
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
            'deal_price' => array(
                    'type' => 'Text',
                    'options' => array(
                            'label' => "Giá khuyến mãi",
                            'filters' => array('StringTrim', 'StripTags','Digits'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
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
            'content' => array(
                    'type' => 'Textarea',
                    'options' => array(
                            'label' => "Nội dung",
                            'class' => 'isEditor',
                            'filters' => array('StringTrim', 'NodePost'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            )
                    ),
            ),
            'parent_category_iid' => array(
                    'type' => 'Select',
                    'options' => array(
                            'label' => "Chuyên mục cha",
                            'filters' => array('StringTrim','StripTags'),
                            'prefixPath' => array(
                                    "filter" => array (
                                            "Filter" => "Filter/"
                                    )
                            ),
                    ),
                    'multiOptionsCallback' => array('getParentCategoryList'),
                    'defaultValue' => 'NoParent'
            ),
            'status' => array(
            		'type' => 'Select',
            		'options' => array(
            				'label' => 'Trạng thái',
            				'required' => true,
            		),
            		'multiOptionsCallback' => array('getStatus')
            ),
        	'avatar' => array(
        			'type' => 'Ảnh',
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
