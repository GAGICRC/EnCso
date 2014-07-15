<?phprequire_once("../../global.php");$slug = Filter::text($_GET['slug']);$project = Project::getProjectFromSlug($slug);$taskID = Filter::numeric($_GET['t']);$task = Task::load($taskID);// kick us out if slug or task invalidif( ($project == null) ||	($task == null) ) {	header('Location: '.Url::error());	exit();}// if private project, limit access to invited users, members, and admins// and exclude banned membersif($project->getPrivate()) {	if (!Session::isAdmin() && (!$project->isCreator(Session::getUserID()))) {		if (((!$project->isInvited(Session::getUserID())) && (!$project->isMember(Session::getUserID())) &&		(!$project->isTrusted(Session::getUserID()))) || ProjectUser::isBanned(Session::getUserID(),$project->getID())) {		 	header('Location: '.Url::error());			exit();				}	}}// get accepted for this task$accepted = Accepted::getByTaskID($taskID);//$contributorInvites = Invitation::getByTaskID($taskID);// has current user joined this task?$hasJoinedTask = false;if(Session::isLoggedIn()) {	$joined = Accepted::getByUserID(Session::getUserID(), $task->getID());	if(!empty($joined)) {		$hasJoinedTask = true;	}}// get latest updates for this task$latestUpdates = array();if($accepted != null) {	foreach($accepted as $a) {		$updates = Update::getByAcceptedID($a->getID());		if(!empty($updates)) {			$latestUpdate = reset($updates);			array_push($latestUpdates, $latestUpdate);		}	}}$events = Event::getTaskEvents($taskID, 5);$uploads = Upload::getByTaskID($taskID, false);$comments = Comment::getByTaskID($taskID);$soup = new Soup();$soup->set('project', $project);$soup->set('task', $task);$soup->set('accepted', $accepted);$soup->set('hasJoinedTask', $hasJoinedTask);//$soup->set('contributorInvites', $contributorInvites);$soup->set('events', $events);$soup->set('uploads', $uploads);$soup->set('comments', $comments);$soup->set('latestUpdates', $latestUpdates);$soup->render('project/page/task');?>