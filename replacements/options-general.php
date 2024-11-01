<?php
/**
 * General settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once(  ABSPATH . 'wp-admin/admin.php' );

/** WordPress Translation Install API */
require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

$title = __('General Settings');
$parent_file = 'options-general.php';
/* translators: date and time format for exact current time, mainly about timezones, see http://php.net/date */
$timezone_format = _x('Y-m-d H:i:s', 'timezone date format');

if ( get_site_option( 'initial_db_version' ) >= 32453  ) { ?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		var section = $('#front-static-pages'),
			staticPage = section.find('input:radio[value="page"]'),
			selects = section.find('select'),
			check_disabled = function(){
				selects.prop( 'disabled', ! staticPage.prop('checked') );
			};
		check_disabled();
 		section.find('input:radio').change(check_disabled);
	});
</script>
<?php }

add_action('admin_head', 'options_general_add_js');

$options_help = '<p>' . __('The fields on this screen determine some of the basics of your site setup.') . '</p>' .
	'<p>' . __('Most themes display the site title at the top of every page, in the title bar of the browser, and as the identifying name for syndicated feeds. The tagline is also displayed by many themes.') . '</p>';

if ( ! is_multisite() ) {
	$options_help .= '<p>' . __('The WordPress URL and the Site URL can be the same (example.com) or different; for example, having the WordPress core files (example.com/wordpress) in a subdirectory instead of the root directory.') . '</p>' .
		'<p>' . __('If you want site visitors to be able to register themselves, as opposed to by the site administrator, check the membership box. A default user role can be set for all new users, whether self-registered or registered by the site admin.') . '</p>';
}

$options_help .= '<p>' . __( 'You can set the language, and the translation files will be automatically downloaded and installed (available if your filesystem is writable).' ) . '</p>' .
	'<p>' . __( 'UTC means Coordinated Universal Time.' ) . '</p>' .
	'<p>' . __( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.' ) . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => $options_help,
) );

if ( get_site_option( 'initial_db_version' ) >= 32453  ) {
get_current_screen()->add_help_tab( array(
	'id'      => 'site-visibility',
	'title'   => has_action( 'blog_privacy_selector' ) ? __( 'Site Visibility' ) : __( 'Search Engine Visibility' ),
	'content' => '<p>' . __( 'You can choose whether or not your site will be crawled by robots, ping services, and spiders. If you want those services to ignore your site, click the checkbox next to &#8220;Discourage search engines from indexing this site&#8221; and click the Save Changes button at the bottom of the screen. Note that your privacy is not complete; your site is still visible on the web.' ) . '</p>' .
		'<p>' . __( 'When this setting is in effect, a reminder is shown in the At a Glance box of the Dashboard that says, &#8220;Search Engines Discouraged,&#8221; to remind you that your site is not being crawled.' ) . '</p>',
) );
/** This filter is documented in wp-admin/options-general.php */
if ( apply_filters( 'enable_update_services_configuration', true ) ) {
	get_current_screen()->add_help_tab( array(
		'id'      => 'options-services',
		'title'   => __( 'Update Services' ),
		'content' => '<p>' . __( 'If desired, WordPress will automatically alert various services of your new posts.' ) . '</p>',
	) );
}
}

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Settings_General_Screen" target="_blank">Documentation on General Settings</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include( ABSPATH . 'wp-admin/admin-header.php' );
?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<form method="post" action="options.php" novalidate="novalidate">
<?php settings_fields('general'); ?>

