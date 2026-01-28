<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать файл в "wp-config.php"
 * и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры базы данных: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'test' );

/** Имя пользователя базы данных */
define( 'DB_USER', 'root' );

/** Пароль к базе данных */
define( 'DB_PASSWORD', '' );

/** Имя сервера базы данных */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу. Можно сгенерировать их с помощью
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}.
 *
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными.
 * Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'e6FQh}~.&2w$(4Lx);!pd,~CD0J~zX`M~pwnJ&}{[];$pCqs16MR1zl?PGwOS?[D' );
define( 'SECURE_AUTH_KEY',  '>Q(nWQCgYH0^&=9xCx8[(^Tf!,>2N7fmlL_$YHlA-qkyZ|j[&>E|;:=3I_{rb0@p' );
define( 'LOGGED_IN_KEY',    ')NdwMw|C|) 7yZ`&xxn*FP<z@[L)JUDolR*_LtBllvz?Qk(1@__aEHh7?W+bO`8b' );
define( 'NONCE_KEY',        'VPA2N&N|jUy#1H^{bx1T@,CUb?R,+mFVuacGHA:.wd|M)?aF.X2Wa.#~4<McZ9-:' );
define( 'AUTH_SALT',        '[ogjHm^KPyn?W2Z_YxKf8AS*LNFF{44Kx(OJcKzi^OP_v![pBIgMcRxtfp8PYA9$' );
define( 'SECURE_AUTH_SALT', ';gz/U8bh5h5}QU??iiDhhVef1S) 95{Ll%/ke=_SzM!*v@RBwX17x*!lHevo%n$[' );
define( 'LOGGED_IN_SALT',   '3mH%Y)S5^-I&BVN1,i)|6vXK[zzsDB-ulLU[Mf$-?f|=AkQKNvkylG|#!wYk0DZ<' );
define( 'NONCE_SALT',       '^(f8:|Jhq5u0sGJ>@%/0RHQrs).Er_A<6;alDN$^87!=T|cE&`Wf~kY&OJ|2ZQ-]' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Произвольные значения добавляйте между этой строкой и надписью "дальше не редактируем". */



/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';
