<?php

// ===========================
// === Plugin Loader Class ===
// ===========================
//
// --------------
// Version: 1.0.9
// --------------
// * changelog at end of file! *
//
// ============
// Loader Usage
// ============
// 1. replace all occurrences of radio_station_ in this file with the plugin namespace prefix eg. my_plugin_
// 2. define plugin options, default settings, and setup arguments your main plugin file
// 3. require this file in the main plugin file and instantiate the loader class (see example below)
//
// ---------------
// Updating Loader
// ---------------
// Repeat Step 1 above with the new version of loader.php and replace in your plugin.
//
// -----------------------------------
// Example Plugin Options and Defaults
// -----------------------------------
// array of plugin option keys, with input types and defaults
// $options = array(
// 	'optionkey1'	=>	array(
//							'type' 		=> 'checkbox',
//							'default'	=> '1',
//							'value'		=> '1',
//						),
//	'optionkey2'	=>	array(
//							'type' 		=> 'radio',
//							'default'	=> 'on',
//							'options'	=> 'on/off',
//						),
//	'optionkey3'	=> array(
//							'type'		=> 'special',
//						),
// );

// -----------------------
// Example Plugin Settings
// -----------------------
// $slug = 'plugin-name';				// plugin slug (usually same as filename)
// $args = array(
//	// --- Plugin Info ---
//	'slug'			=> $slug,			// (uses slug above)
//	'file'			=> __FILE__,		// path to main plugin file (required!)
//	'version'		=> '0.0.1', 		// * rechecked later (pulled from plugin header) *
//
//	// --- Menus and Links ---
//	'title'			=> 'Plugin Name',	// plugin title
//	'parentmenu'	=> 'wordquest',		// parent menu slug
//	'home'			=> 'http://mysite.com/plugins/plugin/',
//	'support'		=> 'http://mysite.com/plugins/plugin/support/',
//	'ratetext'		=> __('Rate on WordPress.org'),		// (overrides default rate text)
//	'share'			=> 'http://mysites.com/plugins/plugin/#share', // (set sharing URL)
//	'sharetext'		=> __('Share the Plugin Love'),		// (overrides default sharing text)
//	'donate'		=> 'https://patreon.com/pagename',	// (overrides plugin Donate URI)
//	'donatetext'	=> __('Support this Plugin'),		// (overrides default donate text)
//	'readme'		=> false,			// to not link to popup readme in settings page header
//	'settingsmenu'	=> false,			// to not automatically add a settings menu [non-WQ]
//
//	// --- Options ---
//	'namespace'		=> 'plugin_name',	// plugin namespace (function prefix)
//	'settings'		=> 'pn',			// input settings prefix
//	'option'		=> 'plugin_key',	// plugin option key
//	'options'		=> $options,		// plugin options array set above
//
//	// --- WordPress.Org ---
//	'wporgslug'		=> 'plugin-slug',	// WordPress.org plugin slug
//	'wporg'			=> false, 			// * rechecked later (via presence of updatechecker.php) *
//	'textdomain'	=> 'text-domain',	// translation text domain (usually same as plugin slug)
//
//	// --- Freemius ---
//	'freemius_id'	=> '',				// Freemius plugin ID
//	'freemius_key'	=> '',				// Freemius public key
//	'hasplans'		=> false,			// has paid plans?
//	'hasaddons'		=> false,			// has add ons?
//	'plan'			=> 'free',	 		// * rechecked later (if premium version found) *
// );
//
// ------------------------------------
// Example Start Plugin Loader Instance
// ------------------------------------
// (add this to your main plugin file to run this loader)
// require(dirname(__FILE__).'/loader.php');				// requires this file!
// $instance = new radio_station_loader($args);					// instantiates loader class
// (ie. search and replace 'radio_station_' with 'my_plugin_' function namespace)


