<?php

class Event extends DbObject
{
	protected $id;
	protected $projectID;
	protected $eventTypeID;
	protected $user1ID;
	protected $user2ID;
	protected $item1ID;
	protected $item2ID;
	protected $item3ID;
	protected $data1;
	protected $data2;
	protected $data3;
	protected $dateCreated;

	const DB_TABLE = 'event';
	
	public function __construct($args=array())
	{
		$defaultArgs = array(
			'id' => null,
			'project_id' => null,
			'event_type_id' => 0,
			'user_1_id' => 0,
			'user_2_id' => null,
			'item_1_id' => null,
			'item_2_id' => null,
			'item_3_id' => null,
			'data_1' => null,
			'data_2' => null,
			'data_3' => null,
			'date_created' => null
		);	
		
		$args += $defaultArgs;
		
		$this->id = $args['id'];
		$this->projectID = $args['project_id'];
		$this->eventTypeID = $args['event_type_id'];
		$this->user1ID = $args['user_1_id'];
		$this->user2ID = $args['user_2_id'];
		$this->item1ID = $args['item_1_id'];
		$this->item2ID = $args['item_2_id'];		
		$this->item3ID = $args['item_3_id'];
		$this->data1 = $args['data_1'];
		$this->data2 = $args['data_2'];
		$this->data3 = $args['data_3'];
		$this->dateCreated = $args['date_created'];		
	}

	public static function load($id)
	{
		$db = Db::instance();
		$obj = $db->fetch($id, __CLASS__, self::DB_TABLE);
		return $obj;
	}	
	
	public function save()
	{
		$db = Db::instance();
		// map database fields to class properties; omit id and dateCreated
		$db_properties = array(
			'project_id' => $this->projectID,
			'event_type_id' => $this->eventTypeID,
			'user_1_id' => $this->user1ID,
			'user_2_id' => $this->user2ID,
			'item_1_id' => $this->item1ID,
			'item_2_id' => $this->item2ID,		
			'item_3_id' => $this->item3ID,
			'data_1' => $this->data1,
			'data_2' => $this->data2,
			'data_3' => $this->data3
		);		
		$db->store($this, __CLASS__, self::DB_TABLE, $db_properties);
		
		// // was this a contribution?
		// $et = EventType::load($this->eventTypeID);
		// if($et->getContribution() == true) {
			// // only project-related events can be contributions
			// if($this->projectID != null) {
				// $pu = ProjectUser::find($this->user1ID, $this->projectID);
				// if($pu != null) {
					// if($pu->getRelationship() == ProjectUser::FOLLOWER) {
						// // only followers can be upgraded to contributors
						// $pu->setRelationship(ProjectUser::CONTRIBUTOR);
						// $pu->save();
					// }
				// } else {
					// // no pre-existing relationship to project, so create new ProjectUser
					// $pu = new ProjectUser(array(
						// 'user_id' => $this->user1ID,
						// 'project_id' => $this->projectID,
						// 'relationship' => ProjectUser::CONTRIBUTOR
					// ));
					// $pu->save();
				// }
			// }
		// }
	}
	
	/* ------------------------------------------------------------------*/
	
	public static function getSparklineData($projectID=null, $sectionID=null, $days=7)
	{
		if($projectID == null) return null;
		
		$m = date("m");
		$de = date("d");
		$y = date("Y");
		
		$data = array();
		for($i=($days-1); $i>=0; $i--) {
			$date = date("Y-m-d", mktime(0,0,0,$m,($de-$i),$y));
			$query = " SELECT e.id FROM ".self::DB_TABLE." e";
			if($sectionID != null)
			{
				$query .= " INNER JOIN event_type et ON";
				$query .= " e.event_type_id = et.id";
			}			
			$query .= " WHERE e.project_id = ".$projectID;
			$query .= " AND DATE(e.date_created) = '".$date."'";
			if($sectionID != null)
				$query .= " AND et.group = ".$sectionID;
			//echo $query."<br>";
			
			$db = Db::instance();
			$result = $db->lookup($query);
			$data[] = mysql_num_rows($result);
		}
		return $data;
	}
	
	/* ------------------------------------------------------------------*/
	
	// public static function isContributor($userID=null, $projectID=null)
	// {
		// if( ($userID == null) || ($projectID == null) ) return null;
		
		// $project = Project::load($projectID);
		// $creatorID = $project->getCreatorID();
		// if($userID == $creatorID)
			// return false; // can't be creator
		
