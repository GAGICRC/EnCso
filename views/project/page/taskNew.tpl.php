<?php$project = $SOUP->get('project');$yourTasks = $SOUP->get('yourTasks');$fork = $SOUP->fork();$fork->set('project', $project);$fork->set('pageTitle', $project->getTitle());$fork->set('headingURL', Url::project($project->getID()));$fork->set('selected', "tasks");$fork->set('breadcrumbs', Breadcrumbs::taskNew($project->getID()));$fork->startBlockSet('body');?><td class="left"><?php	$SOUP->render('project/partial/taskNew', array(		));?></td><td class="right"><?php	$SOUP->render('project/partial/tasks', array(		'tasks' => $yourTasks,		'user' => Session::getUser(),		'size' => 'small',		'hasPermission' => false,		'title' => 'Your Tasks'		));?></td><?php$fork->endBlockSet();$fork->render('site/partial/page');