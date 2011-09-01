<?phpinclude_once TEMPLATE_PATH.'/site/helper/format.php';$project = $SOUP->get('project');$contributors = $SOUP->get('contributors');// only organizers or creator may edit contributors$hasPermission = ( ProjectUser::isOrganizer(Session::getUserID(), $project->getID()) ||					ProjectUser::isCreator(Session::getUserID(), $project->getID()) );$fork = $SOUP->fork();$fork->set('id', 'contributors');$fork->set('title', 'Contributors');$fork->set('editable', $hasPermission);$fork->startBlockSet('body');?><?php if($hasPermission): ?><script type="text/javascript">$(document).ready(function(){	$("#contributors .editButton").click(function(){		var buttons = $("#contributors div.view input[type='button']");		if($(buttons).is(":hidden")) {			$(buttons).fadeIn();		} else {			$(buttons).fadeOut();		}	});		$("#contributors input.ban").click(function(){		var id = $(this).attr('id').substring(4);		buildPost({			'processPage': '<?= Url::peopleProcess($project->getID()) ?>',			'info': {				'action': 'ban',				'userID': id			},			'buttonID': $(this)		});	});		$("#contributors input.organizer").click(function(){		var id = $(this).attr('id').substring(10);		buildPost({			'processPage': '<?= Url::peopleProcess($project->getID()) ?>',			'info': {				'action': 'make-organizer',				'userID': id			},			'buttonID': $(this)		});	});});</script><?php endif; ?><div class="view"><?php if($contributors != null) {	echo '<ul class="segmented-list users">';	foreach($contributors as $c) {		echo '<li>';		if($hasPermission) {			echo '<input id="ban-'.$c->getID().'" type="button" class="ban hidden" value="Ban" />';			echo '<input id="organizer-'.$c->getID().'" type="button" class="organizer hidden" value="Make Organizer" />';		}		echo formatUserPicture($c->getID(), 'small');		echo '<h6 class="primary">'.formatUserLink($c->getID()).'</h6>';		echo '<p class="secondary">contributor</p>';		echo '</li>';	}	echo '</ul>';} else {	echo '<p>(none)</p>';}?></div><?php$fork->endBlockSet();$fork->render('site/partial/panel');