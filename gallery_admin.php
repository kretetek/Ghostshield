<?php

/****

	GALLERY ADMINISTRATION
	
	v0.2

*/

define("APP_PREFIX",	"gallery");
define("APP_PATH",		"/gallery/admin/");
define("LIB_DIR",		"../lib/");

require_once('vendor/autoload.php');
require_once("lib/page.class.php");
//require_once("db.php");
//db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");
//db::debug();

require_once("lib/gallery.class.php");

require_once(LIB_DIR."sys/log.php");
require_once(LIB_DIR."sys/form.php");
require_once(LIB_DIR."sys/settings.php");
require_once(LIB_DIR."sys/settings_form.php");
require_once(LIB_DIR."sys/encryption.php");
require_once(LIB_DIR."sys/pagination.php");

require_once(LIB_DIR."usr/user.php");
require_once(LIB_DIR."usr/users.php");
require_once(LIB_DIR."usr/form_forgot_password.php");
require_once(LIB_DIR."usr/form_password_reset.php");
require_once(LIB_DIR."usr/form_new_user.php");
require_once(LIB_DIR."usr/form_user_edit.php");
require_once(LIB_DIR."usr/form_user_delete.php");
require_once(LIB_DIR."usr/form_login.php");

//session_start();

$page = new page();
$page->addCSS("gallery_admin.css");
$page->addCSS("gallery.css");
$page->addScript("gallery_admin.js?".rand());

$request = $_REQUEST;
if (!isset($_REQUEST["action"]) && isset($_SESSION["request"])) {
	if (isset($_SESSION['request_time']) && $_SESSION['request_time'] > date("U", strtotime("-5 minutes")))
		$request = array_merge($_REQUEST, unserialize($_SESSION["request"]));
}

$no_access = '
					<h3>Class Warfare!</h3>
					<p>You have not been granted access to this feature.</p>';

ob_start();

//require_once("inc/header.php");
echo $page->header("Gallery Administration");

echo '
			<div id="banner">
				<div class="video_wrapper"><img src="//assets.'.$page->host.'/img/bottle-bg/none3-lighter.jpg" /></div>
			</div>
			<section class="gallery_admin admin">
				<div class="column full">
					<div class="column_interior">';

