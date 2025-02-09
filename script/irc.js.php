//<script>
  /**************************************************************************
  FUNCTION execIRC
  ---------------------------------------------------------------------------
  Task:
    Execute IRC commands
  ---------------------------------------------------------------------------
  Parameters:
    message         string        Message body
    in_mainframe    boolean       If true, then called from main chat frame
    user_id         int           Caller's User ID
    user_name       string        Caller's Username
  ---------------------------------------------------------------------------
  Return:
    Message string
  **************************************************************************/
  function execIRC(message, in_mainframe, user_id, user_name){
    try{
      message = trimString(message);
      var message_parts = message.split(" ");
      var user_nr = -1;
      if (in_mainframe && message_parts[0] == '/kick') {
        // "/kick" command
        if (message_parts.length > 1) {
          var kicked_name = '';
          // Create user name
          for (var ii = 1; ii < message_parts.length; ii++) {
            if (ii > 1) {
              kicked_name += ' ';
            }
            kicked_name += message_parts[ii];
          }
          // Look for user
          user_nr = findUserByName(kicked_name);
          if (user_nr == -1) {
            // User not found. Try case-insensitive search
            user_nr = findUserByName(kicked_name, 1);
          }
          if (user_nr >= 0 && user[user_nr].ID != user_id) {
            // User found. Kick him.
            dummyform.location.href = 'main.php?session_id=' + encodeURIComponent(session_id) + '&include=19&profile_user_id=' + encodeURIComponent(user[user_nr].ID) + '&dummy=1';
          }
        }
      } else if (in_mainframe && message_parts[0] == '/clear') {
        // Clear screen
        initMsgFrame();
        if (message_parts[1]) {
          if (message_parts[1] == 'room') {
            // Clear all room users' screens
            dummyform.location.href = 'main.php?session_id=' + encodeURIComponent(session_id) + '&include=40&clear_room_id=' + encodeURIComponent(this_room_id);
          } else if (message_parts[1] == 'all') {
            // Clear all chat users' screens
            dummyform.location.href = 'main.php?session_id=' + encodeURIComponent(session_id) + '&include=40&clear_room_id=0';
          }
        }
        return '';
      } else if (in_mainframe && message_parts[0] == '/quit') {
        // Exit chat
        logout(this_user_id);
        return '';
      } else {
        // Look for "/me" command
        if (-1 != message.indexOf('/me')) {
          for (var i = 0; i < message_parts.length; i++) {
            if (message_parts[i].toLowerCase() == '/me') {
              message_parts[i] = user_name;
            }
          }
        }
      }
      return message_parts.join(' ');
    } catch (e) {}
    return message;
  }
