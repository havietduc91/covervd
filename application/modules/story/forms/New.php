<?php
class Story_Form_New extends Cl_Form
{
	/* @name: story title
	 * @content: story content
	 * @type: kind of story 1<=story, 2<=img, 3<=video
	 * @status: queue, approved
	 * @url: img url , fill when @type=2 , youtubeurl @type=3
	 * @youtubeid : id of video in youtube, which can be embedded on site
	 * Editor: Chungth (huychungtran@gmail.com)
	 * */
	public function init()
	{
		parent::init();

		$this->fieldList = array(
		    //'is_iphone_fun', 
		    'url', 'name','upload_img', 'upload_flash',
		     'game_category', 
		    //'sender_name',
		    'answer', 'content','by', 
		    'type','tags', 'status','ytid','seo.title','seo.desc','images',   
		    'is_feature', 'img_canvas','pair1','pair2','source');

		$this->setCbHelper('Story_Form_Helper');

	}
	//protected $_fieldListConfig; @see Cl_Dao_Foo
	public function setStep($step, $currentRow = null)
	{
		if ($step == 'story')
		{
			$this->fieldList = array('name','content','source','type','tags','status','seo.title','seo.desc');
		}
		elseif ($step == 'image')
		{
		    if (get_value('iphone') != '' || 
		            (isset($currentRow['iphone'])  && $currentRow['is_iphone'] == 1))
		    {
		        $this->fieldList=array(
		        		'is_iphone_fun',
		                'name', 
		                'sender_name', 
		                'content','source',
		                'type','tags','status','seo.title','seo.desc','images', 'images_deleted','img_canvas');
		    }
		    else 
		        $this->fieldList=array(
		        		'url',
		                'upload_img',
		                'name', 'source',
		                //'content',
		                'type','tags','status',
		                'seo.title','seo.desc','images', 'images_deleted','img_canvas');
		}
		elseif($step == 'video')
			$this->fieldList=array('name','url','content','source','type','tags','status','ytid','seo.title','seo.desc');
		elseif($step == 'flash-game')
			$this->fieldList=array('name','url', 'game_category','content','source','type','tags','status','seo.title','seo.desc');
		elseif($step == 'quiz')
			$this->fieldList=array('name', 'content','source','answer', 'type', 'tags','status','seo.title','seo.desc');
		elseif($step == 'link')
			$this->fieldList=array('name','url', 'content','source','type','tags','status','seo.title','seo.desc');
		elseif($step == 'quote')
			$this->fieldList=array('name','content','by','source', 'type','tags','status','seo.title','seo.desc');
		elseif($step == 'multi-choice')
		    $this->fieldList=array(
		            'upload_img',
		            'name','source',
		            'type',
		            'images','pair1','pair2');
					
		parent::setStep($step, $currentRow);
	}


