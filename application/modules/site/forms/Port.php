<?php 
class Site_Form_Port extends Site_Form_New
{
    public function init()
    {
        parent::init();
        $this->fieldList = array('dbname','username', 'password' ,'table');
        //$this->setCbHelper('Question_Form_Helper');
    }
    //protected $_fieldListConfig; @see Cl_Dao_Foo
    public function setStep($step)
    {
        parent::setStep($step);
    }

    protected function _formFieldsConfigCallback()
    {
        return array(
                        'dbname' => $this->generateFormElement('name', "dbname", 'hoibinet'),
                        'table'=>array( //cateogry for game
							'type' => 'Select',
							'options' => array(
									'label' => 'Choose a table (You should import tables in this order)',
									'required' => false,
							),
							'multiOptions' => array(
									'users' => 'users',
									'node_tags' => 'node_tags', 
									'news' => 'stories', 
									'comments' => 'comments',
									'configs'=> 'configs',
							),
						),
        		
                        'username' => $this->generateFormElement('name', "db username", 'root'),
                        'password' => $this->generateFormElement('name', "db password", '123'),
        );
    }
}
