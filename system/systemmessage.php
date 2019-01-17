<?php
// System
define('OPERATION_SUCCESSFULLY', '1000');
define('ERROR_REQUEST_INFORMATION', '1001');

// Common Message
define('ERROR_COMMON_NAME', '1100');
define('ERROR_COMMON_EXISTED', '1101');
define('ERROR_COMMON_EMPTY', '1102');
define('ERROR_COMMON_SPECIAL_CHARACTER', '1103');
define('ERROR_COMMON_INVALID', '1104');

// Login
define('ERROR_USERNAME_PASSWORD', '2100');
define('ERROR_ACCOUNT_LOCKED', '2101');
define('ERROR_USERNAME', '2102');
define('ERROR_USERNAME_SPECIAL_CHARACTER', '2103');
define('ERROR_USERNAME_EMPTY', '2104');
define('ERROR_PASSWORD', '2105');
define('ERROR_PASSWORD_SPECIAL_CHARACTER', '2106');
define('ERROR_PASSWORD_EMPTY', '2107');

// User
define('ERROR_USERNAME_OR_EMAIL_EXISTED', '2120');
define('ERROR_USER_FIRST_NAME', '2121');
define('ERROR_USER_FIRST_NAME_SPECIAL_CHARACTER', '2122');
define('ERROR_USER_FIRST_NAME_EMPTY', '2123');
define('ERROR_USER_LAST_NAME', '2124');
define('ERROR_USER_LAST_NAME_SPECIAL_CHARACTER', '2125');
define('ERROR_USER_LAST_NAME_EMPTY', '2126');
define('ERROR_USER_EMAIL', '2127');
define('ERROR_USER_EMAIL_INVALID', '2128');
define('ERROR_USER_EMAIL_EMPTY', '2129');
define('ERROR_USER_EMAIL_SPECIAL_CHARACTER', '2130');
define('ERROR_USER_FULL_NAME_SPECIAL_CHARACTER', '2131');

// User Group
define('ERROR_USER_GROUP_NAME_EXISTED', '2140');
define('ERROR_USER_GROUP_NAME', '2141');
define('ERROR_USER_GROUP_NAME_SPECIAL_CHARACTER', '2142');
define('ERROR_USER_GROUP_NAME_EMPTY', '2143');
define('ERROR_USER_GROUP_NOT_EMPTY', '2144');

// Admin Side Menu
define('ERROR_MENU_NAME_EXISTED', '2150');
define('ERROR_MENU_NAME', '2151');
define('ERROR_MENU_NAME_SPECIAL_CHARACTER', '2152');
define('ERROR_MENU_NAME_EMPTY', '2153');

// System Setting
define('ERROR_SETTING_NOT_SET', '2160');

$_SYSTEM_MESSAGE = array(
    OPERATION_SUCCESSFULLY      => 'Operation Successfully!',
    ERROR_REQUEST_INFORMATION   => 'Request Information Error!',

    ERROR_USERNAME_PASSWORD     => 'Incorrect username or password!',
    ERROR_ACCOUNT_LOCKED        => 'Account has been locked!',

    ERROR_COMMON_NAME       => '%s has errors!',
    ERROR_COMMON_EXISTED    => '%s has been existed!',
    ERROR_COMMON_EMPTY      => '%s could not be empty',
    ERROR_COMMON_SPECIAL_CHARACTER      => '%s could not contain special characters!',
    ERROR_COMMON_INVALID    => '%s is invalid!',

    ERROR_USER_GROUP_NOT_EMPTY      => 'There are some users belong to this user group.',

    ERROR_SETTING_NOT_SET   => 'This setting has not been set before!',
);

class SystemMessage {
    public function getSystemMessage($id) {
        global $_SYSTEM_MESSAGE;

        return $_SYSTEM_MESSAGE[$id];
    }
}
