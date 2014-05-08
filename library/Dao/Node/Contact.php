<?php
class Dao_Node_Contact extends Cl_Dao_Node
{
    public $nodeType = 'contact';
    public $cSchema = array(
    		'id' => 'string',
    		'name' => 'string',
    		'avatar' => 'string',
    		//add other stuff u want
    );
        
    protected function relationConfigs($subject = 'user')
    {
    	if ($subject == 'user')
    	{
    		return array(
    				'1' => '1', //vote | like
    				'2' => '2', //follow
    				'3' => '3' , //flag as spam
    		);
    	}
    }
    
	protected function _configs(){
    	return array(
    		'collectionName' => 'contact',
        	'documentSchemaArray' => array(
        		'name' => 'string', 
        		'phone' => 'string',
        		'info' => 'string',
    	        'email' => 'string', 
        		'ts' => 'int',
        		'status' => 'string', //queued, approved
        		'company' => 'string',
        		'city' => 'string',
        	)
    	);
	}
	
    /**
     * Add new node
     * @param post data Array $data
     */
	public function beforeInsertNode($data)
	{
        return array('success' => true, 'result' => $data);
	}
	
	public function afterInsertNode($data, $row)
	{
        return array('success'=> true, 'result' => $row);
	}
	
    /******************************UPDATE****************************/
    public function beforeUpdateNode($where, $data, $currentRow)
    {
        /*
         * You have $data['$set']['_cl_step'] and $data['$set']['_u'] available
         */
        return array('success' => true, 'result' => $data);
    }
    
	public function afterUpdateNode($where, $data, $currentRow)
    {
        return array('success' => true, 'result' => $data);    
    }   
     
	/******************************INSERT_COMMENT****************************/
	
	/**
	 * Delete a single node by its Id
	 * @param MongoID $nodeId
	 */
	public function afterDeleteNode($row)
	{
	    //delete all comments
	    $commentDao = Site_Codenamex_Dao_Comment_Contact::getInstance();
	    $where = array('node.id' => $row['id']);
	    $commentDao->delete($where);
	    
	    return array('success' => true, 'result' => $row);
	}
	
	
	/**
	 * Prepare data for new node insert
	 * @param Array $dataArray
	 */
	public function prepareFormDataForDaoInsert($dataArray = array(), $formName = "Contact_Form_New")
	{
		return $dataArray;
	}	
	
	public function prepareCommentFormDataForDao($dataArray = array())
	{
		return $dataArray;
	}	

	/******************************RELATION*********************************/
	public function beforeInsertRelation($data)
	{
		return array('success' => true, 'result' => $data);
	}
	public function afterInsertRelation($data, $newRelations, $currentRow)
	{
		return array('success' => true, 'result' => $data);
	}
	public function afterDeleteRelation($currentRow, $rt, $newRelations = array())
	{
	    return array('success' => true);
	}
	
	public function filterNewObjectForAjax($obj, $formData)
	{
		return array('id' => $obj['id'] /*, 'slug' => $obj['slug'] */);
	}
	
	public function filterUpdatedObjectForAjax($currentRow, $step, $data, $returnedData)
	{
		$ret = array('id' => $currentRow['id']);
		return $ret;
	}
	
}
