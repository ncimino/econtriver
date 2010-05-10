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

function bindQaAccts() {
	// Refresh Txn info
	return QaTxnGet();
}

function QaAccountGet(content_id) {
	AjaxIt('QaAccountGet', content_id, '', '', bindQaAccts());
}

function QaAccountAdd(content_id, name_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value);
	AjaxIt('QaAccountAdd', content_id, post_data, name_id, bindQaAccts());
}

function QaAccountEdit(content_id, name_id, acct_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value)
			+ "&acct_id=" + acct_id;
	AjaxIt('QaAccountEdit', content_id, post_data, name_id, bindQaAccts());
}

function QaAccountDrop(content_id, acct_id) {
	var post_data = "acct_id=" + acct_id;
	AjaxIt('QaAccountDrop', content_id, post_data, '', bindQaAccts());
}

function QaAccountRestore(content_id, acct_id) {
	var post_data = "acct_id=" + acct_id;
	AjaxIt('QaAccountRestore', content_id, post_data, '', bindQaAccts());
}

/*
 * Groups
 */

function bindQaGroups() {
	// Refresh Txn info
	return QaTxnGet();
}

function QaGroupGet(content_id) {
	AjaxIt('QaGroupGet', content_id, '', '', bindQaGroups());
}

function QaGroupAdd(content_id, name_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value);
	AjaxIt('QaGroupAdd', content_id, post_data, name_id, bindQaGroups());
}

function QaGroupEdit(content_id, name_id, group_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value)
			+ "&group_id=" + group_id;
	AjaxIt('QaGroupEdit', content_id, post_data, name_id, bindQaGroups());
}

function QaGroupDrop(content_id, group_id) {
	var post_data = "group_id=" + group_id;
	AjaxIt('QaGroupDrop', content_id, post_data, '', bindQaGroups());
}

function QaGroupPermDrop(content_id, group_id) {
	var post_data = "group_id=" + group_id;
	AjaxIt('QaGroupPermDrop', content_id, post_data, '', bindQaGroups());
}

function QaGroupRejoin(content_id, group_id) {
	var post_data = "group_id=" + group_id;
	AjaxIt('QaGroupRejoin', content_id, post_data, '', bindQaGroups());
}

/*
 * Shared Accounts
 */

function bindQaSa(content_id) {
	return [ function() {
		// Makes Groups draggable and Groups droppable
			$(".ui-draggable").draggable( {
				revert : 'invalid'
			});
			$(".ui-droppable").droppable(
					{
						activeClass : 'ui-state-hover',
						hoverClass : 'ui-state-active',
						drop : function(event, ui) {
							grp_id = $(ui.draggable).attr("id").slice(8);
							acct_id = $(this).attr("id").slice(10);
							QaSharedAccountsAdd(content_id, grp_id, acct_id,
									bindQaSa(content_id));
						}
					});
			// Refresh Txn info
		}, QaTxnGet() ];
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
	return [ function() {
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
							user_id = $(ui.draggable).attr("id").slice(10);
							grp_id = $(this).attr("id").slice(8);
							QaGroupMembersAdd(content_id, grp_id, user_id,
									bindQaGm(content_id));
						}
					});
		}
		// Refresh Txn info
		, QaTxnGet() ];
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

function QaContactAdd(content_id, contact_input_id) {
	var post_data = "name="
			+ escape(document.getElementById(contact_input_id).value);
	AjaxIt('QaContactAdd', content_id, post_data, '', bindQaGm(content_id));
}

function QaContactDrop(content_id, user_id) {
	var post_data = "user_id=" + user_id;
	AjaxIt('QaContactDrop', content_id, post_data, '', bindQaGm(content_id));
}

/*
 * Transactions
 */
var sort_by_id;
$(document).ready(function() {
	if ($('#quick_accounts_txn_div').length) {
		QaTxnGet();
	}

	// Bind dateselection to datepicker
		$(function() {
			$('.dateselection').live('click', function() {
				$(this).datepicker( {
					showOn : 'focus'
				}).focus();
			});
		});

		// Bind Add Txn button to QaTxnAdd
		$(function() {
			$('#new_txn_submit').live('click', function() {
				QaTxnAdd();
			});
		});

		// Bind Sort Txns link to QaTxnSort
		$(function() {
			$('.txn_title').live('click', function() {
				sort_by_id = this.id;
				QaTxnGet('quick_accounts_txn_div', this.id, 1);
			});
		});

		// Bind Show Acct dropdown to QaTxnShow
		$(function() {
			$('#show_acct').live('change',function() {
				QaTxnGet('quick_accounts_txn_div', sort_by_id, 0,
						this.value);
			});
		});
	});

function QaTxnGet(content_id, sort_id, change_dir, show_acct) {
	if (!content_id) {
		content_id = 'quick_accounts_txn_div';
	}
	if (sort_id) {
		if (document.getElementById(sort_id + '_DESC')) {
			if (change_dir == 1)
				sort_dir = 'ASC';
			else
				sort_dir = 'DESC';
		} else {
			if (change_dir == 1)
				sort_dir = 'DESC';
			else
				sort_dir = 'ASC';
		}
		var post_data = "sort_id=" + escape(sort_id) + "&sort_dir="
				+ escape(sort_dir);
		if (show_acct || show_acct == 0)
			var post_data = post_data + "&show_acct=" + escape(show_acct);
	} else {
		if (show_acct || show_acct == 0)
			var post_data = "show_acct=" + escape(show_acct);
	}
	AjaxIt('QaTxnGet', content_id, post_data);
}

function QaTxnAdd(content_id) {
	if (!content_id)
		content_id = 'quick_accounts_txn_div';
	var post_data = "acct="
			+ escape(document.getElementById('new_txn_acct').value) + "&date="
			+ escape(document.getElementById('new_txn_date').value) + "&type="
			+ escape(document.getElementById('new_txn_type').value)
			+ "&establishment="
			+ escape(document.getElementById('new_txn_establishment').value)
			+ "&note=" + escape(document.getElementById('new_txn_note').value)
			+ "&credit="
			+ escape(document.getElementById('new_txn_credit').value)
			+ "&debit="
			+ escape(document.getElementById('new_txn_debit').value);
	AjaxIt('QaTxnAdd', content_id, post_data, 'new_txn_date', '');
}