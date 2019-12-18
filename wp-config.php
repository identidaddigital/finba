<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'i4179195_wp1');

/** MySQL database username */
define('DB_USER', 'i4179195_wp1');

/** MySQL database password */
define('DB_PASSWORD', 'A.LyAjIs(L@cEh6eRS]06((7');

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
define('AUTH_KEY',         'uFXac136hVkXVBWaFMVHkgyC108XKxOwcWIYOAn10zogDGYV9hak5BKX61sRLTNE');
define('SECURE_AUTH_KEY',  'bZwv5r4UXjkaLVC7GcOifc01CoZxkgtHWhQGbLoq0nx6WMyhxJGOty20PbU3nM7I');
define('LOGGED_IN_KEY',    'ECfZ16qMg2pp4NCRZxvE1breQx2pa53lpqfac0wxNzRXCzzVNbbUxyIqXsINUel4');
define('NONCE_KEY',        'vM25vquGCOqasNwejTMiaWEUQFvm0edkjNYuRTt91ANmkKCdNmtTXXgAU8vgwpr3');
define('AUTH_SALT',        '1zHBcp2dqXgtBJHu3LuEJ30npA5rghIvNTcscOyB2yvDIMje4PY05QLOh5cqxvsz');
define('SECURE_AUTH_SALT', 'F5Hc8DI6fjKiMk8afFoSA3w5JkiXLqB7JCOOOurkvCwwa1BQYME2e9baeoEaH9QB');
define('LOGGED_IN_SALT',   'AF7K5bM5vbjtKwHiZDiufJPSQOosXcSTvzt5tWUF9ZziqBu8dzpQxewgbq2MRxc6');
define('NONCE_SALT',       '59vC0brdlOMRonymb03nttivtLdAQs1kTtIDLOXLlKlqgB9bAL3gajITiOxd3eT1');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');define('FS_CHMOD_DIR',0755);define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed upstream.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