if (isset($request["action"]) && $request["action"] == "logout") {
	users::logout();
	echo users::form('login', ["destination" => "dashboard"]);

} elseif (isset($request["action"]) && $request["action"] == "forgot") {
	echo '
						<div id="content">'.
	users::form("forgot_password").'
						</div>';

} elseif (users::me()) {
	$gallery = new gallery(false);
	$gallery_name = "Admin";

	if (!isset($request["action"])) $request["action"] = "dashboard";

	if (isset($request["filter"])) $gallery_name .= "-".$request["filter"];

	echo '
						<div id="login_status">
							<div class="user">Logged in as '.users::me("name").'</div>
							<ul class="login_menu">
								<li class="lm_me"><a href="'.APP_PATH.'user-details?id='.users::me("id").'" class="pop">My Account</a></li>
								<li class="lm_logout"><a href="'.APP_PATH.'logout">Log Out</a></li>
								<li class="lm_dashboard"><a href="'.APP_PATH.'dashboard">Gallery Admin</a></li>';
	if (users::permitted("users")) echo '
								<li class="lm_users"><a href="'.APP_PATH.'users">Users</a></li>
								<li class="lm_new_user"><a href="'.APP_PATH.'new_user">New User</a></li>';
	if (users::permitted("admin")) echo '
								<li class="lm_log"><a href="'.APP_PATH.'log">Logs</a></li>';
	echo '
							</ul>
						</div>';

	if (isset($request["md5"])) {
		$ga = new galleryItem($request);

		if ($ga->validate()) {

			if (strpos($request["action"], "youtube") > -1 || $ga->current["youtube"] > "")
				$ga->youtubeConnect();
			
			echo '<a href="/gallery/admin">&#8592; Return to Gallery Administration</a>';
			
			if (isset($request["action"])) {
				if ($request["action"] == "approve") {
					if (users::permitted("approve")) {
						$result = $ga->approve();
					} else $result = $no_access;
	
				} elseif ($request["action"] == "deny") {
					if (users::permitted("approve")) {
						$result = $ga->deny();
					} else $result = $no_access;

				} elseif ($request["action"] == "approve-captions") {
					if (users::permitted("approve")) {
						$result = $ga->approveCaptions();
					} else $result = $no_access;

				} elseif ($request["action"] == "deny-captions") {
					if (users::permitted("approve")) {
						$result = $ga->denyCaptions();
					} else $result = $no_access;

				} elseif ($request["action"] == "remove") {
					if (users::permitted("approve")) {
						$result = $ga->remove();
					} else $result = $no_access;

				} elseif ($request["action"] == "product2") {
					if (users::permitted("approve")) {
						$result = $ga->product2();
					} else $result = $no_access;

				} elseif ($request["action"] == "comm_exclude") {
					if (users::permitted("approve")) {
						$result = $ga->commExclude();
					} else $result = $no_access;

				} elseif ($request["action"] == "tags") {
					if (users::permitted("approve")) {
						$result = $ga->updateTags();
					} else $result = $no_access;

				} elseif ($request["action"] == "rotate") {
					if (users::permitted("approve")) {
						$result = $ga->rotate();
					} else $result = $no_access;

				} elseif ($request["action"] == "youtube_upload" && $ga->current["filetype"] == "video" && !$ga->current["youtube"]) {
					if (users::permitted("youtube")) {
						$result = $ga->youtubeUpload();
					} else $result = $no_access;

				} elseif ($request["action"] == "youtube_upload" && $ga->current["filetype"] == "video" && $ga->current["youtube"] > "") {
					$result = '
							<h2>YouTube Upload</h2>
							<p>This video is on YouTube with the ID <code>'.$ga->current["youtube"].'</code>.</p>';

				} elseif ($request["action"] == "youtube_remove" && $ga->current["filetype"] == "video" && $ga->current["youtube"] > "") {
					if (users::permitted("youtube")) {
						$result = $ga->youtubeRemove();
					} else $result = $no_access;

				} elseif ($request["action"] == "youtube_remove" && $ga->current["filetype"] == "video" && !$ga->current["youtube"]) {
					$result = '
							<h2>Removed From YouTube</h2>
							<p>The video has previously been removed from YouTube.</p>';

				} elseif ($request["action"] == "session") {
					$ga->resetSession();
					$result = '
							<h2>Session Cleared</h2>
							<p>Session contents: '.var_export($_SESSION, true).'</p>';
				
				} else $result = '
						<h2>Submission Review</h2>';
						

			} else $result = '
						<h2>Submission Review</h2>';

			if		($ga->current["status"] == 0) { $status = "Unapproved"; }
			elseif	($ga->current["status"] == 1) { $status = "Approved"; }
			elseif	($ga->current["status"] == 2) { $status = "Denied"; }

			echo $result;

			if ($ga->display) {
	
				$uploadfile = 'uploads/'.$ga->current["product"].'/'.$ga->current["md5"].'.'.$ga->current["format"];
	
				echo '
						<div class="media_container">';
		
				if ($ga->current["filetype"] == "image") {

					echo '
							<img src="/images/400x300-scale/'.$uploadfile.($ga->current["rotate"] > 0 ? '?rotate='.$ga->current["rotate"] : '').'" alt="uploaded image" class="editing" />
							<div><a href="?id='.$ga->request["id"].'&md5='.$ga->request["md5"].'&action=rotate&rotate='.($ga->current["rotate"]+90).'" class="xxsmall hex-button">Rotate</a></div>';
	
				} elseif ($ga->current["filetype"] == "video") {

					echo '
							<video width="400" height="300" controls src="/'.$uploadfile.'" type="video/'.($ga->current["format"] == "mov" ? "quicktime" : $ga->current["format"]).'">
								<source src="/'.$uploadfile.'" type="video/'.$ga->current["format"].'" />
								Your browser does not support the video tag.
							</video>';
	
				}
				
				echo '
						</div>
						<table class="gallery_data">
							<tr><td class="label">Status:<td>'.$status.( $ga->current["status"] == 0 ? ' <a href="?id='.$ga->request["id"].'&md5='.$ga->request["md5"].'&action=approve">Approve</a>?' : '' ).'
							<tr><td class="label">Email:<td><a href="mailto:'.$ga->current["email"].'">'.$ga->current["email"].'</a>
							<tr><td class="label">Product:<td>'.$page->data["products"][$ga->current["product"]]["name"].'
							<tr>
								<td class="label valign">Product 2:
								<td>
									<form method="GET" class="quick_ajax_response">
										<input type="hidden" name="action" value="product2" />
										<select name="product2" id="product2">
											<option value="">None</option>';
				foreach ($page->data["products"] as $product) {
					if ($product["model"] != $ga->current["product"]) echo '
											<option value="'.$product["model"].($product["model"] == $ga->current["product2"] ? '" selected="selected' : '').'">'.$product["name"].'</option>';
				}
				echo '
										</select>
										<input type="hidden" name="id" value="'.$request["id"].'" />
										<input type="hidden" name="md5" value="'.$request["md5"].'" />
										<input type="submit" class="xxsmall hex-button save-product2" value="Save" />
									</form>';

				if ($page->data["products"][$ga->current["product"]]["commercial"] > 0 || (isset($ga->current["product2"]) && $page->data["products"][$ga->current["product2"]]["commercial"] > 0)) echo '
							<tr>
								<td class="label valign">Commercial
								<td>
									<form method="GET" class="quick_ajax_response">
										<input type="hidden" name="action" value="comm_exclude" />
										<label><input type="checkbox" name="comm_exclude" '.(isset($ga->current["comm_exclude"]) && $ga->current["comm_exclude"] == 1 ? ' checked="checked"' : '').' /> Exclude</label>
										<input type="hidden" name="id" value="'.$request["id"].'" />
										<input type="hidden" name="md5" value="'.$request["md5"].'" />
										<input type="submit" class="xxsmall hex-button save-commexclude" value="Save" />
									</form>';

				echo '
							<tr class="break"><td class="label">Credit:<td>'.$ga->current["credit"].'
							<tr><td class="label">Location:<td>'.$ga->current["location"].'
							<tr><td class="label">Caption:<td>'.$ga->current["caption"];

				if ($ga->current["status"] == 1 && $ga->current["filetype"] == "video") {
		
					if ($ga->current["youtube"]) {
						echo '
							<tr class="break"><td colspan="2">
								<div class="youtube_buttons">
									<a href="http://youtube.com/watch?v='.$ga->current["youtube"].'" target="_blank" class="hex-button xsmall">View</a> <a href="?id='.$ga->request["id"].'&md5='.$ga->request["md5"].'&action=youtube_remove" class="hex-button xsmall">Delete</a>
								</div>';

					} else echo '
							<tr class="break"><td colspan="2">
								<div class="youtube_buttons">
									<a href="?id='.$ga->request["id"].'&md5='.$ga->request["md5"].'&action=youtube_upload" class="hex-button xsmall">Upload</a>
								</div>';
	
				}

				if ($ga->current["change"]) {

					echo '
							<tr class="previous break"><td colspan="2">Previous Data
							<tr class="previous"><td class="label">Credit:<td>'.$ga->current["credit"].'
							<tr class="previous"><td class="label">Location:<td>'.$ga->current["location"].'
							<tr class="previous"><td class="label">Caption:<td>'.$ga->current["caption"];

				} elseif ($ga->current["credit"] != $ga->edit["credit"]
					|| $ga->current["location"] != $ga->edit["location"]
					|| $ga->current["caption"] != $ga->edit["caption"]) {

					echo '
							<tr class="updated break"><td colspan="2">Updated Data
							<tr class="updated"><td class="label">Credit:<td>'.$ga->edit["credit"].'
							<tr class="updated"><td class="label">Location:<td>'.$ga->edit["location"].'
							<tr class="updated"><td class="label">Caption:<td>'.$ga->edit["caption"].'
							<tr class="future break"><td colspan="2"><a href="?id='.$ga->request["id"].'&md5='.$ga->request["md5"].'&action=approve-captions">Approve information updates</a>?';

				}

				echo '
							<tr class="break">
								<td class="label">Tags:
								<td>
									<form method="GET">
										<input type="hidden" name="action" value="tags" />
										<textarea name="tags" id="gallery_tags">'.implode(",", $ga->tags).'</textarea>
										<input type="hidden" name="id" value="'.$request["id"].'" />
										<input type="hidden" name="md5" value="'.$request["md5"].'" />
										<input type="submit" class="xxsmall hex-button save-tags" value="Save Tags" />
									</form>
						</table>';

			}

			/*echo '
				<div style="clear:both; padding-bottom:30px;"></div>';*/

		} else {

			//require_once("inc/header.php");
			echo $page->header("Gallery Administration");

			echo '
						<h2>Error</h2>
						<p>Something went wrong.</p>';

		}
	
	/*

	USERS

	*/

	} elseif ($request["action"] == "users") {

		echo '
						<h2>Gallery Moderators</h2>';
		if (users::permitted("users")) {
			echo users::list_html();
		} else echo $no_access;

	} elseif ($request["action"] == "new_user" || $request["action"] == "user_new" || $request["action"] == "users/new") {

		echo '
						<h2>New Gallery Moderator</h2>';
		if (users::permitted("users")) {
			echo users::form("new user", array("destination" => "users"));
		} else echo $no_access;

	} elseif ($request["action"] == "user-details" || $request["action"] == "user_edit" || $request["action"] == "users/edit") {
		
		echo '
						<h2>Edit Gallery Moderator</h2>';
		if (isset($_REQUEST["id"]) && is_numeric($_REQUEST["id"])) {
			if (users::permitted("users") || $_REQUEST["id"] == users::me("id")) {
				echo users::form("edit", array("destination" => "users", "id" => $_REQUEST["id"]));
			} else echo $no_access;
		} else echo $no_access;

	} elseif ($request["action"] == "delete_user" || $request["action"] == "user_delete" || $request["action"] == "users/delete") {

		echo '
						<h2>Edit Gallery Moderator</h2>';
		if (users::permitted("users")) {
			echo users::form("delete", array("destination" => "users", "id" => $_REQUEST["id"]));
		} else echo $no_access;

	} elseif ($request["action"] == "me") {

		echo '
						<h2>Me</h2>';
		$user_id = users::me("id");
		include_once("inc/user-details.php");

	/*

	SYSTEM & SETTINGS

	*/

	} elseif ($request["action"] == "settings") {

		echo '
						<h2>Gallery Settings</h2>';
		if (users::me("admin")) {
		//	echo edi_options_page::display();
		} else echo $no_access;

	} elseif ($request["action"] == "log") {
	
		echo '
						<h2>Gallery Log</h2>';
		if (users::me("admin")) {
			$log_out = new _log();
			echo $log_out->latest_html();
		} else echo $no_access;

	} else {
		
		$total = 0;
		if ($request["action"] == "deny-all") {
			if (users::permitted("approve")) {
				foreach($_REQUEST["item"] as $item) {
					$ga = new galleryItem(["id" => $item]);
					$result = $ga->deny();
					$total++;
				}
				$result = '<p id="gallery_result">'.$total.' items denied.</p>';
			} else $result = $no_access;
		} elseif ($request["action"] == "commercial-exclude-all") {
			if (users::permitted("approve")) {
				foreach($_REQUEST["item"] as $item) {
					$ga = new galleryItem(["id" => $item]);
					$result = $ga->commExclude();
					$total++;
				}
				$result = '<p id="gallery_result">'.$total.' items removed from the Commercial Gallery.</p>';
			} else $result = $no_access;
		} elseif ($request["action"] == "approve-all") {
			if (users::permitted("approve")) {
				foreach($_REQUEST["item"] as $item) {
					$ga = new galleryItem(["id" => $item]);
					$result = $ga->approve();
					$total++;
				}
				$result = '<p id="gallery_result">'.$total.' items approved.</p>';
			} else $result = $no_access;
		} elseif ($request["action"] == "delete-all") {
			if (users::permitted("admin")) {
				foreach($_REQUEST["item"] as $item) {
					$ga = new galleryItem(["id" => $item]);
					if ($ga->current["status"] > 1) {
						$result = $ga->delete();
						$total++;
					}
				}
				$result = '<p id="gallery_result">'.$total.' items and files permanently deleted.</p>';
			} else $result = $no_access;
		}
		
		echo '
						<h2 class="nopad">Gallery Administration</h2>
						<ul class="text_menu">
							<li>'.((!isset($request["filter"]) || $request["filter"] == "approved")?'<span>Approved</span>':'<a href="?filter=approved">Approved</a>').'
							<li>'.((isset($request["filter"]) && $request["filter"] == "pending")?'<span>Pending</span>':'<a href="?filter=pending">Pending</a>').'
							<li>'.((isset($request["filter"]) && $request["filter"] == "denied")?'<span>Denied</span>':'<a href="?filter=denied">Denied</a>');
		if (users::permitted("admin")) echo '
							<li>'.((isset($request["filter"]) && $request["filter"] == "junk")?'<span>Junk</span>':'<a href="?filter=junk">Junk</a>');
		echo '
						</ul>
						<form action="'.APP_PATH.'" method="POST" class="ignore">';
		if (isset($_REQUEST["filter"])) echo '
							<input type="hidden" name="filter" value="'.$_REQUEST["filter"].'" />';
		echo '
							<div class="gallery_management">
								<a href="javascript:check_all();" class="button xsmall">Select All</a>
								<select name="action">
									<option>With selected...</option>';

		if (isset($request["filter"]) && $request["filter"] != "approved") echo '
									<option value="approve-all">Approve Selected</option>';
		if ($request["filter"] == "denied") {
			echo '
									<option value="delete-all">Delete Selected</option>';
		} else echo '
									<option value="deny-all">Deny Selected</option>';
		echo '
									<option value="commercial-exclude-all">Exclude from Commercial Gallery</option>';

		echo '
								</select>
								<input type="submit" value="Submit" class="hex-button xsmall" />
							</div>';
		if (isset($result)) echo '
							'.$result;
		echo $gallery->admin();
		echo $gallery->create($gallery->get(isset($request["filter"]) ? $request["filter"] : "approved"), $gallery_name).'
						</form>';
	
	}

} else {

	// occurs on auth failure
	// close out page after forms

}

echo '
					</div>
				</div>
			</section>'."\n";

/*echo '
				<div class="'.( isset($request["debug"]) ? '' : 'hidden' ).'">
					<section>
						<div class="column full">
							<div class="column_interior">
								<pre>'. var_export($_SESSION, true) .'</pre>
								<hr/>
								<pre>'. var_export($request, true) .'</pre>
							</div>
						</div>
					</section>
				</div>';
*/

echo '
		<div id="page_footer_before"></div>';

//require_once("inc/footer.php");
echo $page->footer();

$buffer = ob_get_contents();
ob_end_clean();
exit ($buffer);

?>