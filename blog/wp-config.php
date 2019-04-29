<?php

/**
* The base configurations of the WordPress.
*
* This file has the following configurations: MySQL settings, Table Prefix,
* Secret Keys, WordPress Language, and ABSPATH. You can find more information
* by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
* wp-config.php} Codex page. You can get the MySQL settings from your web host.
*
* This file is used by the wp-config.php creation script during the
* installation. You don't have to use the web site, you can just copy this file
* to "wp-config.php" and fill in the values.
*
* @package WordPress
*/

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'concrfe5_ghostshield2015');

/** MySQL database username */
define('DB_USER', 'concrfe5_gs2015a');

/** MySQL database password */
define('DB_PASSWORD', '970d8s7f80!h');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
* Authentication Unique Keys and Salts.
*
* Change these to different unique phrases!
* You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
* You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
*
* @since 2.6.0
*/
define('AUTH_KEY', 'V4JeOGyvxPTyp8IRRpNk4lt4L8T8oBOCksPI3ce4H0v8hhGZ0mpjlAANFVkfeze/');
define('SECURE_AUTH_KEY', '5Q7Ij4fi6zW3i/JRYAj/tmIYtIzpWVfLuNIq7OVgoQIOGhjQyrh8AzuYwaDX8cY+');
define('LOGGED_IN_KEY', '/rUO7XFt5bHzYC+krSugn4VSwPewWwjMGTkLuzacDvsmhql1nbVrs6ZfKEdSG6f5');
define('NONCE_KEY', 'o6a4mEB19naoVs5Wc9irnMD0aRcOV8jaBeNo0oDnojCLHfD7f5PPbIy8WKYS1xdX');
define('AUTH_SALT', 'H2V5F0fRa6W7ScegY0Cdd0MdcY7VRtp5z41VQgYaT5qvcFmJBlT5b0mwYSJruBiB');
define('SECURE_AUTH_SALT', '3y2onfploiQWAZjqnz3wFBn2gViu+GZ4ATizFLVekTO/hMbYro2iJchWw2KtGNsw');
define('LOGGED_IN_SALT', 'EpcQqVBGdRRp4yPmR74EiIiRjJBk2kNP2KHS1nDaLjLXYxAgcSFCBOEGUAkpVacx');
define('NONCE_SALT', 'oNbC7ub5oiIq+2FX2Mpor/sLLOWQ83zKeCu3lv0BGOpzLPhlilHv9qoVAO15gdim');

/**#@-*/

/**
* WordPress Database Table prefix.
*
* You can have multiple installations in one database if you give each a unique
* prefix. Only numbers, letters, and underscores please!
*/
$table_prefix = 'msr6l79_';

/**
* WordPress Localized Language, defaults to English.
*
* Change this to localize WordPress. A corresponding MO file for the chosen
* language must be installed to wp-content/languages. For example, install
* de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
* language support.
*/
define('WPLANG', '');

/**
* For developers: WordPress debugging mode.
*
* Change this to true to enable the display of notices during development.
* It is strongly recommended that plugin and theme developers use WP_DEBUG
* in their development environments.
*/
define('WP_DEBUG', false);
define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