	protected function _formFieldsConfigCallback()
	{
		return array(
    		'name' => array(
	    		'type' => 'Text',
	    		'options' => array(
	    			'label' => t("title",1),
	    			'required' => true,
		    		'filters' => array('StringTrim', 'StripTags'),
	                'validators' => array(
	                               'NotEmpty',
	                               array('stringLength', false, array(0,300))
	                               ),
				),
    			//'permission' => 'update_task'
			),
				'by' => array(
						'type' => 'Text',
						'options' => array(
								'label' => t("author",1),
								'filters' => array('StringTrim', 'StripTags'),
								'validators' => array('NotEmpty'),
						),
						//'permission' => 'update_task'
				),
			'answer' => array(
						'type' => 'Text',
						'options' => array(
								'label' => t("answer",1),
								'required' => true,
								'filters' => array('StringTrim', 'StripTags'),
								'validators' => array('NotEmpty'),
						),
						//'permission' => 'update_task'
				),
				
			'url' => array(
	    		'type' => 'Text',
	    		'options' => array(
	    			'label' => t("url",3),
	    				'placeholder' => "E.g http://www.youtube.com/watch?v=fkm7eMqiuSw",
	    			'required' => false,
		    		'filters' => array('StringTrim', 'StripTags'),
	                'validators' => array('NotEmpty'),
	    				),
    		 ),
			'is_iphone_fun' => array(
					'type' => 'Hidden',
					'options' => array(
							'label' => t("iphone_chat",1),
					),
			        'defaultValue' => 1
					//'permission' => 'update_task'
			),
			'sender_name' =>array(
					'type' => 'Text',
					'options' => array(
							'label' => t("sender_name",1),
							'required' => false,
					),
			),
			'game_category'=>array( //cateogry for game
					'type' => 'Select',
					'options' => array(
							'label' => t("category",1),
							'required' => false,
					),
					'multiOptionsCallback' => array('getGameCategory'),
					),
	    	'content' => array(
	    		'type' => 'Textarea',
	    		'options' => array(
	    	        'label' => t("content",1),
	    	        'class' => 'isEditor',
		    		'filters' => array('StringTrim', 'StoryPost'),
	    			'prefixPath' => array(
	    				"filter" => array (
	    					"Filter" => "Filter/"
	    				)
	    			)
	    		),
	    	),
                'source' => array(
	    		'type' => 'Text',
	    		'options' => array(
	    			'label' => t("source",1),
	    			//'required' => true,		    		
	                //  'validators' => array('NotEmpty'),
				),
                        'defaultValue' => default_source_value(get_value('type'))
                    ),
	    	'type' => array(
	    		'type' => 'Hidden',
	    		'options' => array(
	    			'label' => t("type_of_story"),
	    			'required' => true,
		    		'filters' => array('StringTrim', 'StripTags'),
	                'validators' => array('NotEmpty'),
				),
	    		'defaultValue'=> 1
	    	),	
			'ytid' => array(
	    		'type' => 'Hidden',
	    		'options' => array(
	    			'label' => t('url',3),
	    			'required' => true,
		    		'filters' => array('StringTrim', 'StripTags'),
	                'validators' => array('NotEmpty'),
	    		),
	    			//'defaultValue'=>'none'
	    	),
			'status' => array(
				'type' => 'Select',
	        	'options' => array(
	        		'label' => t('status',1),
	        		'required' => true,
    			),
    			'permission' => 'admin_story',
	        	'multiOptionsCallback' => array('getStatus'),
	    	),
	    	'seo.title' => array(
	    		'type' => 'Text',
	    		'options' => array(
	    			'label' => t("SEO_title",1),
	    			'required' => false,
		    		'filters' => array('StringTrim', 'StripTags'),
	                'validators' => array('NotEmpty'),
	    		),
	    	    'permission'=>'admin_story'
	    	),
	    	'seo.desc' => array(
	    		'type' => 'Text',
	    		'options' => array(
	    			'label' => t("SEO_description",1),
	    			'required' => false,
		    		'filters' => array('StringTrim', 'StripTags'),
	                'validators' => array('NotEmpty'),
	    		),
	    		'permission'=>'admin_story'
	    	),
            'upload_img' => array(
                'type' => 'Hidden',
                'options' => array(
                    'multiple' => true
                )
            ),
            'upload_flash' => array(
                'type' => 'Hidden',
                'options' => array(
                    'multiple' => true
                )
            ),
            'images' => array(
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
            'flash' => array(
                'type' => 'Hidden',
                'options' => array(
                    'filters' => array('StringTrim', 'StripTags')
                )
            ),
			
            'tags' => $this->generateFormElement('tags', 'Tags','', array(
                    'data-url' => '/suggest.php?node=tag&addnew=1',
                    'permission' => 'new_tag'
            )),
		    
           'is_feature' => array(
                'type' => 'Hidden',
                'options' => array(
                    'filters' => array('StringTrim', 'StripTags')
                )
            ),
	        'counter' => array(
                	
                ),
		    'pair1' => array(
	    		'type' => 'Text',
	    		'options' => array(
	    	        'label' => t("pair_one",1),
		    		'filters' => array('StringTrim', 'StoryPost'),
	    			'prefixPath' => array(
	    				"filter" => array (
	    					"Filter" => "Filter/"
	    				)
	    			),
    		        'validators' => array (
    		                array('stringLength', false, array(5,50))
    		        )
	    		),
	    	),
	        'pair2' => array(
	                'type' => 'Text',
	                'options' => array(
	                        'label' => t("pair_two",1),
	                        'filters' => array('StringTrim', 'StoryPost'),
	                        'prefixPath' => array(
	                                "filter" => array (
	                                        "Filter" => "Filter/"
	                                )
	                        ),
	                        'validators' => array (
	                                array('stringLength', false, array(5,50))
	                        )
	                ),
	        ),
				
		);
	}
	/**TODO: hook here if needed
    public function customIsValid($data)
    {
    	return array('success' => false, 'err' => "If customIsValid exist. You must implement it");
    }
    */

}
