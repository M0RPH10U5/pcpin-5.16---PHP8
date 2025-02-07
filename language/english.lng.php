<?PHP
/* This is a language file for PCPIN Chat version 4.x */

/* Accept-Language (ISO 639).
 * Please visit http://www.oasis-open.org/cover/iso639a.html for your language */
$ISO_639_LNG = "en";

/* Determines, which charset will be used by browser to display the chat.
 * More info here: http://www.w3.org/International/O-charset-lang.html */
$lng = [
    "charset" => "UTF-8",

    // Normal messages
    "yes" => "Yes",
    "no" => "No",
    "cancel" => "Cancel",
    "closewindow" => "Close this window",
    "passchanged" => "Password changed",
    "changepass" => "Change password",
    "oldpass" => "Old password",
    "newpass" => "New password",
    "newpassagain" => "New password again",
    "confirmpass" => "Confirm password",
    "profile" => "Profile",
    "edituserprofile" => "Edit user profile: {USER}",
    "viewuserprofile" => "User profile: {USER}",
    "color" => "Color...",
    "nicknamecolor" => "Nickname color",
    "realname" => "Real name",
    "sex" => "Gender",
    "male" => "Male",
    "female" => "Female",
    "email" => "Email",
    "age" => "Age",
    "location" => "Location",
    "about" => "About",
    "savechanges" => "Save changes",
    "resetform" => "Reset form",
    "say" => "Say",
    "login" => "Login",
    "password" => "Password",
    "go" => "GO",
    "createroom" => "Create room",
    "logout" => "Logout",
    "roomname" => "Room name",
    "protectwithpass" => "Protect room with password?",
    "enterroompassword" => "Please enter the password for room '{ROOM}'",
    "memberlist" => "Member list",
    "username" => "Username",
    "joined" => "Joined",
    "online" => "Online",
    "status" => "Status",
    "invite" => "Invite",
    "admin" => "Admin",
    "design" => "Design",
    "chatdesign" => "Chat design",
    "settings" => "Settings",
    "chatsettings" => "Chat settings",
    "edit" => "Edit",
    "editusers" => "Edit users",
    "kick" => "Kick",
    "kickusers" => "Kick users",
    "ban" => "Ban",
    "banusersip" => "Ban users/IP addresses",
    "users" => "Users",
    "chat" => "Chat",
    "change" => "Change",
    "delete" => "Delete",
    "photoupload" => "Photo upload",
    "hideemail" => "Hide email address",
    "banlist" => "Ban list",
    "banip" => "Ban user's IP address",
    "banuser" => "Ban user",
    "bannedsince" => "Banned since",
    "de_activateall" => "de/activate all",
    "bannedusers" => "Banned users",
    "bannedips" => "Banned IP addresses",
    "ipaddress" => "IP address",
    "removeselectedfrombanlist" => "Remove selected from ban list",
    "globalmessages" => "Global messages",
    "globalmessageby" => "Global message from {USER}",
    "post" => "Post",
    "postglobalmessage" => "Post global message",
    "messagetype" => "Message type",
    "messagebody" => "Message body",
    "normal" => "Normal",
    "popup" => "Pop-Up",
    "advertisement" => "Advertisement",
    "add" => "Add",
    "addadvertisement" => "Add advertisement",
    "manageadvertisements" => "Manage advertisements",
    "advertisementtext" => "Advertisement text",
    "htmlallowed" => "HTML tags allowed",
    "htmlnotallowed" => "HTML tags not allowed",
    "start" => "Start",
    "stop" => "Stop",
    "yyyymmdd" => "YYYY.MM.DD",
    "hhmmss" => "HH:MM:SS",
    "date" => "Date",
    "time" => "Time",
    "period" => "Period",
    "minutes" => "Minutes",
    "minimumusersinroom" => "Only show in rooms with at least",
    "userssmall" => "users",
    "alsoshowinprivaterooms" => "Also show in private rooms",
    "save" => "Save",
    "advertisements" => "Advertisements",
    "smilies" => "Smilies",
    "managesmilies" => "Manage smilies",
    "check" => "Check",
    "addsmilie" => "Add smilie",
    "textequivalent" => "Text equivalent",
    "smilieimage" => "Smilie image file",
    "image" => "Image",
    "privatemessage" => "Private message",
    "whisper" => "Whisper",
    "talkprivateto" => "{USER}: private message",
    "badwords" => "Bad words",
    "managebadwords" => "Manage bad words",
    "addbadword" => "Add bad word",
    "badword" => "Bad word",
    "replacement" => "Replacement",
    "guest" => "Guest",
    "guestsonline" => "Guests online",
    "chatstatistics" => "Chat statistics",
    "statistics" => "Statistics",
    "registeredusers" => "Registered users",
    "registeredusersonline" => "Registered users online",
    "usersonline" => "Users online",
    "guests" => "Guests",
    "rooms" => "Rooms",
    "mainroomsnopass" => "Main rooms without password",
    "mainroomspass" => "Main rooms with password",
    "userroomsnopass" => "User rooms without password",
    "userroomspass" => "User rooms with password",
    "totalusersonline" => "Total users online",
    "totalrooms" => "Rooms total",
    "optimizedatabase" => "Optimize database tables",
    "registeredonly" => "registered users only",
    "inviteuser" => "Invite {USER} into your room?",
    "userinvited" => "{USER} was invited into your room",
    "youwereinvited" => "{USER} has invited you into room {ROOM}",
    "invitationaccepted" => "{USER} has accepted your invitation",
    "invitationrejected" => "{USER} has rejected your invitation",
    "acceptinvitation" => "Accept invitation",
    "rejectinvitation" => "Reject invitation",
    "mainrooms" => "Main rooms",
    "userrooms" => "User rooms",
    "confirmdeleteuser" => "Delete user '{USER}'?",
    "confirmkickuser" => "Kick user '{USER}'?",
    "privileges" => "Privileges",
    "manageprivileges" => "Manage privileges",
    "mute" => "Mute",
    "unmute" => "Unmute",
    "managerooms" => "Manage rooms",
    "manage" => "Manage",
    "confirmdeleteroom" => "Delete room '{ROOM}'?",
    "register" => "Register!",
    "lostpassword" => "I forgot my password!",
    "sendpassword" => "Send me my password!",
    "newpassword" => "New password",
    "activationsent" => "Account activation link has been sent to: {EMAIL}",
    "ok" => " OK ",
    "accountactivation" => "Account activation",
    "registration" => "Registration",
    "registrationsuccessfull" => "Registration successfull",
    "confirmemailsent" => "You need to confirm your registration. Check your email  for details.",
    "whisperto" => "whisper to {USER}",
    "whisperedtoyou" => "{USER} whispered to you",
    "youwhisperedto" => "You whispered to {USER}",
    "sayto" => "say to {USER}",
    "usersaidtouser" => "{USER1} said to {USER2}",
    "enter" => "Enter",

    // ERRORS
    "useralreadyloggedin" => "User {USER} is already logged in",
    "loginincorrect" => "Login incorrect",
    "oldpasswordincorrect" => "Old password is incorrect",
    "passwordsnotident" => "Passwords are not ident",
    "passwordlengthwrong" => "Password length must be between {MIN} and {MAX} characters",
    "loginlengthwrong" => "Login length must be between {MIN} and {MAX} characters",
    "passwordillegalchars" => "Password contains illegal characters",
    "emailempty" => "Email address empty",
    "emailinvalid" => "Invalid Email address",
    "roompassword" => "Room password",
    "roomnameempty" => "Roomname is empty",
    "wrongpassword" => "Wrong password",
    "roomalreadyexists" => "Room '{ROOM}' is already exists!",
    "noimageselected" => "Please select file to upload",
    "notanimage" => "Selected file is not an image",
    "filesizetoobig" => "File size is greater than {SIZE} byte.",
    "nobannedusers" => "There are no banned users",
    "nobannedips" => "There are no banned IP addresses",
    "youarebanned" => "You are banned from this chat",
    "ipbanned" => "Your IP address is banned from this chat",
    "textempty" => "Text is empty",
    "startdateinvalid" => "Invalid start date",
    "stopdateinvalid" => "Invalid stop date",
    "starttimeinvalid" => "Invalid start time",
    "stoptimeinvalid" => "Invalid stop time",
    "noadvertisementsfound" => "No advertisements found",
    "textequivalentempty" => "Text equivalent is empty",
    "uploaderror" => "File upload error",
    "equivalentexists" => "Smilie with this text equivalent already exists",
    "nosmiliesfound" => "No smilies found",
    "onlygifsallowed" => "Only GIF images are allowed",
    "invalidcharsintextequiv" => "Not allowed characters in text",
    "wordempty" => "Word is empty",
    "invalidcharsinword" => "Not allowed characters in word",
    "replacementempty" => "Replacement is empty",
    "badwordexists" => "Bad word already exists in database",
    "nobadwordsfound" => "No bad words found",
    "usernametaken" => "Username {USERNAME} is already taken",
    "emailtaken" => "EMail {EMAIL} is already taken",
    "backgroundimage" => "Background image",
    "invalidcharsinlogin" => "Login contains illegal characters",
    "loginempty" => "Login empty",
    "usernotfound" => "User '{USER}' with Email address '{EMAIL}' does not exists",
];

// Chat System Messages
$sysMsg = [
    0 => "User {USER} left this room",
    1 => "User {USER} Entered this room",
    2 => "User {USER} was kicked out",
    3 => "Timestamps turned on",
    4 => "Timestamps turned off",
    5 => "Sounds turned on",
    6 => "Sounds turned off",
    7 => "Ignore {USER}",
    8 => "Don't ignore {USER}"
];

// EMAIL TEMPLATES

// Lost password
$lng["email_lostpassword"] = "
Hello, {USER}!

Get new password here:
{URL}

Have lot of fun!

---------------------------
Best Regards,
{CHATOWNER}
";

// Registration email with account activation link
$lng["activateregistration"] = "
Hello, {USER}!

Your registration was successfull.
Your login: {USER}

Your account needs activation. To do that, please click here:
{ACTIVATIONURL}

Chat start page:
{CHATURL}

Have lot of fun!

---------------------------
Best Regards,
{CHATOWNER}
";

// Registration email without account activation link
$lng["instantregistration"] = "
Hello, {USER}!

Your registration was successfull.
Your login: {USER}
Your password: {PASSWORD}

Chat start page:
{CHATURL}

Have lot of fun!

---------------------------
Best Regards,
{CHATOWNER}
";
?>