<?php$thisUser = User::load(Session::getUserID());$email = $thisUser->getEmail();$fork = $SOUP->fork();$fork->set('id', 'notifications');$fork->set('title', 'Notifications');$fork->startBlockSet('body');function checked($value) {	return (($value == 1) ? 'checked="checked"' : '');}?><script type="text/javascript">$(document).ready(function(){	$('#notifications input:checkbox').change(function(){		var value = ($(this).is(':checked')) ? 'notify' : '';		buildPost({			'processPage': '<?= Url::settingsProcess() ?>',			'info': {				'action': 'notification',				'notificationType': $(this).attr('id'),				'notificationValue': value			}		});	});});</script><p>You will be notified via email (<?= $email ?>) when&hellip;</p><div class="line"></div><ul class="segmented-list notifications">	<li>		<span class="category">tasks</span>		<input id="chkMakeTaskLeader" type="checkbox" value="notify" <?= checked($thisUser->getNotifyMakeTaskLeader()) ?> />		<label for="chkMakeTaskLeader">someone makes you the leader of a task</label>	</li>	<li>		<span class="category">tasks</span>		<input id="chkCommentTaskLeading" type="checkbox" value="notify" <?= checked($thisUser->getNotifyCommentTaskLeading()) ?> />		<label for="chkCommentTaskLeading">someone comments on a task you are leading</label>	</li>	<li>		<span class="category">tasks</span>		<input id="chkEditTaskJoined" type="checkbox" value="notify" <?= checked($thisUser->getNotifyEditTaskAccepted()) ?> />		<label for="chkEditTaskJoined">someone edits a task you joined</label>	</li>	<li>		<span class="category">tasks</span>		<input id="chkCommentTaskJoined" type="checkbox" value="notify" <?= checked($thisUser->getNotifyCommentTaskAccepted()) ?> />		<label for="chkCommentTaskJoined">someone comments on a task you joined</label>	</li>	<li>		<span class="category">tasks</span>		<input id="chkCommentTaskUpdate" type="checkbox" value="notify" <?= checked($thisUser->getNotifyCommentTaskUpdate()) ?> />		<label for="chkCommentTaskUpdate">someone comments on a task update you created</label>	</li>	<li>		<span class="category">people</span>		<input id="chkOrganizeProject" type="checkbox" value="notify" <?= checked($thisUser->getNotifyOrganizeProject()) ?> />		<label for="chkOrganizeProject">you are invited to help organize a project</label>	</li>		<li>		<span class="category">people</span>		<input id="chkFollowProject" type="checkbox" value="notify" <?= checked($thisUser->getNotifyFollowProject()) ?> />		<label for="chkFollowProject">you are invited to follow a project</label>	</li>	<li>		<span class="category">people</span>		<input id="chkBannedProject" type="checkbox" value="notify" <?= checked($thisUser->getNotifyBannedProject()) ?> />		<label for="chkBannedProject">you are banned from a project</label>	</li>	<li>		<span class="category">discussions</span>		<input id="chkDiscussionReply" type="checkbox" value="notify" <?= checked($thisUser->getNotifyDiscussionReply()) ?> />		<label for="chkDiscussionReply">someone replies to a discussion you posted</label>	</li></ul><?php$fork->endBlockSet();$fork->render('site/partial/panel');