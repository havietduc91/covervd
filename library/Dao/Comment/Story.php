<?php
class Dao_Comment_Story extends Cl_Dao_Comment
{
	protected function relationConfigs($subject = 'user')
	{
		if ($subject == 'user')
		{
			return array(
					'1' => '1', //vote|likes
			);
		}
	}

	protected function _configs()
	{
		return array(
				'collectionName' => 'comments_story',
				'documentSchemaArray' => array(
						'content'	=>	'string',
						'content_uf' => "string", //unfiltered content
						'name' => "string", //sort of title for the comment. A substr of fcontent
						'node' => $this->cSchema,
						'pid' => 'string', //parent comment id
						'u'	=>	$this->cSchema, //user who posted this
						'ts'	=>	'int',
						'attachments' => array(
								array(
										'u' => $this->cSchema,
										'id' => 'string',
										'ext' => 'string',
										'path' => 'string',
										'name' => 'string'
								)
						),
						'counter' => array(
								'vote' => 'int',
								'tw'   => 'double', // total weight of comment tree.
						),
						'status' => 'string', // approved|queued
						'is_spam' => 'int', // 0 | 1
				));
	}

	protected  $nodeType;
	public function init($options = null)
	{
		$this->nodeType = 'story';
		$this->nodeDaoClass = "Dao_Node_Story";
		parent::init();
	}

	/**
	 * @see Cl_Dao_Abstract::afterInsertRelation
	 *
	 * @param unknown_type $data
	 * @param unknown_type $newRelations
	 * @param unknown_type $currentRow
	 * @return multitype:boolean unknown
	 */
	public function afterInsertRelation($data, $newRelations, $currentRow)
	{
		//increase vote counter
		//TODO: update karma, hotness... like Dao_Node_Story
		 
		// $where = array('id' => $currentRow['o']['id']);
		if ($data['r']['rt'] == 1 )//vote
		{
			$comment=Dao_Comment_Story::getInstance()->findOne(array('id' => $currentRow['o']['id']));
			
			$dir = realpath(APPLICATION_PATH  . '/../public/' . CODENAME . '/cache/');//add folder caches
			$filename = $dir . "/story/" . $comment['result']['node']['id'];
			if(file_exists($filename)){
				unlink($filename);
			}
			
			$where = array('id'=>$comment['result']['id']);
			$dataUpdate= array('$inc'=>array('counter.vote'=>1));
			$this->update($where, $dataUpdate);
			$this->updateCommentTreeWeight($comment['result'], 'comment_up');
			//TODO: update user karma, hotness ranking...

			$where = array('id' => $data['o']['u']['id']);
			$update = array('$inc' => array('counter.vote' => 1,
					'counter.k' => calculate_karma(Zend_Registry::get('user'), 'comment_up', $data['o']['u']),
			));
			Cl_Dao_Util::getUserDao()->update($where, $update);
				
		}

		//cache comment to Node
		$order= array('counter.tw'=>-1 , 'ts'=>-1);
		Dao_Node_Story::getInstance()->cacheCommentsToNode($data['o']['node']['id'], $order);

		return array('success' => true, 'result' => $data);
	}

	public function afterDeleteRelation($currentRow, $rt, $newRelations = array()){
		$comment=Dao_Comment_Story::getInstance()->findOne(array('id' => $currentRow['o']['id']));

		$dir = realpath(APPLICATION_PATH  . '/../public/' . CODENAME . '/cache/');//add folder caches
		$filename = $dir . "/story/" . $comment['result']['node']['id'];
		if(file_exists($filename)){
			unlink($filename);
		}
		
		$where = array('id'=>$comment['result']['id']);
		$dataUpdate= array('$inc'=>array('counter.vote'=>-1));
		$this->update($where, $dataUpdate);

		$this->updateCommentTreeWeight($comment['result'], 'comment_down');
		 
		//get commentor
		$where = array('id' => $comment['result']['u']['id']);
		//update user karma
		$update = array('$inc' => array('counter.vote' => 1,
				'counter.k' => calculate_karma(Zend_Registry::get('user'), 'comment_down', $comment['result']['u']),
		));
		Cl_Dao_Util::getUserDao()->update($where, $update);
		 
		//cache comment to Node
		$order= array('counter.tw'=>-1 , 'ts'=>-1);
		Dao_Node_Story::getInstance()->cacheCommentsToNode($comment['result']['node']['id'], $order);
		 
		return array('success' => true , 'return'=>$currentRow);


	}
	// update Tree Weight of Parent Comment

	public function updateCommentTreeWeight($comment,$option){
		//update itself first
		$where = array('id'=>$comment['id']);
		if ($option=='comment_up'){
			$dataUpdate= array('$inc'=>array(//'counter.vote'=>1,
					'counter.tw'=>calculate_node_point(Zend_Registry::get('user'), $option)));
		}
		else{
			$dataUpdate= array('$inc'=>array(//'counter.vote'=>-1,
					'counter.tw'=>calculate_node_point(Zend_Registry::get('user'), $option)));
		}
		$this->update($where, $dataUpdate);

		//call parent
		if(!empty($comment['pid'])){
			$r=Dao_Comment_Story::getInstance()->findOne(array('id'=>$comment['pid']));
			 
			$this->updateCommentTreeWeight($r['result'],$option);
		}
		return array('success'=>true);
		 
	}
}