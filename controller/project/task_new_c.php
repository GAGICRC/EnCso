<?php
require_once("../../global.php");

$slug = Filter::text($_GET['slug']);
$project = Project::getProjectFromSlug($slug);

// kick us out if slug invalid or not organizer/creator

if($project == null) {
	header('Location: '.Url::error());
	exit();
} elseif( (!Session::isAdmin()) &&
	(!ProjectUser::isOrganizer(Session::getUserID(), $project->getID())) &&
	(!ProjectUser::isCreator(Session::getUserID(), $project->getID())) ) {
	header('Location: '.Url::error());
	exit();	
}

$yourTasks = Task::getYourTasks(Session::getUserID(), $project->getID());

$soup = new Soup();
$soup->set('project', $project);
$soup->set('yourTasks', $yourTasks);
$soup->render('project/page/taskNew');