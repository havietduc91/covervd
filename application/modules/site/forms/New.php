<?php
/**
 * Helper on generating common elements for all the form in site "edx"
 * @author steve
 *
 */
class Site_Form_New extends Cl_Form
{
    public function generateFormElement($type, $label = '', $defautValue = '', $options = array())
    {
        if ($type == 'dynamic_array')
        {
            //$options should have 'data-splitter' => '---'
            $splitter = isset($options['data-splitter']) ? $options['data-splitter'] : '---'; 
            $ret = array(
            		'type' => 'Hidden',
            		'options' => array(
            				'label' => $label,
            		        'class' => 'dynamic-array',
            				//'required' => true,
            		        'data-splitter' => $splitter,
            				'filters' => array('StringTrim'),
                            'transformers' => array('splitStringToArray'),            		                
            				//'validators' => array('NotEmpty'), // can be empty
            		),
            		//'multiOptions' => schools_im_teaching()
            );
        }
        elseif ($type == 'schools_im_teaching')
        {
            $ret = array(
            		'type' => 'Select',
            		'options' => array(
            				'label' => t('choose_a_school', 1),
            				'required' => true,
            				'filters' => array('StringTrim', 'StripTags'),
            				//'validators' => array('NotEmpty'), // can be empty
            		),
            		'multiOptions' => schools_im_teaching()
            );
        }
        elseif ($type == 'school')
        {
        	$ret = 
            	array(
            			'type' => 'Select',
            			'options' => array(
            					'label' => $label,
            					//'required' => true,
            					'filters' => array('StringTrim', 'StripTags'),
            					//'validators' => array('NotEmpty'), // can be empty
            			),
            	        'multiOptionsCallback' => array('getAllSchoolList') 
            	);
        }
        
        elseif ($type == 'concepts')
        {
            $ret = 
                array(
                		'type' => 'Text',
                		'options' => array(
                				'label' => $label,
                				//'required' => true,
                				'filters' => array('StringTrim', 'StripTags'),
                				'class' => 'conceptTokens',
                				//'validators' => array('NotEmpty'), // can be empty
                				'transformers' => array("tokensJSONStringToArray")
                		),
                        'permission' => 'new_concept',                                
                );
        }
        elseif ($type == 'name')
        {
            $ret =
                array(
                		'type' => 'Text',
                		'options' => array(
                				'label' => $label,
                				'required' => true,
                				'filters' => array('StringTrim', 'StripTags'),
                				'validators' => array('NotEmpty'),
                		),
                        'defaultValue' => $defautValue
                		//'permission' => 'update_task'
                );
        }
        elseif ($type == 'status')
        {
            $ret = 
                array(
                		'type' => 'Select',
                		'options' => array(
                				'label' => 'Status',
                				'required' => true,
                		),
                		'multiOptionsCallback' => array('getStatus'),
                );
        }  
        elseif ($type == 'content')
        {
            $ret = 
                array(
            		'type' => 'Textarea',
            		'options' => array(
            				'label' => $label,
            				'class' => 'isEditor',
            				'filters' => array('StringTrim', 'StoryPost'),
            				'prefixPath' => array(
            						"filter" => array (
            								"Filter" => "Filter/"
            						)
            				)
            		),
                );
        }
        elseif ($type == 'bounty')
        {
            //$bounties = bounty_config();
            if ($label == '')
                $label = t('bounty');
            $ret =
                array(
                		'type' => 'Hidden',
                		'options' => array(
                				'label' => $label,
                				'filters' => array('Int'/*'StringTrim', 'StripTags'*/)
                		),
                		//'multiOptions' => $bounties
                );
        }
        elseif ($type == 'bounty_type')
        {
        	if ($label == '')
        		$label = t('money_type');
        	$ret =
        	array(
        			'type' => 'Select',
        			'options' => array(
        					'label' => $label,
        					'filters' => array('StringTrim', 'StripTags')
        			),
        			'multiOptions' => array('money' => t('real_money'), 'vmoney' => t('virtual_money'))
        	);
        }
        elseif ($type == 'youtube_url')
        {
            if ($label == '')
            	$label = t('youtube_url');
            $ret =array(
                    'type' => 'Text',
                    'options' => array(
                                    'label' => $label,
                                    'filters' => array('StringTrim', 'StripTags'),
                                    'validators' => array('NotEmpty'),
                                    'style' => 'width:80%',
                                    'class' => 'youtube',
                                    'placeholder' => 'http://www.youtube.com/watch?v=5NZPrwgYe60'
                    ),
            );
        }
        else
            $ret = parent::generateFormElement($type, $label, $defautValue, $options);
        
        if (count($options) > 0)
            $ret['options'] = array_merge($ret['options'], $options);
        $ret['defaultValue'] = $defautValue;
        return $ret;
    }
}