<table class="form-table">
<tr>
<th scope="row"><label for="blogname"><?php _e('Site Title') ?></label></th>
<td><input name="blogname" type="text" id="blogname" value="<?php form_option('blogname'); ?>" class="regular-text" /></td>
</tr>
<tr>
<th scope="row"><label for="blogdescription"><?php _e('Tagline') ?></label></th>
<td><input name="blogdescription" type="text" id="blogdescription" aria-describedby="tagline-description" value="<?php form_option('blogdescription'); ?>" class="regular-text" />
<p class="description" id="tagline-description"><?php _e( 'In a few words, explain what this site is about.' ) ?></p></td>
</tr>
<?php if ( !is_multisite() ) { ?>
<tr>
<th scope="row"><label for="siteurl"><?php _e('WordPress Address (URL)') ?></label></th>
<td><input name="siteurl" type="url" id="siteurl" value="<?php form_option( 'siteurl' ); ?>"<?php disabled( defined( 'WP_SITEURL' ) ); ?> class="regular-text code<?php if ( defined( 'WP_SITEURL' ) ) echo ' disabled' ?>" /></td>
</tr>
<tr>
<th scope="row"><label for="home"><?php _e('Site Address (URL)') ?></label></th>
<td><input name="home" type="url" id="home" aria-describedby="home-description" value="<?php form_option( 'home' ); ?>"<?php disabled( defined( 'WP_HOME' ) ); ?> class="regular-text code<?php if ( defined( 'WP_HOME' ) ) echo ' disabled' ?>" />
<p class="description" id="home-description"><?php _e( 'Enter the address here if you <a href="https://codex.wordpress.org/Giving_WordPress_Its_Own_Directory">want your site home page to be different from your WordPress installation directory.</a>' ); ?></p></td>
</tr>
<?php }
if ( get_site_option( 'initial_db_version' ) >= 32453 ) : ?>
	<?php if ( ! get_pages() ) : ?>
			<input name="show_on_front" type="hidden" value="posts" />
			<?php
				if ( 'posts' != get_option( 'show_on_front' ) ) :
					update_option( 'show_on_front', 'posts' );
				endif;

			else :
				if ( 'page' == get_option( 'show_on_front' ) && ! get_option( 'page_on_front' ) && ! get_option( 'page_for_posts' ) ) {
					update_option( 'show_on_front', 'posts' );
				}

			?>
			<tr>
			<th scope="row"><?php _e( 'Front page displays' ); ?></th>
			<td id="front-static-pages"><fieldset><legend class="screen-reader-text"><span><?php _e( 'Front page displays' ); ?></span></legend>
				<p><label>
					<input name="show_on_front" type="radio" value="posts" class="tog" <?php checked( 'posts', get_option( 'show_on_front' ) ); ?> />
					<?php _e( 'Your latest posts' ); ?>
				</label>
				</p>
				<p><label>
					<input name="show_on_front" type="radio" value="page" class="tog" <?php checked( 'page', get_option( 'show_on_front' ) ); ?> />
					<?php printf( __( 'A <a href="%s">static page</a> (select below)' ), 'edit.php?post_type=page' ); ?>
				</label>
				</p>
			<ul>
				<li><label for="page_on_front"><?php printf( __( 'Front page: %s' ), wp_dropdown_pages( array( 'name' => 'page_on_front', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0', 'selected' => get_option( 'page_on_front' ) ) ) ); ?></label></li>
				<li><label for="page_for_posts"><?php printf( __( 'Posts page: %s' ), wp_dropdown_pages( array( 'name' => 'page_for_posts', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0', 'selected' => get_option( 'page_for_posts' ) ) ) ); ?></label></li>
			</ul>
			<?php if ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) == get_option( 'page_on_front' ) ) : ?>
			<div id="front-page-warning" class="error inline"><p><?php _e( '<strong>Warning:</strong> these pages should not be the same!' ); ?></p></div>
			<?php endif; ?>
			</fieldset></td>
		<?php endif; ?>
			</tr>
<?php endif; ?>
<?php if ( !is_multisite() ) { ?>
<tr>
<th scope="row"><label for="admin_email"><?php _e('Email Address') ?> </label></th>
<td><input name="admin_email" type="email" id="admin_email" aria-describedby="admin-email-description" value="<?php form_option( 'admin_email' ); ?>" class="regular-text ltr" />
<p class="description" id="admin-email-description"><?php _e( 'This address is used for admin purposes, like new user notification.' ) ?></p></td>
</tr>
<tr>
<th scope="row"><?php _e('Membership') ?></th>
<td> <fieldset><legend class="screen-reader-text"><span><?php _e('Membership') ?></span></legend><label for="users_can_register">
<input name="users_can_register" type="checkbox" id="users_can_register" value="1" <?php checked('1', get_option('users_can_register')); ?> />
<?php _e('Anyone can register') ?></label>
</fieldset></td>
</tr>
<tr>
<th scope="row"><label for="default_role"><?php _e('New User Default Role') ?></label></th>
<td>
<select name="default_role" id="default_role"><?php wp_dropdown_roles( get_option('default_role') ); ?></select>
</td>
</tr>
<?php } else { ?>
<tr>
<th scope="row"><label for="new_admin_email"><?php _e('Email Address') ?> </label></th>
<td><input name="new_admin_email" type="email" id="new_admin_email" aria-describedby="new-admin-email-description" value="<?php form_option( 'admin_email' ); ?>" class="regular-text ltr" />
<p class="description" id="new-admin-email-description"><?php _e( 'This address is used for admin purposes. If you change this we will send you an email at your new address to confirm it. <strong>The new address will not become active until confirmed.</strong>' ) ?></p>
<?php
$new_admin_email = get_option( 'new_admin_email' );
if ( $new_admin_email && $new_admin_email != get_option('admin_email') ) : ?>
<div class="updated inline">
<p><?php printf( __('There is a pending change of the admin email to <code>%1$s</code>. <a href="%2$s">Cancel</a>'), esc_html( $new_admin_email ), esc_url( admin_url( 'options.php?dismiss=new_admin_email' ) ) ); ?></p>
</div>
<?php endif; ?>
</td>
</tr>
<?php } ?>

<tr>
<?php
$current_offset = get_option('gmt_offset');
$tzstring = get_option('timezone_string');

$check_zone_info = true;

// Remove old Etc mappings. Fallback to gmt_offset.
if ( false !== strpos($tzstring,'Etc/GMT') )
	$tzstring = '';

if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
	$check_zone_info = false;
	if ( 0 == $current_offset )
		$tzstring = 'UTC+0';
	elseif ($current_offset < 0)
		$tzstring = 'UTC' . $current_offset;
	else
		$tzstring = 'UTC+' . $current_offset;
}

?>
<th scope="row"><label for="timezone_string"><?php _e('Timezone') ?></label></th>
<td>

<select id="timezone_string" name="timezone_string" aria-describedby="timezone-description">
<?php echo wp_timezone_choice($tzstring); ?>
</select>

	<span id="utc-time"><?php printf(__('<abbr title="Coordinated Universal Time">UTC</abbr> time is <code>%s</code>'), date_i18n($timezone_format, false, 'gmt')); ?></span>
<?php if ( get_option('timezone_string') || !empty($current_offset) ) : ?>
	<span id="local-time"><?php printf(__('Local time is <code>%1$s</code>'), date_i18n($timezone_format)); ?></span>
<?php endif; ?>
<p class="description" id="timezone-description"><?php _e( 'Choose a city in the same timezone as you.' ); ?></p>
<?php if ($check_zone_info && $tzstring) : ?>
<br />
<span>
	<?php
	// Set TZ so localtime works.
	date_default_timezone_set($tzstring);
	$now = localtime(time(), true);
	if ( $now['tm_isdst'] )
		_e('This timezone is currently in daylight saving time.');
	else
		_e('This timezone is currently in standard time.');
	?>
	<br />
	<?php
	$allowed_zones = timezone_identifiers_list();

	if ( in_array( $tzstring, $allowed_zones) ) {
		$found = false;
		$date_time_zone_selected = new DateTimeZone($tzstring);
		$tz_offset = timezone_offset_get($date_time_zone_selected, date_create());
		$right_now = time();
		foreach ( timezone_transitions_get($date_time_zone_selected) as $tr) {
			if ( $tr['ts'] > $right_now ) {
			    $found = true;
				break;
			}
		}

		if ( $found ) {
			echo ' ';
			$message = $tr['isdst'] ?
				__('Daylight saving time begins on: <code>%s</code>.') :
				__('Standard time begins on: <code>%s</code>.');
			// Add the difference between the current offset and the new offset to ts to get the correct transition time from date_i18n().
			printf( $message, date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $tr['ts'] + ($tz_offset - $tr['offset']) ) );
		} else {
			_e('This timezone does not observe daylight saving time.');
		}
	}
	// Set back to UTC.
	date_default_timezone_set('UTC');
	?>
	</span>
<?php endif; ?>
</td>

</tr>
<tr>
<th scope="row"><?php _e('Date Format') ?></th>
<td>
	<fieldset><legend class="screen-reader-text"><span><?php _e('Date Format') ?></span></legend>
<?php
	/**
	* Filter the default date formats.
	*
	* @since 2.7.0
	* @since 4.0.0 Added ISO date standard YYYY-MM-DD format.
	*
	* @param array $default_date_formats Array of default date formats.
	*/
	$date_formats = array_unique( apply_filters( 'date_formats', array( __( 'F j, Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );

	$custom = true;

	foreach ( $date_formats as $format ) {
		echo "\t<label title='" . esc_attr($format) . "'><input type='radio' name='date_format' value='" . esc_attr($format) . "'";
		if ( get_option('date_format') === $format ) { // checked() uses "==" rather than "==="
			echo " checked='checked'";
			$custom = false;
		}
		echo ' /> ' . date_i18n( $format ) . "</label><br />\n";
	}

	echo '	<label><input type="radio" name="date_format" id="date_format_custom_radio" value="\c\u\s\t\o\m"';
	checked( $custom );
	echo '/> ' . __( 'Custom:' ) . '<span class="screen-reader-text"> ' . __( 'enter a custom date format in the following field' ) . "</span></label>\n";
	echo '<label for="date_format_custom" class="screen-reader-text">' . __( 'Custom date format:' ) . '</label><input type="text" name="date_format_custom" id="date_format_custom" value="' . esc_attr( get_option('date_format') ) . '" class="small-text" /> <span class="screen-reader-text">' . __( 'example:' ) . ' </span><span class="example"> ' . date_i18n( get_option('date_format') ) . "</span> <span class='spinner'></span>\n";
?>
	</fieldset>
</td>
</tr>
<tr>
<th scope="row"><?php _e('Time Format') ?></th>
<td>
	<fieldset><legend class="screen-reader-text"><span><?php _e('Time Format') ?></span></legend>
<?php
	/**
	* Filter the default time formats.
	*
	* @since 2.7.0
	*
	* @param array $default_time_formats Array of default time formats.
	*/
	$time_formats = array_unique( apply_filters( 'time_formats', array( __( 'g:i a' ), 'g:i A', 'H:i' ) ) );

	$custom = true;

	foreach ( $time_formats as $format ) {
		echo "\t<label title='" . esc_attr($format) . "'><input type='radio' name='time_format' value='" . esc_attr($format) . "'";
		if ( get_option('time_format') === $format ) { // checked() uses "==" rather than "==="
			echo " checked='checked'";
			$custom = false;
		}
		echo ' /> ' . date_i18n( $format ) . "</label><br />\n";
	}

	echo '	<label><input type="radio" name="time_format" id="time_format_custom_radio" value="\c\u\s\t\o\m"';
	checked( $custom );
	echo '/> ' . __( 'Custom:' ) . '<span class="screen-reader-text"> ' . __( 'enter a custom time format in the following field' ) . "</span></label>\n";
	echo '<label for="time_format_custom" class="screen-reader-text">' . __( 'Custom time format:' ) . '</label><input type="text" name="time_format_custom" id="time_format_custom" value="' . esc_attr( get_option('time_format') ) . '" class="small-text" /> <span class="screen-reader-text">' . __( 'example:' ) . ' </span><span class="example"> ' . date_i18n( get_option('time_format') ) . "</span> <span class='spinner'></span>\n";

	echo "\t<p>" . __('<a href="https://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date and time formatting</a>.') . "</p>\n";
?>
	</fieldset>
</td>
</tr>
<tr>
<th scope="row"><label for="start_of_week"><?php _e('Week Starts On') ?></label></th>
<td><select name="start_of_week" id="start_of_week">
<?php
/**
 * @global WP_Locale $wp_locale
 */
global $wp_locale;

for ($day_index = 0; $day_index <= 6; $day_index++) :
	$selected = (get_option('start_of_week') == $day_index) ? 'selected="selected"' : '';
	echo "\n\t<option value='" . esc_attr($day_index) . "' $selected>" . $wp_locale->get_weekday($day_index) . '</option>';
endfor;
?>
</select></td>
</tr>
<?php do_settings_fields('general', 'default'); ?>

<?php
$languages = get_available_languages();
$translations = wp_get_available_translations();
if ( ! is_multisite() && defined( 'WPLANG' ) && '' !== WPLANG && 'en_US' !== WPLANG && ! in_array( WPLANG, $languages ) ) {
	$languages[] = WPLANG;
}
if ( ! empty( $languages ) || ! empty( $translations ) ) {
	?>
	<tr>
		<th width="33%" scope="row"><label for="WPLANG"><?php _e( 'Site Language' ); ?></label></th>
		<td>
			<?php
			$locale = get_locale();
			if ( ! in_array( $locale, $languages ) ) {
				$locale = '';
			}

			wp_dropdown_languages( array(
				'name'         => 'WPLANG',
				'id'           => 'WPLANG',
				'selected'     => $locale,
				'languages'    => $languages,
				'translations' => $translations,
				'show_available_translations' => ( ! is_multisite() || is_super_admin() ) && wp_can_install_language_pack(),
			) );

			// Add note about deprecated WPLANG constant.
			if ( defined( 'WPLANG' ) && ( '' !== WPLANG ) && $locale !== WPLANG ) {
				if ( is_super_admin() ) {
					?>
					<p class="description">
						<strong><?php _e( 'Note:' ); ?></strong> <?php printf( __( 'The %s constant in your %s file is no longer needed.' ), '<code>WPLANG</code>', '<code>wp-config.php</code>' ); ?>
					</p>
					<?php
				}
				_deprecated_argument( 'define()', '4.0', sprintf( __( 'The %s constant in your %s file is no longer needed.' ), 'WPLANG', 'wp-config.php' ) );
			}
			?>
		</td>
	</tr>
	<?php
}

if ( get_site_option( 'initial_db_version' ) >= 32453 ) : ?>
<tr class="option-site-visibility">
<th scope="row"><?php has_action( 'blog_privacy_selector' ) ? _e( 'Site Visibility' ) : _e( 'Search Engine Visibility' ); ?> </th>
<td><fieldset><legend class="screen-reader-text"><span><?php has_action( 'blog_privacy_selector' ) ? _e( 'Site Visibility' ) : _e( 'Search Engine Visibility' ); ?> </span></legend>
<?php if ( has_action( 'blog_privacy_selector' ) ) : ?>
	<input id="blog-public" type="radio" name="blog_public" value="1" <?php checked('1', get_option('blog_public')); ?> />
	<label for="blog-public"><?php _e( 'Allow search engines to index this site' );?></label><br/>
	<input id="blog-norobots" type="radio" name="blog_public" value="0" <?php checked('0', get_option('blog_public')); ?> />
	<label for="blog-norobots"><?php _e( 'Discourage search engines from indexing this site' ); ?></label>
	<p class="description"><?php _e( 'Note: Neither of these options blocks access to your site &mdash; it is up to search engines to honor your request.' ); ?></p>
	<?php
	/**
	 * Enable the legacy 'Site Visibility' privacy options.
	 *
	 * By default the privacy options form displays a single checkbox to 'discourage' search
	 * engines from indexing the site. Hooking to this action serves a dual purpose:
	 * 1. Disable the single checkbox in favor of a multiple-choice list of radio buttons.
	 * 2. Open the door to adding additional radio button choices to the list.
	 *
	 * Hooking to this action also converts the 'Search Engine Visibility' heading to the more
	 * open-ended 'Site Visibility' heading.
	 *
	 * @since 2.1.0
	 */
	do_action( 'blog_privacy_selector' );
	?>
<?php else : ?>
	<label for="blog_public"><input name="blog_public" type="checkbox" id="blog_public" value="0" <?php checked( '0', get_option( 'blog_public' ) ); ?> />
	<?php _e( 'Discourage search engines from indexing this site' ); ?></label>
	<p class="description"><?php _e( 'It is up to search engines to honor this request.' ); ?></p>
<?php endif; ?>
</fieldset></td>
</tr>
<tr>
<th scope="row"><label for="default_category"><?php _e('Default Post Category') ?></label></th>
<td>
<?php
wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'default_category', 'orderby' => 'name', 'selected' => get_option('default_category'), 'hierarchical' => true));
?>
</td>
</tr>
<tr>
<th scope="row"><label for="posts_per_page"><?php _e( 'Blog pages show at most' ); ?></label></th>
<td>
<input name="posts_per_page" type="number" step="1" min="1" id="posts_per_page" value="<?php form_option( 'posts_per_page' ); ?>" class="small-text" /> <?php _e( 'posts' ); ?>
</td>
</tr>
<?php if ( get_option( 'link_manager_enabled' ) ) :
?>
<tr>
<th scope="row"><label for="default_link_category"><?php _e('Default Link Category') ?></label></th>
<td>
<?php
wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'default_link_category', 'orderby' => 'name', 'selected' => get_option('default_link_category'), 'hierarchical' => true, 'taxonomy' => 'link_category'));
?>
</td>
</tr>
<?php endif; ?>
<?php
$post_formats = get_post_format_strings();
unset( $post_formats['standard'] );
?>
<tr>
<th scope="row"><label for="default_post_format"><?php _e('Default Post Format') ?></label></th>
<td>
	<select name="default_post_format" id="default_post_format">
		<option value="0"><?php echo get_post_format_string( 'standard' ); ?></option>
<?php foreach ( $post_formats as $format_slug => $format_name ): ?>
		<option<?php selected( get_option( 'default_post_format' ), $format_slug ); ?> value="<?php echo esc_attr( $format_slug ); ?>"><?php echo esc_html( $format_name ); ?></option>
<?php endforeach; ?>
	</select>
</td>
</tr>

<?php
do_settings_fields('writing', 'default');
do_settings_fields('writing', 'remote_publishing'); // A deprecated section.
endif; ?>
</table>
<?php if ( apply_filters( 'enable_update_services_configuration', false ) ) { ?>
	<?php if ( 1 == get_option('blog_public')  ) : ?>

	<p><label for="ping_sites"><?php _e('When you publish a new post, WordPress automatically notifies the following site update services. For more about this, see <a href="https://codex.wordpress.org/Update_Services">Update Services</a> on the Codex. Separate multiple service <abbr title="Universal Resource Locator">URL</abbr>s with line breaks.') ?></label></p>

	<textarea name="ping_sites" id="ping_sites" class="large-text code" rows="3"><?php echo esc_textarea( get_option('ping_sites') ); ?></textarea>

	<?php else : ?>

		<p><?php printf(__('WordPress is not notifying any <a href="https://codex.wordpress.org/Update_Services">Update Services</a> because of your site&#8217;s <a href="%s">visibility settings</a>.'), 'options-reading.php'); ?></p>

	<?php endif; ?>
<?php } ?>
<?php do_settings_sections('writing'); ?>
<?php do_settings_sections('general'); ?>

<?php submit_button(); ?>
</form>

</div>

<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