		// $query = "SELECT id FROM ".self::DB_TABLE." e";
		// $query .= " INNER JOIN ".EventType::DB_TABLE." et ON ";
		// $query .= " e.event_type_id = et.id";		
		// $query .= " WHERE e.project_id = ".$projectID;
		// $query .= " AND e.user_1_id = ".$userID;
		// $query .= " AND et.contribution = 1";
		// //$query .= " AND user_1_id NOT IN ("; // can't be organizer
		// //	$query .= "SELECT user_id FROM ".ProjectUser::DB_TABLE;
		// //	$query .= " WHERE project_id = ".$projectID;
		// //$query .= ")";

		// $db = Db::instance();
		// $result = $db->lookup($query);
		// if(!mysql_num_rows($result))
			// return false;
		// else
			// return true;
	// }
	
	// public static function getContributorsByProjectID($projectID=null)
	// {
		// if($projectID == null) return null;
		
		// $project = Project::load($projectID);
		// $creatorID = $project->getCreatorID();
		
		// $query = "SELECT DISTINCT e.user_1_id AS user_id FROM ".self::DB_TABLE." e";
		// $query .= " INNER JOIN ".User::DB_TABLE." u ON";
		// $query .= " e.user_1_id = u.id";
		// $query .= " WHERE e.project_id = ".$projectID;
		// $query .= " AND e.user_1_id NOT IN ("; // leave out organizers
			// $query .= "SELECT user_id FROM ".ProjectUser::DB_TABLE;
			// $query .= " WHERE project_id = ".$projectID;
			// $query .= " AND (relationship =".ProjectUser::BANNED.")";
			// $query .= " OR (relationship =".ProjectUser::ORGANIZER.")";
		// $query .= ")";
		// $query .= " AND e.user_1_id != ".$creatorID; // leave out creator
		// $query .= " ORDER BY u.username ASC"; // alphabetical

		// $db = Db::instance();
		// $result = $db->lookup($query);
		// if(!mysql_num_rows($result)) return array();

		// $events = array();
		// while($row = mysql_fetch_assoc($result))
			// $events[$row['user_id']] = User::load($row['user_id']);
		// return $events;
	// }
	
	//----------------------------------------------------------------------------//
	
	public static function getDashboardEvents($userID=null, $limit=null) {
		if($userID === null) return null;
		
		$query = "SELECT DISTINCT e.id AS id FROM ".self::DB_TABLE." e";
		$query .= " INNER JOIN ".EventType::DB_TABLE." et ON ";
		$query .= " e.event_type_id = et.id";
		$query .= " WHERE (e.project_id IN (";
		// follower or organizer
			$query .= " SELECT DISTINCT project_id FROM ".ProjectUser::DB_TABLE;
			$query .= " WHERE user_id = ".$userID;
			$query .= " AND relationship != ".ProjectUser::BANNED;
		$query .= " )";
		// creator
		$query .= " OR e.project_id IN (";
			$query .= " SELECT DISTINCT id FROM ".Project::DB_TABLE;
			$query .= " WHERE creator_id = ".$userID;
		$query .= " )";
		// contributor
		$query .= " OR e.project_id IN (";
			$query .= " SELECT DISTINCT project_id FROM ".self::DB_TABLE;
			$query .= " WHERE user_1_id = ".$userID;
			$query .= " OR user_2_id = ".$userID;
		$query .= " ))";
		// not banned
		$query .= " AND e.project_id NOT IN (";
			$query .= " SELECT DISTINCT project_id FROM ".ProjectUser::DB_TABLE;
			$query .= " WHERE user_id = ".$userID;
			$query .= " AND relationship = ".ProjectUser::BANNED;
		$query .= " )";
		$query .= " AND et.hidden = 0";
		$query .= " ORDER BY e.date_created DESC";
		if($limit != null)
			$query .= " LIMIT ".$limit;
		//echo $query;
		
		$db = Db::instance();
		$result = $db->lookup($query);
		if(!mysql_num_rows($result)) return array();

		$events = array();
		while($row = mysql_fetch_assoc($result))
			$events[$row['id']] = self::load($row['id']);
		return $events;	
	}
	
	public static function getBasicsEventsByProjectID($projectID=null, $limit=null)
	{
		return (self::getByProjectID($projectID, BASICS_ID, $limit));
	}
	
	public static function getTasksEventsByProjectID($projectID=null, $limit=null)
	{
		return (self::getByProjectID($projectID, TASKS_ID, $limit));
	}
	
