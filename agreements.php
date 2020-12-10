<!DOCTYPE html>
<html class="wp-toolbar" lang="en-US"><head>


<div class="esign-main-tab">
    <h1 class="nav-tab-wrapper">

        <a class="nav-tab nav-tab-active" href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-docs">My Documents</a>

        <a class="nav-tab " href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-settings">Settings</a>
        <a class="nav-tab " href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-mails-general">E-Mails</a>
            <a class="nav-tab  " href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-licenses-general">Licenses</a>


    <!-- <a class="nav-tab " href="?page=esign-support-general">Premium Support</a> -->


            <a class="nav-tab " href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-misc-general">Customization</a>


            <a class="nav-tab " href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-addons">Add-Ons</a>


    <a class="nav-tab  " href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-unlimited-sender-role">Roles</a>
    </h1>
    <br>

        

</div>


	<div style="padding:12px 0;">
    <div class="header_left">
    <p>
	<a class="add-new-h2" href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-view-document">Add New Document</a>
	</p>
    </div>
    
    <div class="header_right">
	<form id="esig_document_search_form" name="esig_document_search_form" action="" method="get"> <div class="select2-container" id="s2id_esig_document_search" style="min-width:150px;"><a href="javascript:void(0)" class="select2-choice" tabindex="-1">   <span class="select2-chosen" id="select2-chosen-1">All Sender</span><abbr class="select2-search-choice-close"></abbr>   <span class="select2-arrow" role="presentation"><b role="presentation"></b></span></a><label for="s2id_autogen1" class="select2-offscreen"></label><input class="select2-focusser select2-offscreen" type="text" aria-haspopup="true" role="button" aria-labelledby="select2-chosen-1" id="s2id_autogen1"><div class="select2-drop select2-display-none select2-with-searchbox">   <div class="select2-search">       <label for="s2id_autogen1_search" class="select2-offscreen"></label>       <input type="text" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" class="select2-input" role="combobox" aria-expanded="true" aria-autocomplete="list" aria-owns="select2-results-1" id="s2id_autogen1_search" maxlength="10" placeholder="">   </div>   <ul class="select2-results" role="listbox" id="select2-results-1">   </ul></div></div><select name="esig_all_sender" id="esig_document_search" style="min-width:150px;" tabindex="-1" title="" class="select2-offscreen"><option value="All Sender" selected="selected">All Sender</option></select><input type="hidden" name="document_status" value="awaiting"><input type="hidden" name="page" value="esign-docs"><input type="search" id="esig-document-search" class="esig_document_search" name="esig_document_search" style="min-width:250px;" placeholder="Document title or Signer name">
		
		<input type="submit" name="esig_search" class="button-primary" value="Search">
		</form>	</div>
    </div>

		
		
	<div class="header_left">
	<ul class="subsubsub">
		<!--<li class="all"><a class="" href="admin.php?page=esign-docs&amp;document_status=awaiting" title="View all documents">Active Documents</a> <span class="count">(1)</span> |</li>-->
		<li class="awaiting"><a class="current" href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-docs&amp;document_status=awaiting" title="View documents currently awaiting signatures">Awaiting Signatures <span class="count">(1)</span></a> |</li>
		<li class="draft"><a class="" href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-docs&amp;document_status=draft" title="View documents in draft mode">Draft <span class="count">(3)</span></a> |</li>
		<li class="signed"><a class="" href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-docs&amp;document_status=signed" title="View signed documents">Signed <span class="count">(0)</span></a> |</li>
		<li class="trash"><a class="" href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-docs&amp;document_status=trash" title="View documents in trash">Trash <span class="count">(2)</span></a></li>
		| <a title="View Stand Alone Documents" href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-docs&amp;document_status=stand_alone">Stand Alone</a> (1)| <a href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-docs&amp;document_status=esig_template" title="View Document Templates">Templates</a> (0)  		
	</ul>
	</div>

	<div class="header_right">
	
	</div>
	
     <form name="esig_document_form" action="" method="post">
     
	<div class="esig-documents-list-wrap">
		<div class="wrap">
</div><div class="wrap">		
		
		</div><table class="wp-list-table widefat fixed esig-documents-list" cellspacing="0">
			<thead>
				<tr>
                                    <th id="cb" class="check-column">
                                        <input name="selectall" type="checkbox" id="selectall" class="selectall" value=""></th>
					<th style="width: 245px;">Title </th>
                                        
                                        					<th style="width: 145px;">Signer(s)</th>
					<th style="width: 160px;">Latest Activity</th>
					<th style="width: 100px;">Date</th>
                                          				</tr>
			</thead>
		
			<tfoot>
				<tr>
					<th id="cb" class="manage-column column-cb  check-column">
					<input name="selectall1" type="checkbox" id="selectall1" class="selectall" value=""></th>
					<th>Title</th>
                                        
					 					<th style="width: 145px;">Signer(s)</th>
					<th style="width: 160px;">Latest Activity</th>
					<th style="width: 100px;">Date</th>
                                          				</tr>
			</tfoot>
			<tbody><tr id="post-4" class="post-4 type-post status-publish format-standard hentry category-uncategorized alternate iedit author-self level-0 " valign="top">
    <th scope="row" class="check-column">
        <label class="screen-reader-text" for="cb-select-11">Aprove me - Eduardo Bueno</label>
        <input id="cb-select-11" type="checkbox" name="esig_document_checked[]" value="4">