// ===========================
// --- Plugin Loader Class ---
// ===========================
// usage: change class prefix to the plugin function prefix
if (!class_exists('radio_station_loader')) {
 class radio_station_loader {

	public $args = null;
	public $namespace = null;
	public $data = null;
	public $version = null;

	public $options = null;
	public $defaults = null;
	public $tabs = array();
	public $sections = array();

	// -----------------
	// Initialize Loader
	// -----------------
	function __construct($args) {

		// --- set plugin options ---
		// 1.0.6: added options filter
		$args['options'] = apply_filters($args['namespace'].'_options', $args['options']);
		// 1.0.9: maybe get tabs and sections from options array
		if (isset($args['options']['tabs'])) {
			$this->tabs = $args['options']['tabs']; unset($args['options']['tabs']);
		}
		if (isset($args['options']['sections'])) {
			$this->sections = $args['options']['sections']; unset($args['options']['sections']);
		}
		$this->options = $args['options']; unset($args['options']);

		// --- set plugin args and namespace ---
		$this->args = $args;
		$this->namespace = $args['namespace'];

		// --- setup plugin values ---
		$this->setup_plugin();

		// --- maybe transfer settings ---
		$this->maybe_transfer_settings();

		// --- load settings ---
		$this->load_settings();

		// --- load actions ---
		$this->add_actions();

		// --- load helper libraries ---
		$this->load_helpers();

		// --- autoset class instance global for accessibility ---
		$GLOBALS[$args['namespace'].'_instance'] = $this;
	}

	// ------------
	// Setup Plugin
	// ------------
	function setup_plugin() {
		$args = $this->args; $namespace = $this->namespace;

		// --- Read Plugin Header ---
		if (!isset($args['dir'])) {$args['dir'] = dirname($args['file']);}
		$fh = fopen($args['file'], 'r'); $data = fread($fh, 1024);
		$this->data = str_replace("\r", "\n", $data); fclose($fh);

		// --- Version ---
		$this->version = $args['version'] = $this->plugin_data('Version:');

		// --- Title ---
		if (!isset($args['title'])) {$args['title'] = $this->plugin_data('Plugin Name:');}

		// --- Plugin Home ---
		if (!isset($args['home'])) {$args['home'] = $this->plugin_data('Plugin URI:');}

		// --- Author ---
		if (!isset($args['author'])) {$args['author'] = $this->plugin_data('Author:');}

		// --- Author URL ---
		if (!isset($args['author_url'])) {$args['author_url'] = $this->plugin_data('Author URI:');}

		// --- WordQuest plugin flag ---
		// 1.0.8: check for WordQuest helper file
		$wordquest = $args['dir'].'/wordquest.php';
		if (file_exists($wordquest)) {$args['wordquest'] = true;} else {$args['wordquest'] = false;}

		// --- Pro Functions ---
		if (!isset($args['proslug'])) {
			$proslug = $this->plugin_data('@fs_premium_only');
			// 1.0.1: if more than one file, extract pro slug based on the first filename
			if (!strstr($proslug, ',')) {$profiles = array($proslug); $proslug = trim($proslug);}
			else {$profiles = explode(',', $proslug); $proslug = trim($profiles[0]);}
			$args['proslug'] = substr($proslug, 0, -4);		// strips .php extension
			$args['profiles'] = $profiles;
		}

		// --- Update Loader Args ---
		$this->args = $args;
	}

	// ---------------
	// Get Plugin Data
	// ---------------
	function plugin_data($key) {
		$data = $this->data; $value = null;
		$pos = strpos($data, $key);
		if ($pos !== false) {
			$pos = $pos + strlen($key) + 1;
			$tmp = substr($data, $pos);
			$pos = strpos($tmp, "\n");
			$value = trim(substr($tmp, 0, $pos));
		}
		return $value;
	}

	// -----------------
	// Set Pro Namespace
	// -----------------
	function pro_namespace($pronamespace) {
		$this->args['pronamespace'] = $pronamespace;
	}


	// =======================
	// --- Plugin Settings ---
	// =======================

	// --------------------
	// Get Default Settings
	// --------------------
	function default_settings($dkey = false) {

		// --- return defaults if already set ---
		$defaults = $this->defaults;
		if (!is_null($defaults)) {
			if ($dkey && isset($defaults[$dkey])) {return $defaults[$dkey];}
			return $defaults;
		}

		// --- filter and store the plugin default settings ---
		$options = $this->options; $defaults = array();
		foreach ($options as $key => $values) {
			// 1.0.9: set default to null if default value not set
			if (isset($values['default'])) {$defaults[$key] = $values['default'];}
			else {$defaults[$key] = null;}
		}
		$namespace = $this->namespace;
		$defaults = apply_filters($namespace.'_default_settings', $defaults);
		$this->defaults = $defaults;
		if ($dkey && isset($defaults[$dkey])) {return $defaults[$dkey];}
		return $defaults;
	}

	// ------------
	// Add Settings
	// ------------
	function add_settings() {
		// --- add the default plugin settings ---
		$args = $this->args; $defaults = $this->default_settings();
		$added = add_option($args['option'], $defaults);

		// --- if added, make the defaults current settings ---
		if ($added) {
			$namespace = $this->namespace;
			foreach ($defaults as $key => $value) {$GLOBALS[$namespace][$key] = $value;}
		}

		// --- add WordQuest sidebar settings ---
		if ($args['wordquest'] && isset($args['settings'])) {
			if (file_exists($args['dir'].'/updatechecker.php')) {$adsboxoff = '';} else {$adsboxoff = 'checked';}
			$sidebaroptions = array('adsboxoff' => $adsboxoff, 'donationboxoff' => '', 'reportboxoff' => '', 'installdate' => date('Y-m-d'));
			add_option($args['settings'].'_sidebar_options', $sidebaroptions);
		}
	}

	// -----------------------
	// Maybe Transfer Settings
	// -----------------------
	function maybe_transfer_settings() {
		$namespace = $this->namespace; $funcname = $namespace.'_transfer_settings';

		// --- check for either function prefixed or class extended method ---
		if (method_exists($this, 'transfer_settings')) {$this->transfer_settings();}
		elseif (function_exists($funcname)) {call_user_func($funcname);}
	}

	// -----------------------
	// Get All Plugin Settings
	// -----------------------
	function get_settings($filter=true) {
		$namespace = $this->namespace;
		$settings = $GLOBALS[$namespace];
		if ($filter) {
			// 1.0.8: only apply all settings filter if filter is true
			$settings = apply_filters($namespace.'_settings', $settings);
			// 1.0.8: maybe apply all individual key value filters
			foreach ($settings as $key => $value) {
				$settings[$key] = apply_filters($namespace.'_'.$key, $value);
			}
		}
		return $settings;
	}

	// ------------------
	// Get Plugin Setting
	// ------------------
	function get_setting($key, $filter=true) {
		$args = $this->args;
		$namespace = $this->namespace; $settings = $GLOBALS[$namespace];
		$settings = apply_filters($namespace.'_settings', $settings);

		// --- maybe strip settings prefix ---
		// 1.0.4: added for backwards compatibility
		if (substr($key, 0, strlen($args['settings'])) == $args['settings']) {
			$key = substr($key, strlen($args['settings']) + 1, strlen($key));
		}

		// --- get plugin setting ---
		if (isset($settings[$key])) {$value = $settings[$key];}
		else {
			$defaults = $this->default_settings();
			if (isset($defaults[$key])) {$value = $defaults[$key];}
			else {$value = null;}
		}
		if ($filter) {$value = apply_filters($namespace.'_'.$key, $value);}
		return $value;
	}

	// ---------------------
	// Reset Plugin Settings
	// ---------------------
	function reset_settings() {
		$args = $this->args; $namespace = $this->namespace;

		// --- check reset triggers ---
		// 1.0.2: fix to namespace key typo in isset check
		// 1.0.3: only use namespace not settings key
		if (!isset($_POST[$args['namespace'].'_update_settings'])) {return;}
		if ($_POST[$args['namespace'].'_update_settings'] != 'reset') {return;}

		// --- check reset permissions ---
		$capability = apply_filters($args['namespace'].'_manage_options_capability', 'manage_options');
		if (!current_user_can($capability)) {return;}
		check_admin_referer($args['slug']);

		// --- reset plugin settings ---
		$defaults = $this->default_settings();
		$defaults['savetime'] = time();
		update_option($args['option'], $defaults);

		// --- loop to remerge with settings global ---
		foreach ($defaults as $key => $value) {$GLOBALS[$namespace][$key] = $value;}

		// --- set settings reset message flag ---
		$_GET['updated'] = 'reset';
	}

	// ----------------------
	// Update Plugin Settings
	// ----------------------
	function update_settings() {
		$args = $this->args; $namespace = $this->namespace;
		$settings = $GLOBALS[$namespace];

		// --- check update triggers ---
		// 1.0.2: fix to namespace key typo in isset check
		// 1.0.3: only use namespace not settings key
		if (!isset($_POST[$args['namespace'].'_update_settings'])) {return;}
		if ($_POST[$args['namespace'].'_update_settings'] != 'yes') {return;}

		// --- check update permissions ---
		$capability = apply_filters($namespace.'_manage_options_capability', 'manage_options');
		if (!current_user_can($capability)) {return;}
		check_admin_referer($args['slug']);

		// --- get plugin options and default settings ---
		$options = $this->options;
		$defaults = $this->default_settings();

		// --- maybe use custom method or function ---
		$funcname = $namespace.'_process_settings';
		if (method_exists($this, 'process_settings')) {

			// --- use class extended method if found ---
			$settings = $this->process_settings();

		} elseif (function_exists($funcname) && is_callable($funcname))  {

			// --- use namespace prefixed function if found ---
			$settings = call_user_func($funcname);

		} else {

			// --- loop plugin options to get new settings ---
			foreach ($options as $key => $values) {

				// --- get option type and options ---
				$type = $values['type']; $valid = $validate_args = array();
				if (isset($values['options'])) {$valid = $values['options'];}

				// --- get posted value ---
				// 1.0.6: set null value for unchecked checkbox fix
				$posted = null; $postkey = $args['settings'].'_'.$key;
				if (isset($_POST[$postkey])) {$posted = $_POST[$postkey];}
				$newsettings = false;

				// --- maybe validate special options ---
				// 1.0.9: check for special options to prepare
				if (is_string($valid)) {

					// --- maybe get public post type slugs ---
					if (in_array($valid, array('PUBLICTYPE', 'PUBLICTYPES'))) {
						$valid = array();
						if (!isset($public)) {
							$cpts = array('page', 'post');
							$cptargs = array('public' => true, '_builtin' => false);
							$cptlist = get_post_types($cptargs, 'names', 'and');
							$public = array_merge($cpts, $cptlist);
						}
						foreach ($public as $cpt) {$valid[$cpt] = '';}
					}

					// --- maybe get post type slugs ---
					if (in_array($valid, array('POSTTYPE', 'POSTTYPES'))) {
						$valid = array();
						if (!isset($cpts)) {
							$cpts = array('page', 'post');
							$cptargs = array('public' => true, '_builtin' => false);
							$cptlist = get_post_types($cptargs, 'names', 'and');
							$cpts = array_merge($cpts, $cptlist);
						}
						foreach ($cpts as $cpt) {$valid[$cpt] = '';}
					}

					// --- maybe get all post type slugs ---
					if (in_array($valid, array('ALLTYPE', 'ALLTYPES'))) {
						$valid = array();
						if (!isset($allcpts)) {
							$cptargs = array('_builtin' => false);
							$allcpts = get_post_types($cptargs, 'names', 'and');
						}
						foreach ($allcpts as $cpt) {$valid[$cpt] = '';}
					}
				}

				if (isset($_REQUEST['debug']) && ($_REQUEST['debug'] == 'yes')) {
					echo 'Saving Setting Key '.$key.' ('.$postkey.': '.print_r($posted,true).'<br>';
					echo 'Type: '.$type.' - Valid Options '.$key.': '.print_r($valid,true).'<br>';
				}

				// --- sanitize value according to type ---
				if (strstr($type, '/')) {

					// --- implicit radio / select ---
					$valid = explode('/', $type);
					if (in_array($posted, $valid)) {$settings[$key] = $posted;}

				} elseif ( ($type == 'checkbox') || ($type == 'toggle') ) {

					// --- checkbox / toggle ---
					// 1.0.6: fix to new unchecked checkbox value
					// 1.0.9: maybe validate to specified checkbox value
					if (isset($values['value'])) {$valid = array($values['value']);}
					else {$valid = array('yes', '1', 'checked');}
					if (in_array($posted, $valid)) {$settings[$key] = $posted;}
					elseif (is_null($posted)) {$settings[$key] = '';}

				} elseif ($type == 'textarea') {

					// --- text area ---
					// TODO: use sanitize text field ?
					$posted = stripslashes($posted);
					$settings[$key] = $posted;

				} elseif ($type == 'text') {

					// --- text field (slug) ---
					// 1.0.9: move text field sanitization to validation
					// $posted = sanitize_text_field($posted);
					if (!is_string($valid)) {$valid = 'TEXT';}
					$newsettings = $posted;

				} elseif ( ($type == 'number') || ($type == 'numeric') ) {

					// --- number field value ---
					// 1.0.9: added support for number step, minimum and maximum
					$newsettings = $posted; $valid = 'NUMERIC';
					if (isset($values['step'])) {$validate_args['step'] = $values['step'];}
					if (isset($values['min'])) {$validate_args['min'] = $values['min'];}
					if (isset($values['max'])) {$validate_args['max'] = $values['max'];}
							
				} elseif ($type == 'multicheck') {

					// --- process multicheck boxes ---
					// 1.0.9: added multicheck input type
					// note: needs defined options (but works with post types)
					$posted = array();
					foreach ($valid as $option => $label) {
						$optionkey = $args['settings'].'_'.$key.'-'.$option;
						if (isset($_POST[$optionkey]) && ($_POST[$optionkey] == 'yes')) {
							if (isset($values['value'])) {$posted[$option] = $values['value'];}
							else {$posted[$option] = 'yes';}
						} else {$posted[$option] = '';}
					}
					$settings[$key] = $posted;

				} elseif ($type == 'csv') {

					// -- comma separated values ---
					// 1.0.4: added comma separated values option
					$values = array();
					if (strstr($posted, ',')) {$posted = explode(',', $posted);}
					else {$posted[0] = $posted;}
					foreach ($posted as $i => $value) {$posted[$i] = trim($value);}
					if (is_string($valid)) {$newsettings = $posted;}
					elseif (is_array($valid)) {
						foreach ($posted as $i => $value) {
							if (!in_array($value, $valid)) {unset($posted[$i]);}
						}
						$settings[$key] = implode(',', $posted);
					} else {$settings[$key] = implode(',', $posted);}

				} elseif ( ($type == 'radio') || ($type == 'select') ) {

					// --- explicit radio or select value ---
					if (is_string($valid)) {$newsettings = $posted;}
					elseif (is_array($valid) && array_key_exists($posted, $valid)) {
						$settings[$key] = $posted;
					}

				} elseif ($type == 'multiselect') {

					// --- multiselect values ---
					// 1.0.9: added multiselect value saving
					$newsettings = array_values($posted);

				}

				if (isset($_REQUEST['debug']) && ($_REQUEST['debug'] == 'yes')) {
					echo 'New Settings for Key '.$key.': ';
					if ($newsettings) {echo '(to-validate) '.print_r($newsettings,true).'<br>';}
					else {echo '(validated) '.print_r($settings[$key],true).'<br>';}
				}

				// --- maybe validate new settings ---
				if ($newsettings) {

					if (is_array($newsettings)) {
						// --- validate array of settings ---
						foreach ($newsettings as $newkey => $newvalue) {
							$newsetting = $this->validate_setting($newvalue, $valid, $validate_args);
							if ($newsetting && ($newsetting != '')) {
								$newsettings[$newkey] = $newsetting;
							} else {unset($newsettings[$newkey]);}
						}
						if ($type == 'csv') {$settings[$key] = implode(',', $newsettings);}
						else {$settings[$key] = $newsettings;}
					} else {
						// --- validate single setting ---
						$newsetting = $this->validate_setting($newsettings, $valid, $validate_args);
						if ($newsetting) {$settings[$key] = $newsetting;}
					}

					if (isset($_REQUEST['debug']) && ($_REQUEST['debug'] == 'yes')) {
						echo 'Valid Options for Key '.$key.': '.print_r($valid,true).'<br>';
						echo 'Validated Settings for Key '.$key.': '.print_r($settings[$key],true).'<br>';
					}
				}

			}
		}

		// --- process special settings ---
		// 1.0.2: added for processing special settings separately
		$funcname = $namespace.'_process_special';
		if (method_exists($this, 'process_special')) {

			// --- use class extended method if found ---
			$settings = $this->process_special($settings);

		} elseif (function_exists($funcname) && is_callable($funcname))  {

			// --- use namespace prefixed function if found ---
			$settings = call_user_func($funcname, $settings);
		}


		if ($settings && is_array($settings)) {

			// --- loop default keys to remove others ---
			$settings_keys = array_keys($defaults);
			foreach ($settings as $key => $value) {
				if (!in_array($key, $settings_keys)) {unset($settings[$key]);}
			}

			// --- update the plugin settings ---
			$settings['savetime'] = time();
			update_option($args['option'], $settings);

			// --- merge with existing settings for pageload ---
			foreach ($settings as $key => $value) {$GLOBALS[$namespace][$key] = $value;}

			// --- set settings update message flag ---
			$_GET['updated'] = 'yes';

		} else {$_GET['updated'] = 'no';}

		// --- maybe trigger update of pro settings ---
		if (method_exists($this, 'pro_update_settings')) {$this->pro_update_settings();}
		else {
			if (isset($args['pronamespace'])) {$funcname = $args['pronamespace'].'_update_settings';}
			else {$funcname = $args['namespace'].'_pro_update_settings';}
			if (function_exists($funcname)) {call_user_func($funcname);}
		}

	}

	// ----------------
	// Validate Setting
	// ----------------
	function validate_setting($posted, $valid, $args) {

		// --- allow for clearing of a field ---
		// note: array values are cleared if empty, single values are set empty
		if (trim($posted) == '') {return '';}

		// --- validate different data types ---
		if ($valid == 'TEXT') {

			// --- sanitize text field ---
			$posted = sanitize_text_field($posted);
			return $posted;

		} elseif ($valid == 'ALPHABETIC') {

			// --- alphabetic ---
			// 1.0.9: added alphabetic-only for completeness
			$posted = trim($posted);
			$checkposted = preg_match('/^[a-zA-Z]+$/', $posted);
			if ($checkposted) {return $posted;}

		} elseif ($valid == 'NUMERIC') {

			// --- number (numeric text) ---
			// note: step key is only used for controls, not for validation
			// 1.0.9: cast to integer not absolute integer
			$posted = floatval(trim($posted));
			echo "*****".$posted."*****";
			if (isset($args['min']) && ($posted < $args['min'])) {return $args['min'];}
			elseif (isset($args['max']) && ($posted > $args['max'])) {return $args['max'];}
			elseif ($posted === 0) {return 0;}
			return $posted;

		} elseif ($valid == 'ALPHANUMERIC') {

			// --- alphanumeric text only ---
			$posted = trim($posted);
			// TODO: remove underscore from validation regex ?
			$checkposted = preg_match('/^[a-zA-Z0-9_]+$/', $posted);
			if ($checkposted) {return $posted;}

		} elseif (in_array($valid, array('URL', 'URLS'))) {

			// --- URL address ---
			// 1.0.6: fix to type variable typo (vtype)
			// 1.0.4: added validated URL option
			// TODO: maybe replace with a regex URL filter ?
			// 1.0.6: fix to posted variable type (vposted)
			$posted = trim($posted);
			$url = filter_var($posted, FILTER_SANITIZE_STRING);
			if ( (substr($url, 0, 4) != 'http') || !filter_var($url, FILTER_VALIDATE_URL)) {$posted = '';}
			return $posted;

		} elseif (in_array($valid, array('EMAIL', 'EMAILS'))) {

			// --- email address ---
			// 1.0.3: added email option type checking
			$email = sanitize_email(trim($posted));
			if ($email) {$posted = $email;} else {$posted = '';}
			return $posted;

		} elseif (in_array($valid, array('USERNAME', 'USERNAMES'))) {

			// --- username ---
			$username = sanitize_user(trim($posted));
			if (!$username) {return '';}
			$user = get_user_by('login', $username);
			if ($user) {$posted = $username;} else {$posted = '';}
			return $posted;

		} elseif (in_array($valid, array('USERID', 'USERIDS'))) {

			// --- user ID ---
			$userid = intval(trim($posted));
			if ($userid === 0) {return '';}
			$user = get_user_by('ID', $userid);
			if ($user) {$posted = $userid;} else {$posted = '';}
			return $posted;

		} elseif (in_array($valid, array('SLUG', 'SLUGS'))) {

			// -- post slugs ---
			$posted = sanitize_title(trim($posted));
			return $posted;

		} elseif (in_array($valid, array('PAGEID', 'PAGEIDS', 'POSTID', 'POSTIDS'))) {

			$posted = intval(trim($posted));
			if ($posted === 0) {return '';}
			$post = get_post($posted);
			if ($post) {return $posted;}
		}

		return false;
	}

	// ---------------
	// Delete Settings
	// ---------------
	function delete_settings() {
		// TODO: check for settings delete settings switch ?
		$args = $this->args;
		delete_option($args['option']);
	}


	// ===============
	// --- Loading ---
	// ===============

	// --------------------
	// Load Plugin Settings
	// --------------------
	function load_settings() {
		$args = $this->args; $namespace = $this->namespace;
		$GLOBALS[$namespace] = $args;
		$settings = get_option($args['option'], false);
		if ($settings && is_array($settings)) {
			foreach ($settings as $key => $value) {$GLOBALS[$namespace][$key] = $value;}
		} else {
			$defaults = $this->default_settings();
			foreach ($defaults as $key => $value) {$GLOBALS[$namespace][$key] = $value;}
		}
	}

	// -----------
	// Add Actions
	// -----------
	function add_actions() {
		$args = $this->args; $namespace = $this->namespace;

		// --- add settings on activation ---
		register_activation_hook($args['file'], array($this, 'add_settings'));

		// --- always check for update and reset of settings ---
		add_action('admin_init', array($this, 'update_settings'));
		add_action('admin_init', array($this, 'reset_settings'));

		// --- add plugin submenu ---
		add_action('admin_menu', array($this, 'settings_menu'), 1);

		// --- add plugin settings page link ---
		add_filter('plugin_action_links', array($this, 'settings_link'), 10, 2);

		// --- delete settings on deactivation ---
		// TODO: check for flag to delete settings data?
		// register_deactivation_hook($args['file'], array($this, 'delete_settings'));

		// --- maybe load thickbox ---
		add_action('admin_enqueue_scripts', array($this, 'maybe_load_thickbox'));

		// --- AJAX readme viewer ---
		add_action('wp_ajax_'.$namespace.'_readme_viewer', array($this, 'readme_viewer'));
	}

	// ---------------------
	// Load Helper Libraries
	// ---------------------
	function load_helpers() {
		$args = $this->args; $file = $args['file']; $dir = $args['dir'];

		// --- Plugin Slug ---
		if (!isset($args['slug'])) {$args['slug'] = substr($file, 0, -4); $this->args = $args;}

		// --- Pro Functions ---
		$plan = 'free';
		// 1.0.2: auto-load the Pro file(s)
		if (count($args['profiles']) > 0) {
			foreach ($args['profiles'] as $profile) {
				// --- chech for php extension ---
				if (substr($profile, -4, 4) == '.php') {
					$filepath = $dir.'/'.$profile;
					if (file_exists($filepath)) {$plan = 'premium'; include($filepath);}
				}
			}
		}
		$args['plan'] = $plan; $this->args = $args;

		// --- Plugin Update Checker ---
		// note: lack of updatechecker.php file indicates WordPress.Org SVN repo version
		// presence of updatechecker.php indicates direct site download or GitHub version
		$wporg = true; $updatechecker = $dir.'/updatechecker.php';
		if (file_exists($updatechecker)) {
			$wporg = false; $slug = $args['slug'];
			// note: requires $file and $slug to be defined
			include($updatechecker);
		}
		$args['wporg'] = $wporg; $this->args = $args;

		// --- WordQuest Admin ---
		// 1.0.8: check WordQuest plugin flag
		if ($args['wordquest'] && is_admin() && (version_compare(PHP_VERSION, '5.3.0') >= 0)) {
			include($dir.'/wordquest.php');
			global $wordquestplugins; $slug = $args['slug'];
			foreach ($args as $key => $value) {$wordquestplugins[$slug][$key] = $value;}
		}

		// --- Freemius ---
		if (version_compare(PHP_VERSION, '5.4.0') >= 0) {$this->load_freemius();}

	}

	// -------------------
	// Maybe Load Thickbox
	// -------------------
	function maybe_load_thickbox() {
		$args = $this->args;
		if (isset($_REQUEST['page']) && ($_REQUEST['page'] == $args['slug'])) {add_thickbox();}
	}

	// -------------
	// Readme Viewer
	// -------------
	function readme_viewer() {
		$args = $this->args;

		echo "<html><body style='font-family: Consolas, \"Lucida Console\", Monaco, FreeMono, monospace'>";

        // 1.0.7: changed readme.php to reader.php (for Github)
		$readme = dirname($args['file']).'/readme.txt';
		$contents = file_get_contents($readme);
		$parser = dirname($args['file']).'/reader.php';

		if (file_exists($parser)) {

			// --- include Markdown Readme Parser ---
			include($parser);
			$contents = str_replace('License: GPLv2 or later', '', $contents);
			$contents = str_replace('License URI: http://www.gnu.org/licenses/gpl-2.0.html', '', $contents);

			// --- instantiate Parser class ---
			$readme = new WordPress_Readme_Parser;
			$parsed = $readme->parse_readme_contents($contents);

			// --- output plugin info ---
			echo "<b>Plugin Name</b>: ".$parsed['name']."<br>";
			// echo "<b>Tags</b>: ".implode(', ', $parsed['tags'])."<br>";
			echo "<b>Requires at least</b>: WordPress v".$parsed['requires_at_least']."<br>";
			echo "<b>Tested up to</b>: WordPress v".$parsed['tested_up_to']."<br>";
			if (isset($parsed['stable_tag'])) {echo "<b>Stable Tag</b>: ".$parsed['stable_tag']."<br>";}
			echo "<b>Contributors</b>: ".implode(', ', $parsed['contributors'])."<br>";
			// echo "<b>Donate Link</b>: <a href='".$parsed['donate_link']."' target=_blank>".$parsed['donate_link']."</a><br>";
			echo "<br>".$parsed['short_description']."<br><br>";

			// --- output sections ---
			// possible sections: 'description', 'installation', 'frequently_asked_questions',
			// 'screenshots', 'changelog', 'change_log', 'upgrade_notice'
			$strip = array('installation', 'screenshots');
			foreach ($parsed['sections'] as $key => $section) {
				if (!empty($section) && !in_array($key, $strip)) {
					if (strstr($key, '_')) {$parts = explode('_', $key);} else {$parts = array(); $parts[0] = $key;}
					foreach ($parts as $i => $part) {$parts[$i] = strtoupper(substr($part, 0, 1)).substr($part, 1);}
					$title = implode(' ', $parts);
					echo "<h3>".$title."</h3>";
					echo $section;
				}
			}
			if (isset($parsed['remaining_content']) && !empty($remaining_content)) {
				echo "<h3>Extra Notes</h3>".$parsed['remaining_content'];
			}

		} else {
			// --- fallback text display ---
			$readme = dirname($args['file']).'/readme.txt';
			$contents = str_replace("\n", "<br>", $contents);
			echo $contents;
		}

		echo "</body></html>"; exit;
	}


	// =======================
	// --- Freemius Loader ---
	// =======================
	//
	// required settings keys:
	// -----------------------
	// freemius_id	- plugin ID from Freemius plugin dashboard
	// freemius_key	- public key from Freemius plugin dashboard
	//
	// optional settings keys:
	// -----------------------
	// plan 		- (string) curent plugin plan (value of 'free' or 'premium')
	// hasplans		- (boolean) switch for whether plugin has premium plans
	// hasaddons	- (boolean) switch for whether plugin has premium addons
	// wporg		- (boolean) switch for whether free plugin is WordPress.org compliant
	// contact		- (boolean) submenu switch for plugin Contact (defaults to on for premium only)
	// support		- (boolean) submenu switch for plugin Support (default on)
	// account		- (boolean) submenu switch for plugin Account (default on)
	// parentmenu	- (string) optional slug for plugin parent menu
	//
	// okay lets do this...
	// ====================
	function load_freemius() {

		$args = $this->args; $namespace = $this->namespace;

		// --- check for required keys ---
		if (!isset($args['freemius_id']) || !isset($args['freemius_key'])) {return;}

		// --- check for free / premium plan ---
		// convert plan string value of 'free' or 'premium' to boolean premium switch
		$premium = false; if (isset($args['plan']) && ($args['plan'] == 'premium')) {$premium = true;}

		// --- maybe redirect link to plugin support forum ---
		// TODO: change to use new Freemius 2.3.0 support link filter
		if (isset($_REQUEST['page']) && ($_REQUEST['page'] == $args['slug'].'-wp-support-forum') && is_admin()) {
			if (!function_exists('wp_redirect')) {include(ABSPATH.WPINC.'/pluggable.php');}
			if (isset($args['support'])) {
				// changes the support forum slug for premium based on the pro plugin file slug
				// 1.0.7: fix support URL undefined variable warning
				$suport_url = $args['support'];
				if ($premium) {$support_url = str_replace($args['slug'], $args['proslug'], $support_url);}
				$support_url = apply_filters('freemius_plugin_support_url_redirect', $support_url, $args['slug']);
				wp_redirect($support_url); exit;
			}
		}

		// --- do the Freemius Loading boogie ---
		if (!isset($args['freemius'])) {

			// --- start the Freemius SDK ---
			if (!class_exists('Freemius')) {
				$freemiuspath = dirname(__FILE__).'/freemius/start.php';
				if (!file_exists($freemiuspath)) {return;}
				require_once($freemiuspath);
			}

			// --- set defaults for optional key values ---
			if (!isset($args['hasaddons'])) {$args['hasaddons'] = false;}
			if (!isset($args['hasplans'])) {$args['hasplans'] = false;}
			if (!isset($args['wporg'])) {$args['wporg'] = false;}

			// --- set defaults for options submenu key values ---
			// 1.0.2: fix to isset check keys
			// 1.0.5: fix to set args subkeys for support and account
			if (!isset($args['support'])) {$args['support'] = true;}
			if (!isset($args['account'])) {$args['account'] = true;}
			// by default, enable contact submenu item for premium plugins only
			if (!isset($args['contact'])) {$args['contact'] = $premium;}
			if (!isset($args['type'])) {$args['type'] = 'plugin';}

			// --- Freemius settings from plugin settings ---
			$settings = array(
				'type'				=> $args['type'],
				'slug'              => $args['slug'],
				'id'                => $args['freemius_id'],
				'public_key'        => $args['freemius_key'],
				'has_addons'        => $args['hasaddons'],
				'has_paid_plans'    => $args['hasplans'],
				'is_org_compliant'  => $args['wporg'],
				'is_premium'        => $premium,
				'menu'              => array(
					'slug'       	=> $args['slug'],
					'first-path' 	=> 'admin.php?page='.$args['slug'].'&welcome=true',
					'contact'		=> $args['contact'],
					'support'		=> $args['support'],
					'account'		=> $args['account'],
			   )
			);

			// --- maybe add plugin submenu to parent menu ---
			if (isset($args['parentmenu'])) {
				$settings['menu']['parent'] = array('slug' => $args['parentmenu']);
			}

			// --- filter settings before initializing ---
			$settings = apply_filters('freemius_init_settings_'.$args['namespace'], $settings);
			if (!$settings || !is_array($settings)) {return;}

			// --- initialize Freemius now ---
			$freemius = $GLOBALS[$namespace.'_freemius'] = fs_dynamic_init($settings);

			// --- set plugin basename ---
			// 1.0.1: set free / premium plugin basename
			if (method_exists($freemius, 'set_basename')) {
				$freemius->set_basename($premium, $args['file']);
			}

			// --- add Freemius connect message filter ---
			$this->freemius_connect();
		}
	}

	// -----------------------
	// Filter Freemius Connect
	// -----------------------
	function freemius_connect() {
		$namespace = $this->args['namespace']; $freemius = $GLOBALS[$namespace.'_freemius'];
		if (isset($settings['freemius']) && is_object($freemius) && method_exists($freemius, 'add_filter') ) {
			$freemius->add_filter('connect_message', array($this, 'freemius_connect_message'), WP_FS__DEFAULT_PRIORITY, 6);
		}
	}

	// ------------------------
	// Freemius Connect Message
	// ------------------------
	function freemius_connect_message($message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link) {
		// default: 'Never miss an important update - opt-in to our security and feature updates notifications, and non-sensitive diagnostic tracking with %4$s.'
		$message = __fs('hey-x').'<br>';
		$message .= sprintf(
			__("If you want to more easily access support and feedback for this plugins features and functionality, %s can connect your user, %s at %s, to %s"),
			$user_first_name, '<b>'.$plugin_title.'</b>', '<b>'.$user_login.'</b>', $site_link, $freemius_link
		);
		return $message;
	}

	// ----------------------
	// Connect Update Message
	// ----------------------
	// TODO: message for connect updates
	function freemius_update_message($message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link) {
		// default: 'Please help us improve %1$s! If you opt-in, some data about your usage of %1$s will be sent to %4$s. If you skip this, that\'s okay! %1$s will still work just fine.'
		$message = freemius_message($message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link);
	}


	// =============
	// --- Admin ---
	// =============

	// -----------------
	// Add Settings Menu
	// -----------------
	function settings_menu() {
		$args = $this->args; $namespace = $this->namespace; $settings = $GLOBALS[$namespace];

		// --- filter capability ---
		$args['capability'] = apply_filters($args['namespace'].'_manage_options_capability', 'manage_options');
		if (!isset($args['pagetitle'])) {$args['pagetitle'] = $args['title'];}
		if (!isset($args['menutitle'])) {$args['menutitle'] = $args['title'];}

		// --- check if WordQuest plugin ---
		// 1.0.8: change from function exists check
		if ($args['wordquest']) {

			// --- filter menu capability early ---
			$capability = apply_filters('wordquest_menu_capability', 'manage_options');

			// --- maybe add Wordquest top level menu ---
			global $admin_page_hooks;
			if (empty($admin_page_hooks['wordquest'])) {
				$icon = plugins_url('images/wordquest-icon.png', $args['file']);
				$position = apply_filters('wordquest_menu_position', '3');
				add_menu_page('WordQuest Alliance', 'WordQuest', $capability, 'wordquest', 'wqhelper_admin_page', $icon, $position);
			}

			// --- check if using parent menu ---
			// (and parent menu capability)
			if (isset($args['parentmenu']) && ($args['parentmenu'] == 'wordquest') && current_user_can($capability)) {

				// --- add WordQuest Plugin Submenu ---
				$menuadded = add_submenu_page('wordquest', $args['pagetitle'], $args['menutitle'], $args['capability'], $args['slug'], $args['namespace'].'_settings_page');

				// --- add icons and styling fix to the plugin submenu :-) ---
				add_action('admin_footer', array($this, 'submenu_fix'));
			}
		}

		// --- maybe auto-add standalone options page ---
		if (!isset($menuadded) || !$menuadded) {
			// 1.0.8: check settingsmenu switch
			if (!isset($args['settingsmenu']) || ($args['settingsmenu'] !== false)) {
				add_options_page($args['pagetitle'], $args['menutitle'], $args['capability'], $args['slug'], $args['namespace'].'_settings_page');
			}
		}
	}

	// ---------------------
	// WordQuest Submenu Fix
	// ---------------------
	function submenu_fix() {
		$args = $this->args; $slug = $args['slug']; $current = '0';
		$icon_url = plugins_url('images/icon.png', $args['file']);
		if (isset($_REQUEST['page']) && ($_REQUEST['page'] == $slug) ) {$current = '1';}
		echo "<script>jQuery(document).ready(function() {if (typeof wordquestsubmenufix == 'function') {
		wordquestsubmenufix('".$slug."','".$icon_url."','".$current."');} });</script>";
	}

	// -------------------------
	// Plugin Page Settings Link
	// -------------------------
	function settings_link($links, $file) {
		$args = $this->args;
		if ($file == plugin_basename($args['file'])) {
			$settingslink = "<a href='".admin_url('admin.php')."?page=".$args['slug']."'>".__('Settings')."</a>";
			array_unshift($links, $settingslink);
		}
		return $links;
	}

	// -----------
	// Message Box
	// -----------
	function message_box($message, $echo) {
		$box = "<table style='background-color: lightYellow; border-style:solid; border-width:1px; border-color: #E6DB55; text-align:center;'>";
		$box .= "<tr><td><div class='message' style='margin:0.25em;'><font style='font-weight:bold;'>";
		$box .= $message."</font></div></td></tr></table>";
		if ($echo) {echo $box;} else {return $box;}
	}

	// ------------------
	// Plugin Page Header
	// ------------------
	function settings_header() {
		$args = $this->args; $namespace = $this->namespace;
		$settings = $GLOBALS[$namespace];

		// --- output debug values ---
		if (isset($_REQUEST['debug'])) {
			if ( ($_REQUEST['debug'] == 'yes') || ($_REQUEST['debug'] == '1') ) {
				echo "<br><b>Current Settings:</b><br>";
					print_r($settings);
				echo "<br><br>";
	
				echo "<br><b>Plugin Options:</b><br>";
					print_r($this->options);
				echo "<br><br>";
	
				if (isset($_POST)) {
					echo "<br><b>Posted Values:</b><br>";
					foreach ($_POST as $key => $value) {
						echo $key.': '.print_r($value,true).'<br>';
					}
				}
			}
		}

		// --- check for animated gif icon with fallback to normal icon ---
		// 1.0.9: fix to check if PNG file exists
		if (file_exists($this->args['dir'].'/images/'.$args['slug'].'.gif')) {
			$icon_url = plugins_url('images/'.$args['slug'].'.gif', $args['file']);
		} elseif (file_exists($this->args['dir'].'/images/'.$args['slug'].'.png')) {
			$icon_url = plugins_url('images/'.$args['slug'].'.png', $args['file']);
		} else {$icon_url = false;}
		$icon_url = apply_filters($namespace.'_plugin_icon_url', $icon_url);

		// --- check for author icon based on provided author name ---
		// 1.0.2: check if author icon file exists and fallback
		$author_slug = strtolower(str_replace(' ', '', $args['author']));
		if (file_exists($this->args['dir'].'/images/'.$author_slug.'.png')) {
			$author_icon_url = plugins_url('images/'.$author_slug.'.png', $args['file']);
		} elseif (file_exists($this->args['dir'].'/images/wordquest.png')) {
			$author_icon_url = plugins_url('images/wordquest.png', $args['file']);
		} else {$author_icon_url = false;}
		$author_icon_url = apply_filters($namespace.'_author_icon_url', $author_icon_url);

		// --- plugin header styles ---
		echo "<style>.pluginlink {text-decoration:none;} .smalllink {font-size:11px;}
		.readme:hover {text-decoration:underline;}</style>";

		// --- open header table ---
		echo "<table><tr>";

		// --- plugin icon ---
		echo "<td>";
			if ($icon_url) {echo "<img src='".$icon_url."' width='128' height='128'>";}
		echo "</td>";

		echo "<td width='20'></td><td>";

			echo "<table><tr><td>";

				// --- plugin title ---
				echo "<h3 style='font-size:20px;'><a href='".$args['home']."' style='text-decoration:none;'>".$args['title']."</a></h2></a>";

			echo "</td><td width='20'></td>";

			// --- plugin version ---
			echo "<td><h3>v".$args['version']."</h3></td></tr>";

			echo "<tr><td colspan='3' align='center'>";

				echo "<table><tr><td align='center'>";

					// ---- plugin author ---
					// 1.0.8: check if author URL is set
					if (isset($args['author_url'])) {
						echo "<font style='font-size:16px;'>".__('by')."</font> ";
						echo "<a href='".$args['author_url']."' target=_blank style='text-decoration:none;font-size:16px;' target=_blank><b>".$args['author']."</b></a><br><br>";
					}

					// --- readme / docs / support links ---
					// 1.0.8: use filtered links array with automatic separator
					$links = array();
					if (!isset($args['readme']) || ($args['readme'] !== false)) {
						$readme_url = add_query_arg('action', $namespace.'_readme_viewer', admin_url('admin-ajax.php'));
						$links[] = "<a href='".$readme_url."' class='pluginlink smalllink thickbox' title='readme.txt'><b>".__('Readme')."</b></a>";
					}
					if (isset($args['docs'])) {$links[] = "<a href='".$args['docs']."' class='pluginlink smalllink' target=_blank><b>".__('Docs')."</b></a>";}
					if (isset($args['support'])) {$links[] = "<a href='".$args['support']."' class='pluginlink smalllink' target=_blank><b>".__('Support')."</b></a>";}

					$links = apply_filters($args['namespace'].'_plugin_links', $links);
					if (count($links) > 0) {echo implode(' | ', $links);}

					// --- author icon ---
					if ($author_icon_url) {
						echo "</td><td>";
						$author_image = "<img src='".$author_icon_url."' width='64' height='64' border='0'>";
						// 1.0.8: check if author URL is set for link
						if (isset($args['author_url'])) {
							$author_image = "<a href='".$args['author_url']."' target=_blank>".$author_image."</a>";
						}
						echo $author_image;
					}

				echo "</td></tr></table>";

			echo "</td></tr></table>";

		echo "</td><td width='50'></td><td style='vertical-align:top;'>";

			// --- plugin supporter links ---
			// 1.0.1: set rate/share/donate links and texts
			// 1.0.8: added filters for rate/share/donate links
			echo "<br>";

			// --- Rate link ---
			if (isset($args['wporgslug'])) {
				if (isset($args['rate'])) {$rate_url = $args['rate'];}
				elseif (isset($args['type']) && ($args['type'] == 'theme')) {
					$rate_url = 'https://wordpress.org/support/theme/'.$args['wporgslug'].'/reviews/#new-post';
				} else {$rate_url = 'https://wordpress.org/plugins/'.$args['wporgslug'].'/reviews/#new-post';}
				if (isset($args['ratetext'])) {$rate_text = $args['ratetext'];}
				else {$rate_text = __('Rate on WordPress.Org');}
				$rate_link = "<a href='".$rate_url."' class='pluginlink' target='_blank'>";
				$rate_link .= "<span style='font-size:24px; color:#FC5; margin-right:10px;' class='dashicons dashicons-star-filled'></span> ";
				$rate_link .= $rate_text."</a><br><br>";
				$rate_link = apply_filters($args['namespace'].'_rate_link', $rate_link);
				echo $rate_link;
			}

			// --- Share link ---
			if (isset($args['share'])) {
				if (isset($args['sharetext'])) {$share_text = $args['sharetext'];}
				else {$share_text = __('Share the Plugin Love');}
				$share_link = "<a href='".$args['share']."' class='pluginlink' target='_blank'>";
				$share_link .= "<span style='font-size:24px; color:#E0E; margin-right:10px;' class='dashicons dashicons-share'></span> ";
				$share_link .= $share_text."</a><br><br>";
				$share_link = apply_filters($args['namespace'].'_share_link', $share_link);
				echo $share_link;
			}

			// --- Donate link ---
			if (isset($args['donate'])) {
				if (isset($args['donatetext'])) {$donate_text = $args['donatetext'];}
				else {$donate_text = __('Support this Plugin');}
				$donate_link = "<a href='".$args['donate']."' class='pluginlink' target='_blank'>";
				$donate_link .= "<span style='font-size:24px; color:#E00; margin-right:10px;' class='dashicons dashicons-heart'></span> ";
				$donate_link .= "<b>".$donate_text."</b></a><br><br>";
				$donate_link = apply_filters($args['namespace'].'_donate_link', $donate_link);
				echo $donate_link;
			}
		echo "</td></tr>";

		// --- output updated and reset messages ---
		if (isset($_GET['updated'])) {
			if ($_GET['updated'] == 'yes') {$message = $settings['title'].' '.__('Settings Updated.');}
			elseif ($_GET['updated'] == 'no') {$message = __('Error! Settings NOT Updated.');}
			elseif ($_GET['updated'] == 'reset') {$message = $settings['title'].' '.__('Settings Reset!');}
			if (isset($message)) {
				echo "<tr><td></td><td></td><td align='center'>".$this->message_box($message, false)."</td></tr>";
			}
		} else {
			// --- maybe output welcome message ---
			if (isset($_REQUEST['welcome']) && ($_REQUEST['welcome'] == 'true')) {
				if (isset($args['welcome'])) {
					echo "<tr><td colspan='3' align='center'>".$this->message_box($args['welcome'], false)."</td></tr>";
				}
			}
		}

		echo "</table><br>";
	}

	// -------------
	// Settings Page
	// -------------
	function settings_page() {

		$namespace = $this->namespace;

		// --- open page wrapper ---
		echo "<div id='pagewrap' class='wrap' style='width:100%;margin-right:0px !important;'>";

		do_action($namespace.'_admin_page_top');

		// --- output settings header ---
		$this->settings_header();

		do_action($namespace.'_admin_page_middle');

		// --- output settings table ---
		$this->settings_table();

		do_action($namespace.'_admin_page_bottom');

		// --- close page wrapper ---
		echo "</div>";
	}

	// --------------
	// Settings Table
	// --------------
	// 1.0.9: created automatic Plugin Settings page
	// (based on passed plugin options and default settings)
	function settings_table() {

		// --- get all options and settings (unfiltered) ---
		$args = $this->args; $namespace = $this->namespace;
		$options = $this->options;
		$defaults = $this->default_settings();
		$settings = $this->get_settings(false);

		// --- get option tabs and sections ---
		$tabs = $this->tabs;
		$sections = $this->sections;

		if (isset($settings['settingstab'])) {
			$currenttab = $settings['settingstab'];
		} else {$currenttab = '';}

		// --- loop options to maybe get tabbed groupings ---
		$taboptions = array();
		foreach ($options as $key => $option) {
			if (isset($option['tab']) && array_key_exists($option['tab'], $tabs)) {
				if ( (count($sections) > 0) && isset($option['section']) ) {
					$taboptions[$option['tab']][$option['section']][$key] = $option;
				} else {$taboptions[$option['tab']]['general'][$key] = $option;}
			} else {
				if ( (count($sections) > 0) && isset($option['section']) ) {
					$taboptions['general'][$option['section']][$key] = $option;
				} else {$taboptions['general']['general'][$key] = $option;}
			}
		}

		// --- maybe push general section to top of tab ---
		foreach ($taboptions as $tab => $tabsections) {
			if (isset($tabsections['general']) && (count($tabsections) > 1)) {
				$general = $tabsections['general']; unset($tabsections['general']);
				$taboptions[$tab] = array_merge($general, $tabsections);
			}
		}

		// --- maybe output tab groupings ---
		if (count($tabs) > 0) {

			// --- output tab switcher script ---
			echo "<script>function show_settings_tab(tab) {".PHP_EOL;
			foreach ($tabs as $tab => $label) {
				echo "	document.getElementById('".$tab."-tab-button').className = 'settings-tab-button inactive';".PHP_EOL;
				echo "	document.getElementById('".$tab."-tab').className = 'settings-tab inactive'; ".PHP_EOL;
			}
			echo "	document.getElementById(tab+'-tab-button').className = 'settings-tab-button active';".PHP_EOL;
			echo "	document.getElementById(tab+'-tab').className = 'settings-tab active';".PHP_EOL;
			echo "	document.getElementById('settings-tab').value = tab;".PHP_EOL;
			echo "}</script>";

			$i = 0;
			echo "<ul id='settings-tab-buttons'>";
			foreach ($tabs as $tab => $tablabel) {
				if ( ($tab == $currenttab) || ( ($currenttab == '') && ($i == 0) ) ) {
					$class = 'active';
				} else {$class = 'inactive';}
				$onclick = 'show_settings_tab("'.$tab.'")';
				echo "<li id='".$tab."-tab-button' class='settings-tab-button ".$class."' onclick='".$onclick."'>".$tablabel."</li>";
				$i++;
			}
			echo "</ul>";
		} else {
			$tabs = array('general' => __('General'));
		}

		// --- reset to default script ---
		$confirmreset = __('Are you sure you want to reset this plugin to default settings?');
		echo "<script>function resettodefaults() {
			agree = confirm('".$confirmreset."'); if (!agree) {return false;}
			document.getElementById('settings-action').value = 'reset';
			document.getElementById('settings-form').submit();
		}</script>";

		// --- start settings form ---
		echo "<form method='post' id='settings-form'>";
		echo "<input type='hidden' name='".$namespace."_update_settings' id='settings-action' value='yes'>";
		echo "<input type='hidden' name='".$args['settings']."_settingstab' id='settings-tab' value='".$currenttab."'>";
		wp_nonce_field($args['slug']);
		if (isset($_REQUEST['debug'])) {
			if ( ($_REQUEST['debug'] == 'yes') || ($_REQUEST['debug'] == 'yes') ) {
				echo "<input type='hidden' name='debug' value='yes'>";
			}
		}

		// ---- open wrapbox ---
		echo "<div id='wrapbox' class='postbox' style='line-height:2em;'>";
		echo "<div class='inner' style='padding-left:20px;'>";

		// --- output tabbed sections ---
		$i = 0;
		foreach ($tabs as $tab => $tablabel) {

			// --- open tab table output ---
			if ( ($currenttab == $tab) || ( ($currenttab == '') && ($i == 0) ) ) {
				$class = 'active';
			} else {$class = 'inactive';}
			echo "<div id='".$tab."-tab' class='settings-tab ".$class."'>";

			do_action($namespace.'_admin_page_tab_'.$tab.'_top');

			echo "<table cellpadding='0' cellspacing='0'>";

			if (count($sections) > 0) {

				$sectionheadings = array();
				foreach ($sections as $section => $sectionlabel) {

					if (array_key_exists($section, $taboptions[$tab])) {

						// --- section top ---
						ob_start();
						do_action($namespace.'_admin_page_section_'.$section);
						$output = ob_get_clean();
						if ( $output ) {
						 	echo "<tr class='setting-section-bottom'><td colspan='5'>" . $output . "</td></tr>";
						}

						// --- section heading ---
						if (!isset($sectionheadings[$section])) {
							echo "<tr class='setting-section'>";
								echo "<td colspan='5'><h3>".$sectionlabel."</h3></td>";
							echo "</tr>";
							$sectionheadings[$section] = true;
						}

						// --- section setting rows ---
						foreach ($taboptions[$tab][$section] as $key => $option) {
							$option['key'] = $key;
							echo $this->setting_row($option);
						}
						echo "<tr height='25'><td> </td></tr>";

						// --- section bottom hook ---
						ob_start();
						do_action($namespace.'_admin_page_section_'.$section);
						$output = ob_get_clean();
						if ( $output ) {
						 	echo "<tr class='setting-section-bottom'><td colspan='5'>" . $output . "</td></tr>";
						}

					}

				}
			} else {
				foreach ($taboptions[$tab]['general'] as $key => $option) {
					$option['key'] = $key;
					echo "<tr height='25'><td> </td></tr>";
					echo $this->setting_row($option);
					echo "<tr height='25'><td> </td></tr>";
				}
			}

			// --- reset/save settings buttons ---
			// (filtered so removable from any specific tab)
			$buttons = "<tr height='25'><td> </td></tr>";
			$buttons .= "<tr><td align='center'>";
				$buttons .= "<input type='button' class='button-secondary settings-button' onclick='return resettodefaults();' value='".__('Reset Settings')."'>";
			$buttons .= "</td><td colspan='3'></td><td align='center'>";
				$buttons .= "<input type='submit' class='button-primary settings-button' value='".__('Save Settings')."'>";
			$buttons .= "</td></tr>";
			$buttons .= "<tr height='25'><td></td></tr>";
			$buttons = apply_filters($namespace.'_admin_save_buttons', $buttons, $tab);
			if ($buttons) {echo $buttons;}

			// --- close table ---
			echo "</table>";

			// --- do below tab action ---
			do_action($namespace.'_admin_page_tab_'.$tab.'_bottom');

			// --- close tab output ---
			echo "</div>";

			$i++;
		}

		// --- close wrapbox ---
		echo "</div></div>";

		// --- close settings form ---
		echo "</form>";

		// --- number input step script ---
		echo "<script>function number_step(updown, id, min, max, step) {
			if (updown == 'up') {multiplier = 1;}
			if (updown == 'down') {multiplier = -1;}
			current = parseInt(document.getElementById(id).value);
			newvalue = current + (multiplier * parseInt(step));
			if (newvalue < parseInt(min)) {newvalue = min;}
			if (newvalue > parseInt(max)) {newvalue = max;}
			document.getElementById(id).value = newvalue;
		}</script>";
		
		// --- enqueue settings styles ---
		add_action('admin_footer', array($this, 'setting_styles'));

	}

	// ------------
	// Setting Row
	// ------------
	// 1.0.9: added for automatic Settings table generation
	function setting_row($option) {

		// --- prepare setting keys ---
		$args = $this->args; $namespace = $this->namespace;
		$postkey = $args['settings'];
		$name = $postkey.'_'.$option['key'];
		$type = $option['type'];
		$setting = $this->get_setting($option['key'], false);

		// --- convert old option type names ---
		if ($type == 'email') {$type = 'text'; $option['options'] = 'EMAIL';}
		elseif ($type == 'url') {$type = 'text'; $option['options'] = 'URL';}
		elseif ($type == 'alpabetic') {$type = 'text'; $option['options'] = 'ALPHABETIC';}
		elseif ($type == 'alphanumeric') {$type = 'text'; $option['options'] = 'ALPHANUMERIC';}
		elseif ($type == 'numeric') {$option['options'] = 'NUMERIC';}
		elseif ($type == 'numeric') {$option['options'] = 'NUMERIC';}
		elseif ($type == 'usernames') {$type = 'text'; $option['options'] = 'USERNAME';}
		elseif ($type == 'csvslugs') {$type = 'text'; $option['options'] = 'SLUG';}
		elseif ($type == 'emails') {$type = 'text'; $option['options'] = 'EMAIL';}
		elseif ($type == 'urls') {$type = 'text'; $option['options'] = 'URL';}
		elseif ($type == 'pageid') {$type = 'select'; $option['options'] = 'PAGEID';}
		elseif ($type == 'postid') {$type = 'select'; $option['options'] = 'POSTID';}
		elseif ($type == 'pageids') {$type = 'multiselect'; $option['options'] = 'PAGEIDS';}
		elseif ($type == 'postids') {$type = 'multiselect'; $option['options'] = 'POSTIDS';}

		// --- prepare row output ---
		$row = "<tr class='settings-row'>";

			$row .= "<td class='settings-label'>".$option['label'];
			if ($type == 'multiselect') {
				$row .= "<br><span>".__('Use Ctrl and Click to Select')."</span>";
			}
			$row .= "</td><td width='25'></td>";

			// 1.0.9: added multiple cell spanning note type
			if ( ($type == 'note') || ($type == 'info') || ($type == 'helper') ) {
				
				$row .= "<td class='settings-helper' colspan='3'>";				
					if (isset($option['helper'])) {$row .= $option['helper'];}				
				$row .= "</td>";
				
			} else {

				// TODO: add check if already Pro version
 				if (isset($option['pro']) && $option['pro']) {

					// --- Pro version setting (teaser) ---
					$row .= "<td class='settings-input setting-pro'>";
						$upgrade_link = false;
						if ( $args['hasplans'] || $args['hasaddons'] ) {
							$upgrade_link = add_query_arg( 'page=', $args['slug'].'-pricing', admin_url('admin.php'));
							$target = '';
						}  elseif (isset($args['upgrade_link'])) {
							$upgrade_link = $args['upgrade_link']; 
							$target = " target='_blank'";
						}
						if ($upgrade_link) {
							$row .= __('Available in Pro Version.') . '<br>';
							$row .= "<a href='".$upgrade_link."'".$target.">" . __('Click Here to Upgrade!') ."</a>";
						} else {
							$row .= __('Coming soon in Pro version!');
						}
					$row .= "</td>";

				} else {
					
					$row .= "<td class='settings-input'>";

						// --- maybe prepare special options ---
						if (isset($option['options']) && is_string($option['options'])) {

							// --- maybe prepare post/page options (once) ---
							if (in_array($option['options'], array('POSTID', 'POSTIDS', 'PAGEID', 'PAGEIDS'))) {

								$posttype = strtolower(substr($option['options'], 0, 4));
								if ( ( ($posttype == 'page') && !isset($pageoptions) )
								  || ( ($posttype == 'post') && !isset($postoptions) ) ) {
									$pageoptions = $postoptions = array('' => '');
									global $wpdb;
									$query = "SELECT ID,post_title,post_status FROM ".$wpdb->prefix."posts ";
									$query .= " WHERE post_type = %s AND post_status != 'auto-draft'";
									$query = $wpdb->prepare($query, $posttype);
									$results = $wpdb->get_results($query, ARRAY_A);
									if ($results && (count($results) > 0)) {
										foreach ($results as $result) {
											if (strlen($result['post_title']) > 35) {
												$result['post_title'] = substr($result['post_title'], 0, 35).'...';
											}
											$label = $result['ID'].': '.$result['post_title'];
											if ($result['post_status'] != 'publish') {$label .= ' ('.$result['post_status'].')';}
											if ($posttype == 'page') {$pageoptions[$result['ID']] = $label;}
											elseif ($posttype == 'post') {$postoptions[$result['ID']] = $label;}
										}
									}
								}
								if ($posttype == 'page') {$option['options'] = $pageoptions;}
								elseif ($posttype == 'post') {$option['options'] = $postoptions;}
							}

							// --- maybe prepare public post type options (once) ---
							if (in_array($option['options'], array('PUBLICTYPE', 'PUBLICTYPES'))) {
								if (!isset($publicoptions)) {
									$cpts = array('page', 'post');
									$args = array('public' => true, '_builtin' => false);
									$cptlist = get_post_types($args, 'names', 'and');
									$cpts = array_merge($cpts, $cptlist);
									foreach ($cpts as $cpt) {
										$posttypeobject = get_post_type_object($cpt);
										$label = $posttypeobject->labels->singular_name;
										$publicoptions[$cpt] = $label;
									}
								}
								$option['options'] = $publicoptions;
							}

							// --- maybe prepare post type options (once) ---
							if (in_array($option['options'], array('POSTTYPE', 'POSTTYPES'))) {
								if (!isset($cptoptions)) {
									$cpts = array('page', 'post');
									$args = array('_builtin' => false);
									$cptlist = get_post_types($args, 'names', 'and');
									$cpts = array_merge($cpts, $cptlist);
									foreach ($cpts as $cpt) {
										$posttypeobject = get_post_type_object($cpt);
										$label = $posttypeobject->labels->singular_name;
										$cptoptions[$cpt] = $label;
									}
								}
								$option['options'] = $cptoptions;
							}

							// --- maybe prepare all post type options (once) ---
							if (in_array($option['options'], array('ALLTYPE', 'ALLTYPES'))) {
								if (!isset($allcptoptions)) {
									$args = array('_builtin' => true);
									$cpts = get_post_types($args, 'names', 'and');
									foreach ($cpts as $cpt) {
										$posttypeobject = get_post_type_object($cpt);
										$label = $posttypeobject->labels->singular_name;
										$allcptoptions[$cpt] = $label;
									}
								}
								$option['options'] = $allcptoptions;
							}

							// --- maybe prepare user options (once) ---
							if (in_array($option['options'], array('USERID', 'USERIDS', 'USERNAME', 'USERNAMES'))) {
								if (in_array($option['options'], array('USERID', 'USERIDS'))) {
									$userkey = 'userid';
								} else {$userkey = 'username';}
								$option['options'] = array('' => '');
								global $wpdb;
								$query = "SELECT ID,user_login,display_name FROM ".$wpdb->prefix."users";
								$results = $wpdb->get_results($query, ARRAY_A);
								if ($results && (count($results) > 0)) {
									foreach ($results as $result) {
										$label = $result['ID'].': '.$result['display_name'].' ('.$result['user_login'].')';
										if ($userkey == 'userid') {$useroptions[$result['ID']] = $label;}
										elseif ($userkey == 'username') {$useroptions[$result['user_login']] = $label;}
									}
								}
								$option['options'] = array_merge($option['options'], $useroptions);
							}

						}

						// --- output option input types ---
						if ($type == 'toggle') {

							// --- toggle ---
							// 1.0.9: add toggle input (styled checkbox)
							if ($setting == $option['value']) {$checked = ' checked';} else {$checked = '';}
							$row .= "<label for='".$name."' class='setting-toggle'>";
								$row .= "<input type='checkbox' name='".$name."' class='setting-toggle' value='".$option['value']."'".$checked.">";
								$row .= "<span class='setting-slider round'></span>";
							$row .= "</label>";
							if (isset($option['suffix'])) {$row .= " ".$option['suffix'];}

						} elseif ($type == 'checkbox') {

							// --- checkbox ---
							if ($setting == $option['value']) {$checked = ' checked';} else {$checked = '';}
							$row .= "<input type='checkbox' name='".$name."' class='setting-checkbox' value='".$option['value']."'".$checked.">";
							if (isset($option['suffix'])) {$row .= " ".$option['suffix'];}

						}  elseif ($type == 'multicheck') {

							// --- multicheck boxes ---
							$checkboxes = array();
							foreach ($option['options'] as $key => $label) {
								if (is_array($setting) && in_array($key, $setting)) {$checked = ' checked';} else {$checked = '';}
								$checkboxes[] = "<input type='checkbox' name='".$name."-".$key."' class='setting-checkbox' value='yes'".$checked."> ".$label;
							}
							$row .= implode("<br>", $checkboxes);

						} elseif ($type == 'radio') {

							// --- radio buttons ---
							$radios = array();
							foreach ($option['options'] as $value => $label) {
								if ($setting === $value) {$checked = " checked";} else {$checked = '';}
								$radios[] = "<input type='radio' class='setting-radio' name='".$name."' value='".$value."'".$checked."> ".$label;
							}
							$row .= implode('<br>', $radios);

						} elseif ($type == 'select') {

							// --- select dropdown ---
							$row .= "<select class='setting-select' name='".$name."'>";
							foreach ($option['options'] as $value => $label) {	
								// 1.0.9: support option grouping (set unique key containing OPTGROUP-)
								if (strstr($value, '*OPTGROUP*')) {
									$row .= "<optgroup label='".$label."'>".$label.'</optgroup>';
								} else {
									if ($setting === $value) {$selected = " selected='selected'";} else {$selected = '';}
									$row .= "<option value='".$value."'".$selected.">".$label."</option>";
								}
							}
							$row .= "</select>";
							if (isset($option['suffix'])) {$row .= " ".$option['suffix'];}

						} elseif ($type == 'multiselect') {

							// --- multiselect dropdown ---
							$row .= "<select multiple='multiple' class='setting-select' name='".$name."[]'>";
							foreach ($option['options'] as $value => $label) {
								if ($value != '') {
									if ($value === 'OPTGROUP') {
										$row .= "<optgroup label='".$label."'>";
									} else {
										if (is_array($setting) && in_array($value, $setting)) {$selected = " selected='selected'";} else {$selected = '';}
										$row .= "<option value='".$value."'".$selected.">".$label."</option>";
									}
								}
							}
							$row .= "</select>";
							if (isset($option['suffix'])) {$row .= " ".$option['suffix'];}

						} elseif ($type == 'text') {

							// --- text inputs ---
							$class = 'setting-text';
							if ($type != 'text') {$class .= ' setting-'.$type;}
							if (isset($option['placeholder'])) {$placeholder = " placeholder='".$option['placeholder']."'";} else {$placeholder = '';}
							$row .= "<input type='text' name='".$name."' class='".$class."' value='".$setting."'".$placeholder.">";
							if (isset($option['suffix'])) {$row .= " ".$option['suffix'];}

						} elseif ($type == 'textarea') {

							// --- textarea input ---
							if (isset($option['rows'])) {$rows = " rows='".$option['rows']."'";} else {$rows = '';}
							if (isset($option['placeholder'])) {$placeholder = " placeholder='".$option['placeholder']."'";} else {$placeholder = '';}
							$row .= "<textarea class='setting-textarea' name='".$name."'".$rows.$placeholder.">".$setting."</textarea>";

						} elseif ( ($type == 'numeric') || ($type == 'number') ) {

							// --- numeric text input ---
							// note: step key is only used for controls, not for validation
							if (isset($option['placeholder'])) {$placeholder = " placeholder='".$option['placeholder']."'";} else {$placeholder = '';}
							if (isset($option['min'])) {$min = $option['min'];} else {$min = false;}
							if (isset($option['max'])) {$max = $option['max'];} else {$max = false;}
							if (isset($option['step'])) {$step = $option['step'];} else {$step = 1;}
							$onclickup = 'number_step("up", "'.$name.'", '.$min.', '.$max.', '.$step.');';
							$onclickdown = 'number_step("down", "'.$name.'", '.$min.', '.$max.', '.$step.');';
							$row .= "<input class='setting-button button-secondary' type='button' value='-' onclick='".$onclickdown."'>";
							$row .= "<input class='setting-numeric' type='text' name='".$name."' id='".$name."' value='".$setting."'".$placeholder.">";
							$row .= "<input class='setting-button button-secondary' type='button' value='+' onclick='".$onclickup."'>";
							if (isset($option['suffix'])) {$row .= " ".$option['suffix'];}

						}

					$row .= "</td>";
				}

				// --- setting helper text ---
				if (isset($option['helper'])) {
					$row .= "<td width='25'></td>";
					$row .= "<td class='settings-helper'>".$option['helper']."</td>";
				}
			}

		$row .= "</tr>";

		// --- settings row spacer ---
		$row .= "<tr class='settings-spacer'><td> </td></tr>";

		// --- filter and return setting row ---
		$row = apply_filters($namespace.'_setting_row', $row, $option);
		return $row;
	}

	// ---------------
	// Settings Styles
	// ---------------
	function setting_styles() {

		$styles = array();

		// --- page styles ---
		$styles[] = '#wrapbox {margin-right: 20px;}';

		// --- settings tab styles ---
		$styles[] = '.settings-tab-button {display:inline-block; font-size:15px; padding:7px 14px; margin-right:20px; border-radius:7px;}';
		$styles[] = '.settings-tab-button.active {font-weight:bold; background-color:#0073aa; color:#FFF; border:1px solid #FFF;}';
		$styles[] = '.settings-tab-button.inactive {font-weight:bold; background-color:#F5F5F5; color:#0073aa; border:1px solid #000;}';
		$styles[] = '.settings-tab-button.inactive:hover {background-color:#FFFFFF; color:#00a0d2;}';
		$styles[] = '.settings-tab.active {display:block;}';
		$styles[] = '.settings-tab.inactive {display:none;}';

		// --- setting row styles ---
		$styles[] = '.settings-label {vertical-align:top; font-weight:bold; min-width:100px; max-width:200px;}';
		$styles[] = '.settings-label span {font-weight: normal; font-size:10px;}';
		$styles[] = '.settings-helper {vertical-align:top; font-style:italic; min-width:200px; max-width:300px;}';
		$styles[] = '.settings-spacer {height: 7px;}';

		// --- setting input styles ---
		$styles[] = '.settings-input {vertical-align:top; min-width:100px; max-width:300px;}';
		$styles[] = '.settings-input input.setting-radio {}';
		$styles[] = '.settings-input input.setting-checkbox {}';
		$styles[] = '.settings-input input.setting-text {width:100%;}';
		$styles[] = '.settings-input input.setting-numeric {display:inline-block; width:50%; text-align:center;}';
		$styles[] = '.settings-input input.setting-button {display:inline-block; padding:0px 5px;}';
		$styles[] = '.settings-input input.setting-textarea {width:100%;}';
		$styles[] = '.settings-input select.setting-select {min-width:100px;}';

		// --- toggle input styles ---
		// Ref: https://www.w3schools.com/howto/howto_css_switch.asp
		$styles[] = '
		.setting-toggle {position:relative; display:inline-block; width:30px; height:17px;}
		.setting-toggle input {opacity:0; width:0; height:0;}
		.setting-slider {position:absolute; cursor:pointer;
		  top:0; left:0; right:0; bottom:0; background-color:#ccc;
		  -webkit-transition:.4s; transition:.4s;
		}
		.setting-slider:before {position:absolute; content:""; height:13px; width:13px;
		  left:2px; bottom:2px; background-color:white; -webkit-transition:.4s; transition:.4s;
		}
		input:checked + .setting-slider {background-color: #2196F3;}
		input:focus + .setting-slider {box-shadow: 0 0 1px #2196F3;}
		input:checked + .setting-slider:before {
		  -webkit-transform:translateX(13px); -ms-transform:translateX(13px); transform:translateX(13px);
		}
		.setting-slider.round {border-radius: 17px;}
		.setting-slider.round:before {border-radius: 50%;}
		';


		// --- filter and output styles ---
		$namespace = $this->namespace;
		$styles = apply_filters($namespace.'_admin_page_styles', $styles);
		echo "<style>".implode("\n", $styles)."</style>";

	}

 }
} // end Plugin Loader Class


