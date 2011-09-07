<?php

class User extends DbObject
{
	protected $id;
	protected $username;
	protected $password;
	protected $email;
	protected $name;
	protected $dob;
	protected $sex;
	protected $location;
	protected $biography;
	protected $picture;
	protected $pictureSmall;
	protected $pictureLarge;
	protected $notifyCommentTaskLeading;
	protected $notifyEditTaskAccepted;
	protected $notifyCommentTaskAccepted;
	protected $notifyCommentTaskUpdate;
	protected $notifyFollowProject;
	protected $notifyOrganizeProject;
	protected $notifyBannedProject;
	protected $notifyDiscussionReply;
	protected $notifyMakeTaskLeader;
	protected $admin;
	protected $dateCreated;
	protected $lastLogin;
	
	const DB_TABLE = 'user';
	
	public function __construct($args = array())
	{
		$defaultArgs = array(
			'id' => null,
			'username' => '',
			'password' => '',
			'email' => '',
			'name' => null,
			'dob' => null,			
			'sex' => null,
			'location' => null,
			'biography' => null,
			'picture' => '',
			'picture_small' => '',
			'picture_large' => '',
			'notify_comment_task_leading' => 1,
			'notify_edit_task_accepted' => 1,
			'notify_comment_task_accepted' => 1,
			'notify_comment_task_update' => 1,
			'notify_follow_project' => 1,
			'notify_organize_project' => 1,
			'notify_banned_project' => 1,
			'notify_discussion_reply' => 1,
			'notify_make_task_leader' => 1,
			'admin' => 0,
			'date_created' => null,
			'last_login' => null
		);
		
		$args += $defaultArgs; // combine the arrays
		
		$this->id = $args['id'];
		$this->username = $args['username'];
		$this->password = $args['password'];
		$this->email = $args['email'];
		$this->name = $args['name'];
		$this->dob = $args['dob'];		
		$this->sex = $args['sex'];
		$this->location = $args['location'];
		$this->biography = $args['biography'];
		$this->picture = $args['picture'];
		$this->pictureSmall = $args['picture_small'];
		$this->pictureLarge = $args['picture_large'];
		$this->notifyCommentTaskLeading = $args['notify_comment_task_leading'];
		$this->notifyEditTaskAccepted = $args['notify_edit_task_accepted'];
		$this->notifyCommentTaskAccepted = $args['notify_comment_task_accepted'];
		$this->notifyCommentTaskUpdate = $args['notify_comment_task_update'];
		$this->notifyFollowProject = $args['notify_follow_project'];
		$this->notifyOrganizeProject = $args['notify_organize_project'];
		$this->notifyBannedProject = $args['notify_banned_project'];
		$this->notifyDiscussionReply = $args['notify_discussion_reply'];
		$this->notifyMakeTaskLeader = $args['notify_make_task_leader'];
		$this->admin = $args['admin'];
		$this->dateCreated = $args['date_created'];
		$this->lastLogin = $args['last_login'];
	}
	
	public static function load($id)
	{
		$db = Db::instance();
		$obj = $db->fetch($id, __CLASS__, self::DB_TABLE);
		return $obj;
	}
	
	public static function loadByUsername($username)
	{
		$db = Db::instance();
			
		$query = sprintf("SELECT * FROM user WHERE username = '%s'",
				$username
			);
		$result = $db->lookup($query);
		
		if(!mysql_num_rows($result))
		{
			return null;
		}
		else
		{
			$row = mysql_fetch_assoc($result);
			$obj = new User($row);
			ObjectCache::set('User', $row['id'], $obj);
			return $obj;
		}		
	}
	
	public static function loadByEmail($email)
	{
		$db = Db::instance();
			
		$query = sprintf("SELECT * FROM user WHERE email = '%s'",
				$email
			);
		$result = $db->lookup($query);
		
		if(!mysql_num_rows($result))
		{
			return null;
		}
		else
		{
			$row = mysql_fetch_assoc($result);
			$obj = new User($row);
			ObjectCache::set('User', $row['id'], $obj);
			return $obj;
		}		
	}	
	
	public static function getUnaffiliatedUsernames($projectID=null) {
		if($projectID === null) return null;
		$project = Project::load($projectID);
		$creatorID = $project->getCreatorID();
		
		$query = "SELECT username FROM ".User::DB_TABLE;	
		// project users
		$query .= " WHERE id NOT IN (";
			$query .= " SELECT user_id FROM ".ProjectUser::DB_TABLE;
			$query .= " WHERE project_id = ".$projectID;
		$query .= ")";
		// project creator
		$query .= " AND id != ".$creatorID;
		$query .= " ORDER BY username ASC";
		
		$db = Db::instance();
		$result = $db->lookup($query);
		
		if(!mysql_num_rows($result))
			return array();
		
		$usernames = array();
		while($row = mysql_fetch_assoc($result))
			$usernames[] = $row['username'];
		return $usernames;			
	}	
	