	public static function getTaskEvents($taskID=null, $limit=null) {
		if($taskID == null) return null;
		
		$task = Task::load($taskID);
		
		$query = "SELECT e.id AS id FROM ".self::DB_TABLE." e";
		$query .= " INNER JOIN ".EventType::DB_TABLE." et ON ";
		$query .= " e.event_type_id = et.id";		
		$query .= " WHERE (e.item_1_id = ".$taskID;
		$query .= " AND e.event_type_id IN (";
			$query .= " SELECT id FROM ".EventType::DB_TABLE;
			$query .= " WHERE `group` = ".TASKS_ID;
		$query .= " ))";
		$query .= " OR (e.item_2_id = ".$taskID;
		$query .= " AND e.event_type_id = 'create_task_comment')";
		$query .= " OR (e.item_2_id = ".$taskID;
		$query .= " AND e.event_type_id = 'create_update_comment')";
		$query .= " OR (e.item_2_id = ".$taskID;
		$query .= " AND e.event_type_id = 'accept_task')";			
		$query .= " OR (e.item_2_id = ".$taskID;
		$query .= " AND e.event_type_id = 'edit_accepted_status')";	
		$query .= " OR (e.item_3_id = ".$taskID;
		$query .= " AND e.event_type_id = 'create_task_comment_reply')";
		$query .= " OR (e.item_3_id = ".$taskID;
		$query .= " AND e.event_type_id = 'create_update_comment_reply')";
		$query .= " OR (e.item_3_id = ".$taskID;
		$query .= " AND e.event_type_id = 'edit_update_title')";
		$query .= " OR (e.item_3_id = ".$taskID;
		$query .= " AND e.event_type_id = 'edit_update_message')";		
		$query .= " OR (e.item_3_id = ".$taskID;
		$query .= " AND e.event_type_id = 'create_update')";
		$query .= " AND et.hidden = 0";		
		$query .= " ORDER BY e.date_created DESC";
		if($limit != null)
			$query .= " LIMIT ".$limit;	
			
		$db = Db::instance();
		$result = $db->lookup($query);
		if(!mysql_num_rows($result)) return array();

		$events = array();
		while($row = mysql_fetch_assoc($result))
			$events[$row['id']] = self::load($row['id']);
		return $events;				
	}
	
	public static function getUpdateEvents($updateID=null, $limit=null) {
		if($updateID == null) return null;
		
		$update = Update::load($updateID);
		
		$query = "SELECT e.id AS id FROM ".self::DB_TABLE." e";
		$query .= " INNER JOIN ".EventType::DB_TABLE." et ON ";
		$query .= " e.event_type_id = et.id";			
		$query .= " WHERE (e.item_1_id = ".$updateID;
		$query .= " AND e.event_type_id = 'create_update')";
		$query .= " OR (e.item_1_id = ".$updateID;
		$query .= " AND e.event_type_id = 'edit_update_title')";
		$query .= " OR (e.item_1_id = ".$updateID;
		$query .= " AND e.event_type_id = 'edit_update_message')";		
		$query .= " OR (e.item_2_id = ".$updateID;
		$query .= " AND e.event_type_id = 'create_update_comment')";
		$query .= " OR (e.item_3_id = ".$updateID;
		$query .= " AND e.event_type_id = 'create_update_comment_reply')";
		$query .= " AND et.hidden = 0";
		$query .= " ORDER BY date_created DESC";
		if($limit != null)
			$query .= " LIMIT ".$limit;	
			
		$db = Db::instance();
		$result = $db->lookup($query);
		if(!mysql_num_rows($result)) return array();

		$events = array();
		while($row = mysql_fetch_assoc($result))
			$events[$row['id']] = self::load($row['id']);
		return $events;		
	}

	public static function getDiscussionsEventsByProjectID($projectID=null, $limit=null)
	{
		return (self::getByProjectID($projectID, DISCUSSIONS_ID, $limit));
	}
	
	public static function getDiscussionEvents($discussionID=null, $limit=null)
	{
		if($discussionID == null) return null;
		
		$discussion = Discussion::load($discussionID);
		$projectID = $discussion->getProjectID();
		
		$query = "SELECT e.id AS id FROM ".self::DB_TABLE." e";
		$query .= " INNER JOIN ".EventType::DB_TABLE." et ON ";
		$query .= " e.event_type_id = et.id";			
		$query .= " WHERE e.project_id = ".$projectID;
		$query .= " AND ( e.item_1_id = ".$discussionID." AND e.event_type_id = 'create_discussion' )";
		$query .= " OR ( e.item_2_id = ".$discussionID." AND e.event_type_id = 'create_discussion_reply' )";	
		$query .= " AND et.hidden = 0";		
		$query .= " ORDER BY e.date_created DESC";
		if($limit != null)
			$query .= " LIMIT ".$limit;
		//echo $query;
			
		$db = Db::instance();
		$result = $db->lookup($query);
		if(!mysql_num_rows($result)) return array();

		$events = array();
		while($row = mysql_fetch_assoc($result))
			$events[$row['id']] = self::load($row['id']);
		return $events;			
	}	

