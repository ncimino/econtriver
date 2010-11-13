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
	// AjaxIt('QaAccountGet', content_id, '', '', bindQaAccts());
	AjaxIt('QaAccountGet', content_id);
}

function QaAccountAdd(content_id, name_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value);
	AjaxIt('QaAccountAdd', content_id, post_data, name_id, bindQaAccts());
}

function QaAccountEdit(content_id, name_id, acct_id) {
	var post_data = "name=" + escape(document.getElementById(name_id).value) + "&acct_id=" + acct_id;
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
	var post_data = "name=" + escape(document.getElementById(name_id).value) + "&group_id=" + group_id;
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
		$(".ui-droppable").droppable( {
		activeClass : 'ui-state-hover',
		hoverClass : 'ui-state-active',
		drop : function(event, ui) {
			grp_id = $(ui.draggable).attr("id").slice(8);
			acct_id = $(this).attr("id").slice(10);
			QaSharedAccountsAdd(content_id, grp_id, acct_id, bindQaSa(content_id));
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
	AjaxIt('QaSharedAccountsAdd', content_id, post_data, '', bindQaSa(content_id));
}

function QaSharedAccountsDrop(content_id, grp_id, acct_id) {
	var post_data = "grp_id=" + grp_id + "&acct_id=" + acct_id;
	AjaxIt('QaSharedAccountsDrop', content_id, post_data, '', bindQaSa(content_id));
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
		$(".ui-droppable").droppable( {
		activeClass : 'ui-state-hover',
		hoverClass : 'ui-state-active',
		drop : function(event, ui) {
			user_id = $(ui.draggable).attr("id").slice(10);
			grp_id = $(this).attr("id").slice(8);
			QaGroupMembersAdd(content_id, grp_id, user_id, bindQaGm(content_id));
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
	AjaxIt('QaGroupMembersDrop', content_id, post_data, '', bindQaGm(content_id));
}

function QaContactAdd(content_id, contact_input_id) {
	var post_data = "name=" + escape(document.getElementById(contact_input_id).value);
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
	
	// Bind Txn Add focus to Special color
	$('#txn_add').live('focusin', function() {
		document.getElementById('new_txn_submit')
				.setAttribute('class', 'ui-icon-special ui-icon-plusthick ui-float-left');
	}).live('focusout', function() {
		document.getElementById('new_txn_submit')
				.setAttribute('class', 'ui-icon ui-icon-plusthick ui-float-left');
	});
	
	// Bind Add Txn button to QaTxnAdd
	$('#new_txn_submit').live('click', function() {
		QaTxnAdd();
	}).live('keypress', function(e) {
		if (e.keyCode == 13) {
			QaTxnAdd('new_txn_');
		}
	});
	
	// Bind Split Txn button to 
	$('#new_txn_split').live('click', function() {
		//QaTxnAdd();
	}).live('keypress', function(e) {
		if (e.keyCode == 13) {
			//QaTxnAdd('new_txn_');
		}
	});
	
	// Bind Delete Txn button to QaTxnDelete
	$('.txn_delete').live('click', function() {
		txn_id = this.getAttribute('id').slice(this.getAttribute('id').lastIndexOf('_') + 1);
		if (confirm("Are you sure that you want to delete this transaction?")) QaTxnDelete(txn_id);
	});
	
	// Bind Sort Txns link to QaTxnSort
	$('.txn_title').live('click', function() {
		sort_by_id = this.id;
		QaTxnGet('quick_accounts_txn_div', this.id, 1);
	});
	
	// Bind Show Acct dropdown to QaTxnShow
	$('#show_acct').live('change', function() {
		QaTxnGet('quick_accounts_txn_div', sort_by_id, 0, this.value);
	});
	
	// Bind BankSays Checkbox to QaTxnEdit
	$('.txn_banksays_check')
			.live('click', function() {
				txn_id = this.getAttribute('id')
						.slice(this.getAttribute('id').lastIndexOf('_') + 1);
				QaTxnEdit('quick_accounts_txn_div', sort_by_id, 0, document
						.getElementById('show_acct').value, txn_id);
			});
	
	// Bind Txn History to QaTxnShowHistory
	$('.txn_show_history').live('click', function() {
		txn_id = this.getAttribute('id').slice(this.getAttribute('id').lastIndexOf('_') + 1);
		cell_id = 'txn_history_' + txn_id;
		row_id = document.getElementById('txn_history_row_' + txn_id);
		table = document.getElementById('txnh_table_' + txn_id);
		if (row_id && row_id.getAttribute('style')) {
			row_id.removeAttribute('style');
			if (!(table)) QaTxnGetHistory(cell_id, txn_id); // Only
		} else {
			row_id.setAttribute('style', 'display:none;');
		}
	});
	
	// Bind Txn Notes to QaTxnShowNotes
	$('.txn_show_notes').live('click', function() {
		txn_id = this.getAttribute('id').slice(this.getAttribute('id').lastIndexOf('_') + 1);
		cell_id = 'txn_notes_' + txn_id;
		row_id = document.getElementById('txn_notes_row_' + txn_id);
		table = document.getElementById('txnn_table_' + txn_id);
		if (row_id && row_id.getAttribute('style')) {
			row_id.removeAttribute('style');
			if (!(table)) QaTxnGetNotes(cell_id, txn_id);
		} else {
			row_id.setAttribute('style', 'display:none;');
		}
	});
	
	// Bind Txn Trash Bin to QaTxnGetTrash
	$('.txn_show_trash').live('click', function() {
		content = document.getElementById('txn_actions_content');
		table = document.getElementById('txnt_table');
		show_acct = document.getElementById('show_acct');
		if (content && content.getAttribute('style')) {
			content.removeAttribute('style');
			if (!(table)) QaTxnGetTrash(sort_by_id, 0, show_acct.value);
		} else {
			content.setAttribute('style', 'display:none;');
		}
	});
	
	// Bind Txn Modification to change save icon
	$('.txn_acct_select_odd, .txn_acct_select_even, .txn_input').live('change', function() {
		txn_id = this.getAttribute('id').slice(this.getAttribute('id').lastIndexOf('_') + 1);
		element = document.getElementById('txn_save_' + txn_id);
		if (element) element.setAttribute('class', 'ui-icon-special ui-icon-disk ui-float-left');
		element = document.getElementById('txn_save_anchor_' + txn_id);
		if (element) element.setAttribute('class', 'txn_save');
	});
	
	// Bind Txn Save to QaTxnEdit
	$('.txn_save').live('click', function() {
			txn_id = this.getAttribute('id').slice(this.getAttribute('id').lastIndexOf('_') + 1);
			QaTxnEdit('quick_accounts_txn_div', sort_by_id, 0, document.getElementById('show_acct').value, txn_id);
		}).live('keypress', function(e) {
			if (e.keyCode == 13) {
				txn_id = this.getAttribute('id')
					.slice(this.getAttribute('id').lastIndexOf('_') + 1);
				QaTxnEdit('quick_accounts_txn_div', sort_by_id, 0, document
						.getElementById('show_acct').value, txn_id);
			}
		});
	
	// Bind TxnH Make Active to QaTxnMakeActive
	$('.txnh_make_active').live('click', function() {
		txn_id = this.getAttribute('id').slice(this.getAttribute('id').lastIndexOf('_') + 1);
		active_txn_id = $('#txnh_make_inactive_' + txn_id).val();
		QaTxnMakeActive('quick_accounts_txn_div', sort_by_id, 0, document.getElementById('show_acct').value, txn_id, active_txn_id);
	});
	
	// Bind TxnT Make Active to QaTxnRestore
	$('.txnt_make_active').live('click', function() {
		txn_id = this.getAttribute('id').slice(this.getAttribute('id').lastIndexOf('_') + 1);
		QaTxnRestore(txn_id);
	});
	
});

function bindQaTxn() {
	return function() {
		$('.dateselection').datepicker( {
			showOn : 'focus',
			dateFormat : $('#date_format').val()
		});
		$("input.autocomplete_type").autocomplete( {
			minLength : 2,
	        source : QaTxnGetAutoCompleteValues('type').split("--QaAjaxDelimeter--") 
		});
		$("input.autocomplete_establishment").autocomplete( {
			minLength : 2,
	        source : QaTxnGetAutoCompleteValues('establishment').split("--QaAjaxDelimeter--") 
		});
		$("input.autocomplete_note").autocomplete( {
			minLength : 2,
	        source : QaTxnGetAutoCompleteValues('note').split("--QaAjaxDelimeter--") 
		});
	};
}

function bindQaTxnGetNotes(txn_id) {
	return function() {
		$('#txnn_' + txn_id).tabs();
	};
}

function QaTxnEdit(content_id, sort_id, change_dir, show_acct, txn_id, focus_id) {
	if (!content_id) content_id = 'quick_accounts_txn_div';
	if (!focus_id) focus_id = '';
	var post_data = getTxnDataFromInputs('txn_', '_' + txn_id) + "&current_txn_id=" + escape(txn_id);
	AjaxIt('QaTxnAdd', content_id, post_data, focus_id, bindQaTxn());
}

function QaTxnGetAutoCompleteValues(field_id) {
	return AjaxGetIt('QaTxnGetAutoCompleteValues.php?field_id='+field_id);
}

function QaTxnGetHistory(content_id, txn_id) {
	var post_data = "txn_id=" + escape(txn_id);
	AjaxIt('QaTxnGetHistory', content_id, post_data);
}

function QaTxnGetNotes(content_id, txn_id) {
	txn_parent_id = $('#txn_parent_id_' + txn_id).val();
	var post_data = "txn_id=" + escape(txn_id) + "&txn_parent_id=" + escape(txn_parent_id);
	AjaxIt('QaTxnGetNotes', content_id, post_data, '', bindQaTxnGetNotes(txn_parent_id));
}

function QaTxnGetTrash(sort_id, change_dir, show_acct, content_id) {
	if (!content_id) content_id = 'txn_actions_content';
	if (sort_id) {
		sort_dir = (document.getElementById(sort_id + '_DESC')) ? ((change_dir) ? 'ASC' : 'DESC') : ((change_dir) ? 'DESC' : 'ASC');
		var post_data = "sort_id=" + escape(sort_id) + "&sort_dir=" + escape(sort_dir);
		if (show_acct || show_acct == 0) var post_data = post_data + "&show_acct=" + escape(show_acct);
	} else {
		if (show_acct || show_acct == 0) var post_data = "show_acct=" + escape(show_acct);
	}
	AjaxIt('QaTxnGetTrash', content_id, post_data, '', bindQaTxn());
}

function QaTxnDelete(txn_id, content_id) {
	if (!content_id) content_id = 'quick_accounts_txn_div';
	var post_data = "txn_id=" + escape(txn_id) + "&log=true";
	AjaxIt('QaTxnDelete', content_id, post_data, '', bindQaTxn());
}

function QaTxnGet(content_id, sort_id, change_dir, show_acct) {
	if (!content_id) content_id = 'quick_accounts_txn_div';
	var post_data = getDisplayOptions(sort_id, change_dir, show_acct);
	AjaxIt('QaTxnGet', content_id, post_data, '', bindQaTxn());
}

function getDisplayOptions(sort_id, change_dir, show_acct) {
	var post_data = "";
	if (sort_id) {
		sort_dir = (document.getElementById(sort_id + '_DESC')) ? ((change_dir) ? 'ASC' : 'DESC') : ((change_dir) ? 'DESC' : 'ASC');
		post_data = "sort_id=" + escape(sort_id) + "&sort_dir=" + escape(sort_dir);
		if (show_acct || show_acct == 0) post_data = post_data + "&show_acct=" + escape(show_acct);
	} else {
		if (show_acct || show_acct == 0) post_data = "show_acct=" + escape(show_acct);
	}
	return post_data;
}

function QaTxnRestore(txn_id, content_id) {
	if (!content_id) content_id = 'quick_accounts_txn_div';
	var post_data = "txn_id=" + escape(txn_id);
	AjaxIt('QaTxnRestore', content_id, post_data, '', bindQaTxn());
}

function QaTxnMakeActive(content_id, sort_id, change_dir, show_acct, txn_id, active_txn_id) {
	if (!content_id) content_id = 'quick_accounts_txn_div';
	var post_data = getDisplayOptions(sort_id, change_dir, show_acct) + "&" + getTxnDataFromInputs('txnh_', '_' + txn_id) + "&current_txn_id=null" + "&log=true" + "&active_txn_id=" + escape(active_txn_id);
	AjaxIt('QaTxnMakeActive', content_id, post_data, '', bindQaTxn());
}

function QaTxnAddNotes(txn_id, note, content_id) {
	if (!content_id) content_id = 'quick_accounts_txn_div';
	var post_data = "txn_id=" + escape(txn_id) + "&note=" + escape(note);
	AjaxIt('QaTxnAddNotes', content_id, post_data, '', bindQaTxn());
}

function QaTxnAdd(current_txn_id, focus_id, content_id) {
	if (!content_id) content_id = 'quick_accounts_txn_div';
	if (!focus_id) focus_id = 'new_txn_type';
	var post_data = getTxnDataFromInputs('new_txn_') + "&current_txn_id=null";
	AjaxIt('QaTxnAdd', content_id, post_data, focus_id, bindQaTxn());
}

function getTxnDataFromInputs(prefix, postfix) {
	if (!prefix) prefix = '';
	if (!postfix) postfix = '';
	if (document.getElementById(prefix + 'banksays' + postfix)) {
		banksays = $('#' + prefix + 'banksays' + postfix + ':checked').val();
	} else {
		banksays = 'null';
	}
	if (document.getElementById(prefix + 'parent_id' + postfix)) {
		parent_id = document.getElementById(prefix + 'parent_id' + postfix).value;
	} else {
		parent_id = 'null';
	}
	var post_data = "acct=" + escape(document.getElementById(prefix + 'acct' + postfix).value) + "&date=" + escape(document
			.getElementById(prefix + 'date' + postfix).value) + "&type=" + escape(document
			.getElementById(prefix + 'type' + postfix).value) + "&establishment=" + escape(document
			.getElementById(prefix + 'establishment' + postfix).value) + "&note=" + escape(document
			.getElementById(prefix + 'note' + postfix).value) + "&credit=" + escape(document
			.getElementById(prefix + 'credit' + postfix).value) + "&debit=" + escape(document
			.getElementById(prefix + 'debit' + postfix).value) + "&parent_id=" + escape(parent_id) + "&banksays=" + escape(banksays);
	return post_data;
}