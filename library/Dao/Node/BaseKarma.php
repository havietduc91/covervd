<?php
/**
 * If you only care about last 7 days, last 30 days
 * then basekarma stores the karma at the 31 days ago
 * 
 * scheme of base karma 
 * Duchv (havietduc91@gmail.com)
 * **/
class Dao_Node_BaseKarma extends Cl_Dao_Node
{
	public $hotnessRanking = false;
    
    public $cSchema = array(
    		'uiid' => 'int',
    		'ts' => 'int',
    		'karma' => 'string',
    		//add other stuff u want
    );
    
	protected function _configs(){
    	return array(
    		'collectionName' => 'baseKarma',
        	'documentSchemaArray' => array(
        		'uiid' => 'int', // user iid 
        		'ts' => 'int', 
        		'karma' => 'float',//karma user at date		
        	),
    		'indexes' => array(
    					array(
    							array('uiid' => 1),
    							array("unique" => true, "dropDups" => true)
    					)
    			)
    	);
	}
	
	/**
	 * insert into snapshot array data include:
	 * 		- uiid	
	 * 		- date
	 * 		- karma
	 * 
	 * @see Cl_Dao_Node::beforeInsertNode()
	 */
	public function beforeInsertNode($data){
	    /**
	     * Lay du lieu trong 
	     */
	}
	
	/**
	 *	
	 * @see Cl_Dao_Node::afterInsertNode()
	 */
	public function afterInsertNode($data, $row){
	    
	}
	
	/**
	 * update  snapshot array data include:
	 * 		- uiid	
	 * 		- date
	 * 		- karma
	 * 
	 * @see Cl_Dao_Node::afterUpdateNode()
	 */
	public function afterUpdateNode($where, $data, $currentRow){
	    
	}
	
	/**
	 * delete snapshot array data include:
	 * 		- uiid	
	 * 		- date
	 * 		- karma
	 * 
	 * @see Cl_Dao_Node::afterDeleteNode()
	 */
	
	public function afterDeleteNode($row){
	    
	}
}
