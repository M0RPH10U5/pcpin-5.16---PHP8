<?php
/* This is a PCPIN Chat interface file */

/* Call
interface.php?t=TYPE&list_type=LIST_TYPE

TYPE - see SWITCH() below
LIST_TYPE - Comma-separated list (1) or formatted with HTML <li> tags (2)
*/


/* Offset */
define('offset', './');

/* Load Configuration */
require './config/config.inc.php';

/* Execute global actions and load classes */
require './config/prepend.inc.php';

/* Load Database Connection Settings */
include './config/db.inc.php';

/* Creating Session */
$session_id = '';
$session = new session($session_id);

/* Deleting Old Sessions */
$session -> cleanup();

/* Update Max Users Online Counters */
$maxusers = new maxusers($session);

if (!isset($list_type)){
    $list_type = '';
}

if (!isset($t)){
    $t = null;
}
switch($t){
    CASE 1 :  /* Show Total Online Users Count */
              echo $session -> countRoomUsers();
              break;
    CASE 2 :  /* Show Total Rooms Count */
              $room = new room();
              $room -> listRooms($session);
              echo count($room -> roomlist);
              break;
    CASE 3 :  /* Show Online Users List */
              $userlist = $session -> listRoomUsers();
              $userlist_count = count($userlist);
              $user = new user();
              $users = [];
              for ($i = 0; $i < $userlist_count; $i++){
                $user -> readUser($session, $userlist[$i]['user_id']);
                $users[] = $user -> login;
              }
              showList($users);
              break;
    CASE 4 :  /* Show Colored Online Users List */
              $userlist = $session -> listRoomUsers();
              $userlist_count = count($userlist);
              $user = new user();
              $users = [];
              for ($i = 0; $i < $userlist_count; $i++){
                $user -> readUser($session, $userlist[$i]['user_id']);
                $users[] = '<font color="#' . $user -> color . '">' . htmlspecialchars($user -> login, ENT_QUOTES, 'UTF-8') . '</font>';
              }
              showList($users);
              break;
    CASE 5 :  /* Show Rooms List */
              $room = new room();
              $room -> listRooms($session);
              $roomlist = $room -> roomlist;
              $roomlist_count = count($roomlist);
              $rooms = [];
              for ($i = 0; $i < $roomlist_count; $i++){
                $rooms[] = $roomlist[$i]['name'];
              }
              showList($rooms);
              break;
    CASE 6 :  /* Show Rooms With Online Users Count List */
              $room = new room();
              $room -> listRooms($session);
              $roomlist = $room -> roomlist;
              $roomlist_count = count($roomlist);
              $rooms = [];
              for ($i = 0; $i < $roomlist_count; $i++){
                $rooms[] = $roomlist[$i]['name'] . ' (' . $session -> countRoomUsers($roomlist[$i]['id']) . ')';                
              }
              showList($rooms);
              break;
    CASE 7 :  /* Show Rooms with Online Usernames List */
              $room = new room();
              $user = new user();
              $room -> listRooms($session);
              $roomlist = $room -> roomlist;
              $roomlist_count = count($roomlist);
              $rooms = [];
              for ($i = 0; $i < $roomlist_count; $i++){
                $users = [];
                $roomusers = $session -> listRoomUsers($roomlist[$i]['id']);
                $roomusers_count = count($roomusers);
                for ($ii = 0; $ii < $roomusers_count; $ii++){
                    $user -> readUser($session, $roomusers[$ii]['user_id']);
                    $users[] = htmlspecialchars($user -> login, ENT_QUOTES, 'UTF-8');
                }
                $rooms[] = $roomlist[$i]['name'] . ' (' . implode(', ', $users) . ')';
              }
              showList($rooms);
              break;
    CASE 8 :  /* Show Rooms With Colored Online User Names List */
                 $room = new room();
                 $user = new user();
                 $roomlist = $room -> roomlist;
                 $roomlist_count = count($roomlist);
                 $rooms = [];
                 for ($i=0; $i < $roomlist_count; $i++){
                    $users = [];
                    $roomusers = $session -> listRoomUsers($roomlist[$i]['id']);
                    $roomusers_count = count($roomusers);
                    for ($ii = 0; $ii < $roomusers_count; $ii++){
                        $user -> readUser($session, $roomusers[$ii]['user_id']);
                        $users[] = '<font conlor="#' . $user -> color . '">' . htmlspecialchars($user -> login, ENT_QUOTES, 'UTF-8') . '</font>';
                    }
                    $rooms[] = $roomlist[$i]['name'] . ' (' . implode(', ', $users) . ')';
                 }
                 showList($rooms);
                 break;       
}

exit();

function showList($list){
    global $list_type;
    if (is_array($list) && count($list)){
        switch($list_type){
            DEFAULT     :   // DEFAULT: Comma-separated list
                            echo implode(', ', $lsit);
                            break;
            CASE 'list' :   // Format list using <li> tags
                            echo '<li>' . implode('</li><li>', $list) . '</li';
                            break;
        }
    }
}
?>