	// public static function findUsers($username = null, $limit = null)
	// {
		// if ($username == null) return null;
		
		// $db = Db::instance();
			
		// $query = sprintf("SELECT * FROM user WHERE user_name like '%s'",
				// $username."%"
			// );
		// if($limit != null)
			// $query .= " LIMIT " . $limit;
			
		// $result = $db->lookup($query);
		
		// if(!mysql_num_rows($result))
			// return array();
		
		// $users = array();
		// while($row = mysql_fetch_assoc($result))
			// $users[$row['id']] = self::load($row['id']);
		// return $users;
	// }

	public function save()
	{
		$db = Db::instance();
		// map database fields to class properties; omit id and dateCreated
		$db_properties = array(
			'username' => $this->username,
			'password' => $this->password,
			'email' => $this->email,
			'name' => $this->name,
			'dob' => $this->dob,			
			'sex' => $this->sex,
			'location' => $this->location,
			'biography' => $this->biography,
			'picture' => $this->picture,
			'picture_small' => $this->pictureSmall,
			'picture_large' => $this->pictureLarge,
			'notify_comment_task_leading' => $this->notifyCommentTaskLeading,
			'notify_edit_task_accepted' => $this->notifyEditTaskAccepted,
			'notify_comment_task_accepted' => $this->notifyCommentTaskAccepted,
			'notify_comment_task_update' => $this->notifyCommentTaskUpdate,
			'notify_follow_project' => $this->notifyFollowProject,
			'notify_organize_project' => $this->notifyOrganizeProject,
			'notify_banned_project' => $this->notifyBannedProject,
			'notify_discussion_reply' => $this->notifyDiscussionReply,
			'notify_make_task_leader' => $this->notifyMakeTaskLeader,
			'admin' => $this->admin,
			'last_login' => $this->lastLogin
		);		
		$db->store($this, __CLASS__, self::DB_TABLE, $db_properties);
	}

	/* instance methods */

	// public function getSubmissions($projectID=null, $limit=null, $deleted=false)
	// {
		// return (Submission::getUserSubmissions($this->id, $projectID, $limit, $deleted));		
	// }
	
	// public function getLastSubmission($projectID=null, $deleted=false)
	// {
		// return (Submission::getLastSubmissionByUser($this->id, $projectID, $deleted));
	// }	
	
	// public function getActions($projectID = null, $limit = null)
	// {
		// return (Action::getUserActions($this->getID(), $projectID, $limit));
	// }
	
	// public function getFiles($projectID=null, $limit=null, $deleted=false)
	// {
		// return (File::getUserFiles($this->id, $projectID, $limit, $deleted));
	// }
	
	// public function getDiscussions($projectID=null, $limit=null, $deleted=false)
	// {
		// return (Discussion::getUserDiscussions($this->id, $projectID, $limit, $deleted));
	// }
	
	// public function getLastDiscussion($projectID=null, $deleted=false)
	// {
		// return (Discussion::getLastDiscussionByUser($this->id, $projectID, $deleted));
	// }
	
	// public function getReadMessages()
	// {
		// return (Message::getReceivedMessagesByUserID($this->id,null,false));
	// }
	
	// public function getUnreadMessages()
	// {
		// return (Message::getReceivedMessagesByUserID($this->id,null,true));
	// }
	
	// public function getNumUnreadMessages()
	// {
		// $unreadMessages = self::getUnreadMessages();
		// return (count($unreadMessages));
	// }
	
	public function toString()
	{
		return ($this->userName);
	}
	
	// --- only getters and setters below here --- //
	
	/* Epic getter -- convert the object data to an array */
	// public function getArgsArray()
	// {
		// $args = array
		// (
			// 'id' => $this->id,
			// 'username' => $this->username,
			// 'password' => $this->password,
			// 'email' => $this->email,
			// 'name' => $this->firstName,
			// 'dob' => $this->dob,			
			// 'sex' => $this->sex,
			// 'location' => $this->location,
			// 'biography' => $this->biography,
			// 'date_created' => $this->dateCreated,
			// 'last_login' => $this->lastLogin
		// );
		
		// return $args;
	// }
	public function getID()
	{
		return ($this->id);
	}
	
	public function setID($newID)
	{
		$this->modified = true;
		$this->id = $newID;
	}
	
	public function getUsername()
	{
		return ($this->username);
	}
	
	public function setUsername($newUsername)
	{
		$this->modified = true;
		$this->username = $newUsername;
	}
	
	public function getPassword()
	{
		return ($this->password);
	}
	
	public function setPassword($newPassword)
	{
		$this->modified = true;
		$this->password = $newPassword;
	}
	
	public function getEmail()
	{
		return ($this->email);
	}
	
	public function setEmail($newEmail)
	{
		$this->modified = true;	
		$this->email = $newEmail;
	}
	
