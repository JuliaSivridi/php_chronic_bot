<?php
require_once 'cnct_chronic_bot.php';
require_once 'clas_chronic_bot.php';
require_once 'lang_chronic_bot.php';
date_default_timezone_set( 'Europe/Moscow' );

// send telegram request
function trequest($method, $inputarray) {
    global $bottoken;
    $inputstring = http_build_query($inputarray, null, '&', PHP_QUERY_RFC3986);
    $options = ['http' => ['method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded\r\n',
            'ignore_errors' => true,
            'content' => $inputstring]];
    $request='https://api.telegram.org/bot'.$bottoken.'/'.$method;
    $context = stream_context_create($options);
    $answer = file_get_contents($request, false, $context);
    return $answer;
}

// get user from db
function select_user($dblink, $tbname, $chat_id) {
	$query_usr = "select * from ".$tbname." where chat_id='".$chat_id."'";
	$result_usr = mysqli_query($dblink, $query_usr);
    return $result_usr;
}

// update something in db
function update_data($dblink, $tbname, $chat_id, $field_name, $data) {
	$data = (is_array($data)) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
	$query_upd = "update ".$tbname." set 
		".$field_name."='".$data."' 
		where chat_id='".$chat_id."'";
	$result_upd = mysqli_query($dblink, $query_upd);
}

// menu keyboard
function draw_menu($lang_ul, $type) {
	switch ($type) {
		case 'main': return $kbd = [[$lang_ul['menu-chus'], $lang_ul['menu-res']], 
									[$lang_ul['menu-set'], $lang_ul['menu-hlp']]]; break;
		case  'set': return $kbd = [[$lang_ul['menu-list'], $lang_ul['menu-lang']], 
									[$lang_ul['main-back']]]; break;
		case 'list': return $kbd = [[$lang_ul['list-add'], $lang_ul['list-view'], $lang_ul['list-del']], 
									[$lang_ul['set-back']]]; break;
	}
}

// chronik keyboard
function draw_list($lang_ul, $user_list) {
	$kbd = []; $line = [];
	foreach ($user_list as $chronic) {
		$len = mb_strlen($chronic);
		$maxbtn = $len > 25 ? 1 : ($len > 10 ? 2 : 3);
		if ($maxbtn == 1) {
			$kbd[] = [['text' => $chronic]];
		} else {
			$line[] = ['text' => $chronic];
		}
		if (count($line) >= $maxbtn) {
			if (!empty($line)) $kbd[] = $line;
			$line = [];
		}
	} if (!empty($line)) $kbd[] = $line;
	$kbd[] = [$lang_ul['main-back']];
	return $kbd;
}

// get user request
$content = file_get_contents('php://input');
$input = json_decode($content, TRUE);
$dblink = mysqli_connect($dbhost, $dbuser, $dbpswd, $dbname);

