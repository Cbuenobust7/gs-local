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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'gs-local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Lsd/P]o+Mm[^eNv08kTc?$$uCRV0Zb!L,KR^*5w<>mlr.ryB1/~_2#(|T:6N+j*O' );
define( 'SECURE_AUTH_KEY',  'sfC2Ig&&2pw&E]oww?-iJ^c8hp~0Byi*dRuw[nI?N`W9y)yp|ZMh?7cTYkCsNVua' );
define( 'LOGGED_IN_KEY',    'X6ts~ktdxD1`(usS?X{0OE$KJ!yf-L1kv46P@LD--4A$.%Y`xs}/d7AJzTsVcJN$' );
define( 'NONCE_KEY',        'ru?Nx@Y #Ek,Hd!|x`e_h>TkW;iI!{lrkh>nRtvQ:Z,tv8}?*F[L<qiV@Zj`y,Qb' );
define( 'AUTH_SALT',        '=VPrLX}~$LqH*>n5#<KXX*$>6_X{YVRR$Caw%7|JTN-B)ji2AkFC4/CCC%mE!O$J' );
define( 'SECURE_AUTH_SALT', 'Wo!9:AM!7E?k8E78Y _}mP7~Cn l7 xL.iz8eZTf,4n0@2,7Yb-F+elqIo}RhlSZ' );
define( 'LOGGED_IN_SALT',   '$0B0&Y*4sj<g mpQT!]%:;vODXIe/ACNvv-4WfuGe5GFEa+z gFLVw#qmW^u=||z' );
define( 'NONCE_SALT',       'piKI/`8=hHG*|W:YfD%w057VYTX3+u<.:J<EW(zJ0P]*ZYOz3VRVJxX!P7vQa3xg' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
