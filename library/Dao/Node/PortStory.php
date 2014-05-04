<?php
class Dao_Node_PortStory extends Dao_Node_Story
{
	public function portStory($data)
	{
		unset($data['_u']);
		$this->runBackground('importMysql', array($data));
	}
	
	public function importMysql($data)
	{
		$pdoParams = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8;');
		$dbname = $data['dbname'];
		$table = $data['table'];
		$username = $data['username'];
		$password = $data['password'];
		$options = array(
				'host' => 'localhost',
				'dbname' => $dbname,
				'username' => $username,
				'password' => $password,
				'unix_socket' => "/var/run/mysqld/mysqld.sock",
				'driver_options' => $pdoParams
		);
		$sqlDb = Zend_Db::factory("PDO_Mysql", $options);
		//DROP table first
		if ($table == 'users')
		{
			//$sql = "DELETE FROM users where lname!='root'";
			$where = array('lname' => array('$ne' => 'root'));
			Dao_User::getInstance()->delete($where);
			Dao_User::getInstance()->ensureIndex();
		}
		elseif ($table == 'news')
		{
			$where = array();
			$this->delete($where);
			$this->ensureIndex();
		}
		elseif ($table == 'node_tags')
		{
			//empty autosuggestions
			$redis = init_redis();
			$redis->flushDB();
			$where = array();
			//remove tags
			Dao_Node_Tag::getInstance()->delete($where);
			//ensureIndex			
			Dao_Node_Tag::getInstance()->ensureIndex();
		}
	
		$start = 0;
		$limit = 200;//might be the best batch number
		$end = $limit;
		$sql = "SELECT * FROM $table WHERE 1 LIMIT $start, $end";
		$ret = $sqlDb->fetchAll($sql);
		while(count($ret) > 0) {
			foreach($ret as $row) {
				if ($table == 'users')
				{
					$user = $this->importUser($row);
				}
				elseif ($table == 'node_tags')
				{
					$tag = $this->importTag($row);
				}
				elseif ($table == 'news')
				{
					$story = $this->importStory($row);
				}
				elseif ($table == 'comments')
				{
					$this->importComment($row);
				}
			}
			$start = $end;
			$end += $limit;
			$sql = "SELECT * FROM $table WHERE 1 LIMIT $start, $end";
			$ret = $sqlDb->fetchAll($sql);
		}
		$this->updateSiteIidToRedis();
		
	}
	
	public function importUser($row)
	{
		$same = array('birthday', 'mail', 'intro');
		foreach ($same as $key)
		{
			$data[$key] = isset($row[$key]) ? $row[$key] : '';
		}
		//transform the
		$data['iid'] = $row['id'];
		$data['lname'] = $row['name'];
		if (isset($row['realname']) && $row['realname'] != '')
			$data['name'] = $row['realname'] ;
		else 
			$data['name'] = $row['name'] ;
		
		$data['ts'] = $row['created'];
		$data['counter'] = array(
				'k' => isset($row['points']) ? $row['points']: 0,
				'p' => isset($row['posts']) ? $row['posts']: 0,
		);
		$data['roles'] = array('regular');
		//avatar
	
		$avatar = "/ufiles/" . $row['id'] . "/images/original/avatar.jpg";
		if (is_file(PUBLIC_PATH . $avatar))
			$data['avatar'] = $avatar;
	
		$r = Dao_User::getInstance()->insertUser($data);
		if ($r['success'])
			return $r['result'];
	}
	
	public function importTag($row)
	{
		if ($row['tag'] != '')
		{
			//transform the
			$data['slug'] = $row['tag'];
			$data['name'] = str_replace('-' , ' ', $row['tag']);
			$data['iid'] = $row['nid'];
			$data['counter'] = array('s' => $row['count']);
		
			Dao_Node_Tag::getInstance()->insertNode($data);
		}	
	}
	
	public function importStory($row)
	{
		$same = array('slug', 'promote');
		foreach ($same as $key)
		{
			$data[$key] = isset($row[$key]) ? $row[$key] : '';
		}
		//transform the
		$data['iid'] = $row['nid'];
		$type = $this->convertMysqlType($row['type']);
		$data['type'] = story_typeint($type);
		
		$data['name'] = $row['title'];
		$data['content'] = $row['body'];
		$data['ts'] = $row['created'];
	
		if ($row['status'] = 1)
			$data['status'] = 'approved';
		else
			$data['status'] = 'queued';
	
		
		if ($row['type'] == 'video')
		{
			// vid_type => 0 ~ youtube
			if ($row['vid_type'] == 0)
			{
				$data['url'] = "http://www.youtube.com/watch?v=" . $row['vid_id'];
				$data['ytid'] = $row['vid_id'];
				$data['yt_duration'] = $row['vid_duration'];
				$data['avatar'] = "http://img.youtube.com/vi/" . $row['vid_id'] . "/default.jpg";
			}
			else
				return; //skip all the non-youtube videos
		}
		elseif ($row['type'] == 'image')
		{
			$data['avatar'] = $row['thumb'];
			$data['images'] = array(
					array('url' => $row['image_src'])
			);
		}
		elseif ($row['type'] == 'flash')
		{
			$data['flash'] = $row['flash_src'];
			$data['avatar'] = $row['thumb'];  	//ustore
		}
		elseif ($row['type'] == 'quiz')
			$data['answer'] = $row['answer'];
		elseif ($row['type'] == 'joke')
		{
			$data['avatar'] = isset($row['thumb']) ? $row['thumb'] : '';
		}
		
		
		$userWhere = array('lname' => $row['uname']);
		
		$userDao = Dao_User::getInstance();
		$r = $userDao->findOne($userWhere);
		if ($r['success'])
		{
			$cache = $userDao->getCacheObject($r['result']);
			if ($cache['success'])
				$data['u'] = $cache['result'];
		}
		else
		{
			l("story id " . $row['nid'] . " user {$row['uname']} nout found");
			return;
		}
		
		$newTags = array();
		if (isset($row['tags']) && $row['tags'] != '')
		{
			//find out the tags
			
			$tags = explode(',', $row['tags']);
			foreach ($tags as $tag)
			{
				$r = Dao_Node_Tag::getInstance()->findOne(array('slug' => $tag));
				if ($r['success'])
				{
					$t = Dao_Node_Tag::getInstance()->getCacheObject($r['result']);
					if ($t['success'])
						$newTags[] = $t['result'];
				}
				else
				{
					l("tag $tag not found");
				}
			}
		}
		
		$data['tags'] = $newTags;
		$data['weight'] = $row['weight'];

		$data['counter'] = array(
				'v' => $row['views'],
				'p' => $row['vote_up'],
				'vd' => $row['vote_down'],
				'vote' => $row['vote_up'],
				//'hn' => ???
		);
		$r = $this->insertNode($data);
		if (!$r['success'])
		{
			l($r['err']);
		}
	}
	
	public function convertMysqlType($mysqlNodeType)
	{
		if ($mysqlNodeType == 'joke' || $mysqlNodeType == '')
			return 'story';
		elseif ($mysqlNodeType == 'flash')
			return 'flash-game';
		return $mysqlNodeType;
	}
}