	public function getName()
	{
		return ($this->name);
	}
	
	public function setName($newName)
	{
		$this->modified = true;
		$this->name = $newName;
	}
	
	public function getSex()
	{
		return ($this->sex);
	}
	
	public function setSex($newSex)
	{
		$this->modified = true;	
		$this->sex = $newSex;
	}
	
	public function getLocation()
	{
		return ($this->location);
	}
	
	public function setLocation($newLocation)
	{
		$this->modified = true;	
		$this->location = $newLocation;
	}
	
	public function getDOB()
	{
		return ($this->dob);
	}
	
	public function setDOB($newDOB)
	{
		$this->modified = true;	
		$this->dob = $newDOB;
	}
	
	public function getBiography()
	{
		return ($this->biography);
	}
	
	public function setBiography($newBiography)
	{
		$this->modified = true;
		$this->biography = $newBiography;
	}
	
	public function setPictureSmall($newPictureSmall)
	{
		$this->modified = true;
		$this->pictureSmall = $newPictureSmall;
	}
	
	public function getPictureSmall()
	{
		return ($this->pictureSmall);
	}

	public function setPictureLarge($newPictureLarge)
	{
		$this->modified = true;
		$this->pictureLarge = $newPictureLarge;
	}
	
	public function getPictureLarge()
	{
		return ($this->pictureLarge);
	}
	
	public function setPicture($newPicture)
	{
		$this->modified = true;
		$this->picture = $newPicture;
	}
	
	public function getPicture()
	{
		return ($this->picture);
	}
	
	public function getNotifyCommentTaskLeading() {
		return ($this->notifyCommentTaskLeading);
	}
	
	public function setNotifyCommentTaskLeading($newNotifyCommentTaskLeading) {
		$this->notifyCommentTaskLeading = $newNotifyCommentTaskLeading;
		$this->modified = true;
	}
	
	public function getNotifyEditTaskAccepted() {
		return ($this->notifyEditTaskAccepted);
	}
	
	public function setNotifyEditTaskAccepted($newNotifyEditTaskAccepted) {
		$this->notifyEditTaskAccepted = $newNotifyEditTaskAccepted;
		$this->modified = true;
	}

	public function getNotifyCommentTaskAccepted() {
		return ($this->notifyCommentTaskAccepted);
	}
	
	public function setNotifyCommentTaskAccepted($newNotifyCommentTaskAccepted) {
		$this->notifyCommentTaskAccepted = $newNotifyCommentTaskAccepted;
		$this->modified = true;
	}

	public function getNotifyCommentTaskUpdate() {
		return ($this->notifyCommentTaskUpdate);
	}
	
	public function setNotifyCommentTaskUpdate($newNotifyCommentTaskUpdate) {
		$this->notifyCommentTaskUpdate = $newNotifyCommentTaskUpdate;
		$this->modified = true;
	}

	public function getNotifyFollowProject() {
		return ($this->notifyFollowProject);
	}
	
	public function setNotifyFollowProject($newNotifyFollowProject) {
		$this->notifyFollowProject = $newNotifyFollowProject;
		$this->modified = true;
	}

	public function getNotifyOrganizeProject() {
		return ($this->notifyOrganizeProject);
	}
	
	public function setNotifyOrganizeProject($newNotifyOrganizeProject) {
		$this->notifyOrganizeProject = $newNotifyOrganizeProject;
		$this->modified = true;
	}

	public function getNotifyBannedProject() {
		return ($this->notifyBannedProject);
	}
	
	public function setNotifyBannedProject($newNotifyBannedProject) {
		$this->notifyBannedProject = $newNotifyBannedProject;
		$this->modified = true;
	}

	public function getNotifyDiscussionReply() {
		return ($this->notifyDiscussionReply);
	}
	
	public function setNotifyDiscussionReply($newNotifyDiscussionReply) {
		$this->notifyDiscussionReply = $newNotifyDiscussionReply;
		$this->modified = true;
	}	
	
	public function getNotifyMakeTaskLeader() {
		return ($this->notifyMakeTaskLeader);
	}
	
	public function setNotifyMakeTaskLeader($newNotifyMakeTaskLeader) {
		$this->notifyMakeTaskLeader = $newNotifyMakeTaskLeader;
		$this->modified = true;
	}
	
	public function getAdmin() {
		return ($this->admin);
	}
	
	public function setAdmin($newAdmin) {
		$this->admin = $newAdmin;
		$this->modified = true;
	}
	
	public function getDateCreated()
	{
		return ($this->dateCreated);
	}
	
	public function setDateCreated($newDateCreated)
	{
		$this->modified = true;	
		$this->dateCreated = $newDateCreated;
	}

	public function getLastLogin()
	{
		return ($this->lastLogin);
	}
	
	public function setLastLogin($newLastLogin)
	{
		$this->modified = true;	
		$this->lastLogin = $newLastLogin;
	}
}