	public static function getPeopleEventsByProjectID($projectID=null, $limit=null)
	{
		return (self::getByProjectID($projectID, PEOPLE_ID, $limit));
	}	
	
	public static function getUserEvents($userID=null, $limit=null) {
		if($userID == null) return null;
		
		$query = "SELECT e.id AS id FROM ".self::DB_TABLE." e";
		$query .= " INNER JOIN ".EventType::DB_TABLE." et ON ";
		$query .= " e.event_type_id = et.id";		
		$query .= " WHERE e.user_1_id = ".$userID;
		$query .= " AND et.hidden = 0";
		$query .= " ORDER BY e.date_created DESC";
		if($limit != null)
			$query .= " LIMIT ".$limit;
		//echo $query;
			
		$db = Db::instance();
		$result = $db->lookup($query);
		if(!mysql_num_rows($result)) return array();

		$events = array();
		while($row = mysql_fetch_assoc($result))
			$events[$row['id']] = self::load($row['id']);
		return $events;	
	}	
	
	public static function getByProjectID($projectID=null, $eventTypeGroup=null, $limit=null)
	{
		if($projectID == null) return null;
		
		$query = "SELECT e.id AS id FROM ".self::DB_TABLE." e";
		$query .= " INNER JOIN ".EventType::DB_TABLE." et ON";
		$query .= " e.event_type_id = et.id";
		$query .= " WHERE e.project_id = ".$projectID;
		if($eventTypeGroup != null)
			$query .= " AND et.group = ".$eventTypeGroup;
		$query .= " AND et.hidden = 0";
		$query .= " ORDER BY e.date_created DESC";
		if($limit != null)
			$query .= " LIMIT ".$limit;
		//echo $query;
		
		$db = Db::instance();
		$result = $db->lookup($query);
		if(!mysql_num_rows($result)) return array();

		$events = array();
		while($row = mysql_fetch_assoc($result))
			$events[$row['id']] = self::load($row['id']);
		return $events;
	}
	
	public function getCssClass()
	{
		$et = EventType::load($this->getEventTypeID());
		$cssClass = $et->getCssClass();
		return $cssClass;
	}
	
	// --- only getters and setters below here --- //	
	
	public function getID()
	{
		return ($this->id);
	}
	
	public function setID($newID)
	{
		$this->id = $newID;
		$this->modified = true;
	}
	
	public function getEventTypeID()
	{
		return ($this->eventTypeID);
	}
	
	public function setEventTypeID($newEventTypeID)
	{
		$this->eventTypeID = $newEventTypeID;
		$this->modified = true;
	}
	
	public function getProjectID()
	{
		return ($this->projectID);
	}
	
	public function setProjectID($newProjectID)
	{
		$this->projectID = $newProjectID;
		$this->modified = true;
	}
	
	public function getCreatorID()
	{
		return ($this->getUser1ID());
	}
	
	public function getUser1ID()
	{
		return ($this->user1ID);
	}
	
	public function setUser1ID($newUser1ID)
	{
		$this->user1ID = $newUser1ID;
		$this->modified = true;
	}
	
	public function getUser2ID()
	{
		return ($this->user2ID);
	}
	
	public function setUser2ID($newUser2ID)
	{
		$this->user2ID = $newUser2ID;
		$this->modified = true;
	}
	
	public function getItem1ID()
	{
		return ($this->item1ID);
	}
	
	public function setItem1ID($newItem1ID)
	{
		$this->item1ID = $newItem1ID;
		$this->modified = true;
	}
	
	public function getItem2ID()
	{
		return ($this->item2ID);
	}
	
	public function setItem2ID($newItem2ID)
	{
		$this->item2ID = $newItem2ID;
		$this->modified = true;
	}
	
	public function getItem3ID()
	{
		return ($this->item3ID);
	}
	
	public function setItem3ID($newItem3ID)
	{
		$this->item3ID = $newItem3ID;
		$this->modified = true;
	}
	
	public function getData1()
	{
		return ($this->data1);
	}
	
	public function setData1($newData1)
	{
		$this->data1 = $newData1;
		$this->modified = true;
	}
	
	public function getData2()
	{
		return ($this->data2);
	}
	
	public function setData2($newData2)
	{
		$this->data2 = $newData2;
		$this->modified = true;
	}
	
	public function getData3()
	{
		return ($this->data3);
	}
	
	public function setData3($newData3)
	{
		$this->data3 = $newData3;
		$this->modified = true;
	}

	public function getDateCreated()
	{
		return ($this->dateCreated);
	}
	
	public function setDateCreated($newDateCreated)
	{
		$this->dateCreated = $newDateCreated;
		$this->modified = true;
	}

}