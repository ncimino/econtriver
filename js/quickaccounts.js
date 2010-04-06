/*
 * General
 */

function QaHideMsgs() {
	msg_id = AjaxGetIt('GetVar.php?var=getQaMsgsId');
	timedHide("'" + msg_id + "'", 8000);
}

/*
 * Accounts
 */

function QaAccountGet(content_id) {
	AjaxIt('QaAccountGet', content_id);
}

function QaAccountAdd(content_id, name_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value);
	AjaxIt('QaAccountAdd', content_id, post_data, name_id);
}

function QaAccountEdit(content_id, name_id, acct_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value)
			+ "&acct_id=" + acct_id;
	AjaxIt('QaAccountEdit', content_id, post_data, name_id);
}

function QaAccountDrop(content_id, acct_id) {
	var post_data = "acct_id=" + acct_id;
	AjaxIt('QaAccountDrop', content_id, post_data);
}

/*
 * Groups
 */

function QaGroupGet(content_id) {
	AjaxIt('QaGroupGet', content_id);
}

function QaGroupAdd(content_id, name_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value);
	AjaxIt('QaGroupAdd', content_id, post_data, name_id);
}

function QaGroupEdit(content_id, name_id, group_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value)
			+ "&group_id=" + group_id;
	AjaxIt('QaGroupEdit', content_id, post_data, name_id);
}

function QaGroupDrop(content_id, group_id) {
	var post_data = "group_id=" + group_id;
	AjaxIt('QaGroupDrop', content_id, post_data);
}

function QaGroupRejoin(content_id, group_id) {
	var post_data = "group_id=" + group_id;
	AjaxIt('QaGroupRejoin', content_id, post_data);
}

/*
 * Shared Accounts
 */

function bindQaSa(content_id) {
	return function() {
		$(".ui-draggable").draggable( {
			revert : 'invalid'
		});
		$(".ui-droppable").droppable(
				{
					activeClass : 'ui-state-hover',
					hoverClass : 'ui-state-active',
					drop : function(event, ui) {
						grp_id = $(ui.draggable).attr("id").slice(6);
						acct_id = $(this).attr("id").slice(7);
						QaSharedAccountsAdd(content_id, grp_id, acct_id,
								bindQaSa(content_id));
					}
				});
	};
}

function QaSharedAccountsGet(content_id) {
	AjaxIt('QaSharedAccountsGet', content_id, '', '', bindQaSa(content_id));
}

function QaSharedAccountsAdd(content_id, grp_id, acct_id) {
	var post_data = "grp_id=" + grp_id + "&acct_id=" + acct_id;
	AjaxIt('QaSharedAccountsAdd', content_id, post_data, '',
			bindQaSa(content_id));
}

function QaSharedAccountsDrop(content_id, grp_id, acct_id) {
	var post_data = "grp_id=" + grp_id + "&acct_id=" + acct_id;
	AjaxIt('QaSharedAccountsDrop', content_id, post_data, '',
			bindQaSa(content_id));
}

/*
 * Group Membership
 */

function bindQaGm(content_id) {
	return function() {
		// Clears find contact input from 'Email or User name'
		clearField('contact');
		// Makes Users dragable and Groups dropable
		$(".ui-draggable").draggable( {
			revert : 'invalid'
		});
		$(".ui-droppable").droppable(
				{
					activeClass : 'ui-state-hover',
					hoverClass : 'ui-state-active',
					drop : function(event, ui) {
						user_id = $(ui.draggable).attr("id").slice(6);
						grp_id = $(this).attr("id").slice(7);
						QaGroupMembersAdd(content_id, grp_id, user_id,
								bindQaGm(content_id));
					}
				});
	};
}

function QaGroupMembersGet(content_id) {
	AjaxIt('QaGroupMembersGet', content_id, '', '', bindQaGm(content_id));
}

function QaGroupMembersAdd(content_id, grp_id, user_id) {
	var post_data = "grp_id=" + grp_id + "&user_id=" + user_id;
	AjaxIt('QaGroupMembersAdd', content_id, post_data, '', bindQaGm(content_id));
}

function QaGroupMembersDrop(content_id, grp_id, user_id) {
	var post_data = "grp_id=" + grp_id + "&user_id=" + user_id;
	AjaxIt('QaGroupMembersDrop', content_id, post_data, '',
			bindQaGm(content_id));
}