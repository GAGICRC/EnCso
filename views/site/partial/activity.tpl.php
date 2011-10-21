<?phpinclude_once TEMPLATE_PATH.'/site/helper/formatEvents.php';include_once TEMPLATE_PATH.'/site/helper/format.php';$events = $SOUP->get('events', array());$size = $SOUP->get('size', 'large');$olderURL = $SOUP->get('olderURL', null);$title = $SOUP->get('title', 'Recent Activity');$showProject = $SOUP->get('showProject', false);$fork = $SOUP->fork();$fork->set('id', 'activity');$fork->set('title', $title);$fork->startBlockSet('body');?><script type="text/javascript">$(document).ready(function(){	$('#activity div.diff-box').dialog({		autoOpen: false,		title: 'Show Diff',		modal: true,		width: 500	});	$('#activity a.diff').click(function(){		var id = $(this).attr('id').substring(5);		$('#diff-box-'+id).dialog('open');		return false;	});	});</script><?phpif($events != null){	echo '<ul class="segmented-list activity">';	foreach($events as $event)	{		echo '<li class="'.$event->getCssClass().'">';		//echo '<a class="picture small" href="'.Url::user($event->getUser1ID()).'"><img src="'.Url::userPictureSmall($event->getUser1ID()).'" /></a>';		echo '<h6 class="primary">'.formatEvent($event, $showProject).'</h6>';		echo '<p class="secondary">'.formatTimeTag($event->getDateCreated());		if($event->isDiffable()) {			echo ' <span class="slash">/</span> <a id="diff-'.$event->getID().'" class="diff" href="#">diff</a></p>';			echo '<div id="diff-box-'.$event->getID().'" class="diff-box">';			echo formatEvent($event, $showProject).' ('.formatTimeTag($event->getDateCreated()).')';			echo '<div class="line"> </div>';						// generate diff-box content			switch($event->getEventTypeID()) {				case 'edit_update_uploads':				case 'edit_task_uploads':					$addedIDs = explode(',',$event->getData2());					$added = '';					foreach($addedIDs as $a) {						if($a == '') continue; // skip blanks						$upload = Upload::load($a);						$added .= $upload->getOriginalName().' ('.formatFileSize($upload->getSize()).')<br /><br />';					}					if(!empty($added)) {						echo '<ins>'.$added.'</ins>';					}					$deletedIDs = explode(',',$event->getData1());					$deleted = '';					foreach($deletedIDs as $d) {						if($d == '') continue; // skip blanks						$upload = Upload::load($d);						$deleted .= $upload->getOriginalName().' ('.formatFileSize($upload->getSize()).')<br /><br />';					}					if(!empty($deleted)) {						echo '<del>'.$deleted.'</del>';					}					break;				case 'edit_pitch':					case 'edit_specs':				case 'edit_rules':				case 'edit_task_description':				case 'edit_update_message':										$from = $event->getData1();					$to = $event->getData2();						$from = str_replace('&#10;','<br />', $from);						$to = str_replace('&#10;','<br />', $to);						$diff = new FineDiff($from, $to);					$htmlDiff = $diff->renderDiffToHTML();									$htmlDiff = html_entity_decode($htmlDiff, ENT_QUOTES, 'UTF-8');					$htmlDiff = html_entity_decode($htmlDiff, ENT_QUOTES, 'UTF-8');					echo $htmlDiff;						break;									case 'edit_task_title':				case 'edit_update_title':					$from = $event->getData1();					$to = $event->getData2();						$diff = new FineDiff($from, $to);					$htmlDiff = $diff->renderDiffToHTML();					$htmlDiff = html_entity_decode($htmlDiff, ENT_QUOTES, 'UTF-8');					$htmlDiff = html_entity_decode($htmlDiff, ENT_QUOTES, 'UTF-8');					echo $htmlDiff;									break;				case 'edit_task_leader':					echo 'Old Leader: <del>'.formatUserLink($event->getUser1ID(), $event->getProjectID()).'</del><br /><br />';					echo 'New Leader: <ins>'.formatUserLink($event->getUser2ID(), $event->getProjectID()).'</ins>';					break;				case 'edit_task_num_needed':					$old = ($event->getData1() != null) ? $event->getData1() : '&#8734;';					$new = ($event->getData2() != null) ? $event->getData2() : '&#8734;';									echo 'Old: <del>'.$old.'</del> people needed<br /><br />';					echo 'New: <ins>'.$new.'</ins> people needed';					break;						case 'edit_task_deadline':				case 'edit_project_deadline':					$old = ($event->getData1() != null) ? formatTimeTag($event->getData1()) : '(none)';					$new = ($event->getData2() != null) ? formatTimeTag($event->getData2()) : '(none)';					echo 'Old Deadline: <del>'.$old.'</del><br /><br />';					echo 'New Deadline: <ins>'.$new.'</ins>';					break;				case 'edit_project_status':					$old = formatProjectStatus($event->getData1());					$new = formatProjectStatus($event->getData2());					echo 'Old Project Status: <del>'.$old.'</del><br /><br />';					echo 'New Project Status: <ins>'.$new.'</ins>';					break;								case 'edit_accepted_status':					$old = formatAcceptedStatus($event->getData1());					$new = formatAcceptedStatus($event->getData2());					echo 'Old Status: <del>'.$old.'</del><br /><br />';					echo 'New Status: <ins>'.$new.'</ins>';					break;				default:					echo 'unsupported diff';					break;			}						echo '</div>';		} else {			echo '</p>';		}		echo '</li>';	}	echo '</ul>';} else {	echo "<p>(none)</p>";}$fork->endBlockSet();if($olderURL != null){	$fork->startBlockSet("footer");	?><p><a href="<?= $olderURL ?>">Older Activity &raquo;</a></p>	<?php	$fork->endBlockSet();}$fork->render('site/partial/panel');