<div class="locked-indicator"></div>
</th>

<td class="post-title page-title column-title">

    <strong><a class="row-title" href="http://new.gsproperty.ca/e-signature-document/?esigpreview=1&amp;document_id=4" title="preview">Aprove me - Eduardo Bueno</a></strong>
    <div class="locked-info">
        <span class="locked-avatar"></span> 
        <span class="locked-text"></span>
    </div>

    <div class="row-actions">
<span class="active"><a href="http://new.gsproperty.ca/e-signature-document/?esigpreview=1&amp;document_id=4" title="View this document" target="_blank">View</a> </span>| <span class="edit"><a href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-resend_invite-document&amp;document_id=4" title="Resend this document">Resend Invite</a> </span>| <span class="trash"><a class="submitdelete" title="Move this item to the Trash" href="http://new.gsproperty.ca/wp-admin/admin.php?page=esign-trash-document&amp;document_id=4&amp;token=e0df44c298">Trash</a></span>        |<span class="esig_reminders_setting"> <a href="javascript:void(0)" data-document="4" data-reminder="&lt;i class='fa fa-play'&gt;&lt;/i&gt;start reminders" data-title="Aprove me - Eduardo Bueno" title="Signing reminders settings " id="reminders_document">Signing Reminders</a></span>     </div>
</td>

   
    
     <td><span title="Abdeisluk@gmail.com
">Eduardo Bueno<br></span></td>
    <td>Awaiting Signature(s)<br>Invite Sent<br></td>
    <td>23 November 2020</td>
    
    
</tr></tbody>
	</table>
</div>



<div class="footer-container">
<div class="header_left">

<select name="esig_bulk_option" id="bulk-action-selector-top">
            <option value="-1" selected="selected">Bulk Actions</option><option value="trash">Move to Trash</option> </select><input type="submit" name="esigndocsubmit" id="esig-action" class="button action" value="Apply">
</div>
<div class="footer_right">

  
</div>
</div>
<div class="pagination-below"><a href="https://www.approveme.com/your-voice-matters/" class="esig-feedback" target="_blank"><span class="esig-feedback-span"></span>We'd Love to hear Your Feedback!</a></div>
</form>
<!--<p align="right" style="font-weight:500;"><a href="admin.php?page=esign-about-general" class="esig-feedback"><span class="esig-feedback-span"></span>We'd Love to hear Your Feedback!</a></p>-->


			<div id="esig_reminder_popup_hidden" style="display:none;">
			<form name="esig_reminder_form" action="" method="post">
				<div class="esig_sad_popup wp-core-ui">
					<p class="popup-logo" align="center"><img src="My%20Documents%20_files/logo.png"></p>
					
					<p class="document_title_caption" style="display:none;">
						Send signing reminders for : <br>
					</p>
					<p class="instructions">
						
					</p>
					
					<div class="esig_reminder_invite_box">
					<span class="invite_signers">Invited Signers</span>
					</div>
					<div id="esig_reminder_invite_row">
					
					
					</div>
					
					<div class="settings_box_left"><a href="#" id="esig_pause_reminders">pause reminders</a></div>
					<div class="settings_box_right"><a href="#" id="send_instant_reminder_email" class="button-primary esig-button-large">Send Reminder Now</a></div>
				</div>
				</form>
			</div>
		

			<div id="esig_sad_popup_hidden" style="display:none;">
				<div class="esig_sad_popup wp-core-ui">
					<p class="popup-logo" align="center"><img src="My%20Documents%20_files/logo.png"></p>
					
					<p class="document_title_caption" style="display:none;">
						
					</p>
					<p class="instructions">
						Invite someone to sign your document.
					</p>
					<form class="invite_form">
						<ul>
							<input type="hidden" name="document_id" value="" class="document_id">
							<input type="hidden" name="url" value="" class="url">
							<li>
								<input type="text" id="sad-invite-name" name="name" placeholder="James Franco">
							</li>
							<li>
								<input type="text" id="sad-invite-email" name="email" placeholder="james@email.com">
							</li>
							<li>
								<input class="esig-mini-btn esig-blue-btn" id="sad-invite-submit" type="submit" name="" value="Send Invite">
							</li>
						</ul>
						<div class="loader_wrap">
							<div class="loader" style="display:none;">
								<img src="My%20Documents%20_files/loader.gif">
							</div>
						</div>
					</form>
					<div class="invite_box">
						Here is the URL for your document.
						<input class="invite_url" name="">
						<div class="copy-msg">Copy instructions go here</div>
					</div>
				</div>
			</div>

<!-- expired popup msg--> 
</div>