// user send msg
if (($input['message']) != null) {
	$chat_id = $input['message']['chat']['id'];
	$user_lang = $input['message']['from']['language_code'];
	$msg_id = $input['message']['message_id'];
	$user_msg = trim($input['message']['text']);

	$result_usr = select_user($dblink, $tbname, $chat_id);
	// user new -> insert to db
	if (mysqli_num_rows($result_usr) <= 0) {
		$ul = (array_key_exists($user_lang, $lang)) ? $user_lang : 'en';
		$constantlist = ['☀️ Wake up', '🛏 Sleep', '🚿 Shower', '🍳 Breakfast', '🍜 Lunch', '🍝 Dinner', '🫖 Tee', '☕️ Coffee', 
		'🖥 Working', '🎮 Gaming', '🎬 Movie', '🎵 Music', '🚶 Walking', '🧹 Cleaning', '🪴 Watering plants'];
		$query_ins = "insert into ".$tbname." (chat_id, user_lang, user_name, user_list) values ('".$chat_id."', '".$ul."', 
			'".$input['message']['from']['first_name']." ".$input['message']['from']['last_name']."',
			'".json_encode($constantlist, JSON_UNESCAPED_UNICODE)."')";
		$result_ins = mysqli_query($dblink, $query_ins);
		$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['hi1'].$input['message']['from']['first_name'].$lang[$ul]['hi2'], 
			'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'main'), 'resize_keyboard' => true])]);

	// user exists
	} else {
		$row = mysqli_fetch_assoc($result_usr);
		$user_lang = $row['user_lang'];
		$ul = (array_key_exists($user_lang, $lang)) ? $user_lang : 'en';
		$lkeys = array_keys($lang); $flag_lang = [];
		foreach ($lkeys as $lkey) $flag_lang[] = $flags[$lkey].' '.$lkey;
		$user_list = json_decode($row['user_list'], false, 512, JSON_UNESCAPED_UNICODE);
		$user_day = json_decode($row['user_day'], false, 512, JSON_UNESCAPED_UNICODE);

		switch ($user_msg) {
			// basic functionality {
			// main menu
			case '/main': case $lang[$ul]['main-back']: {
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['main-ttl'], 
					'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'main'), 'resize_keyboard' => true])]);
				break;
			}

			// main menu -> help
			case '/help': case $lang[$ul]['menu-hlp']: {
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['help'], 'parse_mode' => 'Markdown', 
					'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'main'), 'resize_keyboard' => true])]);
				break;
			}

			// main menu -> settings
			case '/settings': case $lang[$ul]['menu-set']: case $lang[$ul]['set-back']: {
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['set-ttl'], 
					'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'set'), 'resize_keyboard' => true])]);
				break;
			}

			// settings menu -> language ask
			case '/lang': case $lang[$ul]['menu-lang']: {
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['lang-ask'], 
					'reply_markup' => json_encode(['keyboard' => [$flag_lang, [$lang[$ul]['set-back']]], 'resize_keyboard' => true])]);
				break;
			} // settings menu -> language set
			case in_array($user_msg, $flag_lang): {
				$l = explode(' ', $user_msg); $ul = $l[1];
				update_data($dblink, $tbname, $chat_id, 'user_lang', $ul);
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['lang-ok'], 
					'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'set'), 'resize_keyboard' => true])]);
				break;
			}
			// basic functionality }

			// settings menu -> chronic list
			case $lang[$ul]['menu-list']: case $lang[$ul]['list-view']: {
				$result = $lang[$ul]['list-ttl']."\n";
				foreach ($user_list as $chronic)
					$result .= "\n".$chronic;
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $result, 'parse_mode' => 'Markdown', 
					'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'list'), 'resize_keyboard' => true])]);
				break;
			}
			// chronic list menu -> add new
			case $lang[$ul]['list-add']: {
				update_data($dblink, $tbname, $chat_id, 'flag', 'listadd');
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['list-add-ttl'], 
					'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'list'), 'resize_keyboard' => true])]);
				break;
			}
			// chronic list menu -> delete
			case $lang[$ul]['list-del']: {
				update_data($dblink, $tbname, $chat_id, 'flag', 'listdel');
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['list-del-ttl'], 
					'reply_markup' => json_encode(['keyboard' => draw_list($lang[$ul], $user_list), 'resize_keyboard' => true])]);
				break;
			}

			// main menu -> choose chronic
			case $lang[$ul]['menu-chus']: {
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['chus-ttl'], 
					'reply_markup' => json_encode(['keyboard' => draw_list($lang[$ul], $user_list), 'resize_keyboard' => true])]);
				break;
			}

			// main menu -> show result
			case $lang[$ul]['menu-res']: {
				if (!empty($user_day)) {
					$result = $lang[$ul]['res-ttl'].' '.date('d.m.Y', $user_day[0]->ctime);
					foreach ($user_day as $chronic) {
						$result .= "\n*".date('H:i', $chronic->ctime)."* ".$chronic->chronic;
						if (!empty($chronic->comment)) $result .= ". ".$chronic->comment;
					}
				} else { $result = $lang[$ul]['res-ttl'].$lang[$ul]['res-empty']; }
				$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $result, 'parse_mode' => 'Markdown', 
					'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'main'), 'resize_keyboard' => true])]);
				update_data($dblink, $tbname, $chat_id, 'user_day', []);
				break;
			}

			// msg -> add/del list item OR new chronic/comment
			default:
				switch ($row['flag']) {
					case 'listadd': { // chronic list -> add new
						$user_list[] = $user_msg;
						update_data($dblink, $tbname, $chat_id, 'user_list', $user_list);
						$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['chronic-added'], 
							'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'list'), 'resize_keyboard' => true])]);
						update_data($dblink, $tbname, $chat_id, 'flag', null);
						break;
					}
					case 'listdel': { // chronic list -> delete
						if (($key = array_search($user_msg, $user_list)) !== false) {
							// unset($user_list[$key]); $user_list = array_values($user_list);
							$user_list = array_values(array_diff($user_list, [$user_msg]));
							update_data($dblink, $tbname, $chat_id, 'user_list', $user_list);
							$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['chronic-deleted'], 
								'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'list'), 'resize_keyboard' => true])]);
						} else {
							$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['chronic-notfound'], 
								'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'list'), 'resize_keyboard' => true])]);
						} update_data($dblink, $tbname, $chat_id, 'flag', null);
						break;
					}
					default: {
				        $reply_id = $input['message']['reply_to_message']['message_id'];
						if (isset($reply_id)) { // add comment to chronic
							$found_item = array_filter($user_day, function($chronic) use ($reply_id) {
								return $chronic->msg_id === $reply_id;
							});
							if (!empty($found_item)) {
								$matched_object = reset($found_item);
								$matched_object->comment = $matched_object->comment.$user_msg.' ';
								update_data($dblink, $tbname, $chat_id, 'user_day', $user_day);
								$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['comment-saved'], 
									'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'main'), 'resize_keyboard' => true])]);
							} else {
								$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['chronic-notfound'], 
									'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'main'), 'resize_keyboard' => true])]);
							}
						} else { // add new chronic
							$user_day[] = new Cchronic($msg_id, $user_msg);
							update_data($dblink, $tbname, $chat_id, 'user_day', $user_day);
							$answer = trequest('sendMessage', ['chat_id' => $chat_id, 'text' => $lang[$ul]['chronic-saved'], 
								'reply_markup' => json_encode(['keyboard' => draw_menu($lang[$ul], 'main'), 'resize_keyboard' => true])]);
						}
					}
				}
		}
	} mysqli_free_result($result_usr);

// user press button
} else if ($input['callback_query'] != null) {
	// $cb_id = $input['callback_query']['id'];
	// $chat_id = $input['callback_query']['message']['chat']['id'];
	// $cb_data = $input['callback_query']['data'];
	// switch off clock on button
	// $answer = trequest('answerCallbackQuery', array('callback_query_id' => $cb_id));

	// if ($cb_data != '-') {
		// $result_usr = select_user($dblink, $tbname, $chat_id);
		// $row = mysqli_fetch_assoc($result_usr);
		// $msg_id = $row['msg_id'];
		// $user_lang = $row['user_lang'];
		// $ul = (array_key_exists($user_lang, $lang)) ? $user_lang : 'ru';
		// mysqli_free_result($result_usr);
	// }
} mysqli_close($dblink); ?>