// ----------------------------------
// Load Namespaced Prefixed Functions
// ----------------------------------
// [Optional] rename functions prefix to your plugin namespace
// these functions will then be available within your plugin
// to more easily call the matching plugin loader class methods

// 1.0.3: added priority of 0 to prefixed function loading action
add_action('plugins_loaded', 'radio_station_load_prefixed_functions', 0);

if (!function_exists('radio_station_load_prefixed_functions')) {
 function radio_station_load_prefixed_functions() {

	// auto-magic namespacing note
	// ---------------------------
	// the below functions use the function name to grab and load the corresponding class method
	// all function name suffixes here must be two words for the magic namespace grabber to work
	// ie. _add_settings, because the namespace is taken from *before the second-last underscore*

	if (!function_exists('radio_station_loader_instance')) {
		function radio_station_loader_instance() {
			$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
			return $GLOBALS[$namespace.'_instance'];
		}
	}

	// -------------------
	// Get Loader Instance
	// -------------------
	// 2.3.0: added function for getting loader class instance
	if (!function_exists('radio_station_loader_instance')) {
	 function radio_station_loader_instance() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance;
	 }
	}

	// ---------------
	// Get Plugin Data
	// ---------------
	// 2.3.0: added function for getting plugin data
	if (!function_exists('radio_station_plugin_data')) {
	 function radio_station_plugin_data() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance->plugin_data();
	 }
	}

	// ------------
	// Add Settings
	// ------------
	if (!function_exists('radio_station_add_settings')) {
	 function radio_station_add_settings() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->add_settings();
	 }
	}

	// ------------
	// Get Defaults
	// ------------
	if (!function_exists('radio_station_default_settings')) {
	 function radio_station_default_settings($key=false) {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance->default_settings($key);
	 }
	}

	// -----------
	// Get Options
	// -----------
	if (!function_exists('radio_station_get_options')) {
	 function radio_station_get_options() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance->options;
	 }
	}

	// -----------
	// Get Setting
	// -----------
	if (!function_exists('radio_station_get_setting')) {
	 function radio_station_get_setting($key, $filter=true) {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance->get_setting($key, $filter);
	 }
	}

	// ----------------
	// Get All Settings
	// ----------------
	// 1.0.9: added missing get_settings prefixed function
	if (!function_exists('radio_station_get_settings')) {
	 function radio_station_get_settings($filter=true) {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance->get_settings($filter);
	 }
	}

	// --------------
	// Reset Settings
	// --------------
	if (!function_exists('radio_station_reset_settings')) {
	 function radio_station_reset_settings() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->reset_settings();
	 }
	}

	// ---------------
	// Update Settings
	// ---------------
	if (!function_exists('radio_station_update_settings')) {
	 function radio_station_update_settings() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->update_settings();
	 }
	}

	// ---------------
	// Delete Settings
	// ---------------
	if (!function_exists('radio_station_delete_settings')) {
	 function radio_station_delete_settings() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->delete_settings();
	 }
	}

	// -----------------
	// Set Pro Namespace
	// -----------------
	if (!function_exists('radio_station_pro_namespace')) {
	 function radio_station_pro_namespace($pronamespace) {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->pro_namespace($pronamespace);
	 }
	}

	// -----------
	// Message Box
	// -----------
	if (!function_exists('radio_station_message_box')) {
	 function radio_station_message_box($message, $echo=false) {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance->message_box($message, $echo);
	 }
	}

	// ---------------
	// Settings Header
	// ---------------
	if (!function_exists('radio_station_settings_header')) {
	 function radio_station_settings_header() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->settings_header();
	 }
	}

	// -------------
	// Settings Page
	// -------------
	if (!function_exists('radio_station_settings_page')) {
	 function radio_station_settings_page() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->settings_page();
	 }
	}

	// --------------
	// Settings Table
	// --------------
	// 1.0.9: added for standalone setting table output
	if (!function_exists('radio_station_settings_table')) {
	 function radio_station_settings_table() {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->settings_table();
	 }
	}

	// ------------
	// Settings Row
	// ------------
	// 1.0.9: added for standalone setting row output
	if (!function_exists('radio_station_settings_row')) {
	 function radio_station_settings_row($option, $setting) {
		$f = __FUNCTION__; $namespace = substr($f,0,strrpos($f,'_',(strrpos($f,'_')-strlen($f)-1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->settings_row($option, $setting);
	 }
	}

 }
}

// fully loaded
// ------------


// =========
// CHANGELOG
// =========

// == 1.0.9 ==
// - added automated Settings table and Admin Page output
// - added tab and section support to options array
// - added tab and section bottom and top action hooks
// - added multicheck, multiselect and toggle input types
// - added post type and post ID input validation
// - added _loader_instance and _get_settings prefixed functions
// - refactor separate setting update inputs and validation
// - fix to check if plugin icon PNG exists

// == 1.0.8 ==
// - added WordQuest plugin flag (for use by non-WQ plugins)
// - apply settings key filters when getting all settings
// - added filters for rate/share/donate links
// - added page ID to settings input types

// == 1.0.7 ==
// - fix support URL undefined variable warning
// - change readme.php to reader.php (for GitHub)

// == 1.0.6 ==
// - added global options filter
// - added 'emails' option type for multiple email saving
// - fix for new unchecked checkbox value
// - fix for typos in URL option type saving

// == 1.0.5 ==
// - fix for undefined account and support variables

// == 1.0.4 ==
// - added 'url' option type checking and saving
// - added 'csv' option type checking and saving
// - added 'csvslugs' option type checking and saving

// == 1.0.3 ==
// - added priority of 0 to prefixed function loading action
// - only use namespace not settings key for updates
// - added 'email' option type checking and save
// - added 'usernames' option type checking and save

// == 1.0.2 ==
// - fix some Freemius loading argument checks
// - fix namespace typo in update/reset triggers
// - add check for author icon file and fileback
// - add allowance for special settings processing
// - add allowance for auto-load of multiple Pro files

// == 1.0.1 ==
// - set_basename for Freemius free / premium plugins
// - set rate / share / donate links and text anchors
// - check for multiple Pro filenames and use first one

// == 1.0.0 ==
// - Working Release version

// == 0.9.0 ==
// - Development Version
