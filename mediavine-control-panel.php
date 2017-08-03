<?php
/*
Plugin Name: Mediavine Control Panel
Plugin URI: http://mediavine.com/
Description: Manage your ads, analytics and more with our lightweight plugin!
Version: 1.3.9
Author: mediavine
Author URI: http://mediavine.com
License: GPL2
*/

require_once('lib/mvbase.php');
require_once('lib/mvextension.php');

if (!class_exists('MVCP')) {
    class MVCP extends MVBase
    {
        public $EXTENSION_MAP = array(
            'amp' => 'MVAMP',
            'security' => 'MVSecurity',
            'debug' => 'MVDebug'
        );

        // Constants/Config
        public $SETTINGS = array(
            'include_script_wrapper' => 'bool',
            'site_id' => 'string',
        );

        public $SETTINGS_DEFAULTS = array(
            'include_script_wrapper' => FALSE,
            'site_id' => '',
        );

        public $SETTING_PREFIX = 'MVCP_';

        private $_extensions = array();

        public function __construct()
        {
            parent::__construct($this);

            $this->init_views();
            $this->init_plugin_filters();
            $this->load_extensions();
        }

        public static function activate()
        {
        }

        public static function deactivate()
        {
        }

        public function init_views()
        {
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        public function init_plugin_filters()
        {
        }

        private function load_extensions()
        {
            foreach ($this->EXTENSION_MAP as $extension_name => $class_name) {
                try {
                    require_once("extensions/{$extension_name}/{$extension_name}.php");
                    $this->load_extension_class($extension_name, $class_name);
                } catch (Exception $e) {
                    // TODO: Error handling
                }
            }
        }

        private function load_extension_class($extension_name, $class_name)
        {
            $instance = new $class_name($this);
            $this->_extensions[$extension_name] = $instance;
        }

        // Hooks
        public function admin_init()
        {
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));

            $this->initialize_settings();
            $this->get_extension('amp')->initialize_settings();
            $this->get_extension('security')->initialize_settings();
        }

        public function add_action_links($links)
        {
            return array_merge($links, array(
                '<a href="' . admin_url( 'options-general.php?page=mediavine_amp_settings' ) . '">Settings</a>',
            ));
        }

        public function admin_menu()
        {
            add_options_page('Mediavine Control Panel', 'Mediavine Control Panel', 'manage_options', 'mediavine_amp_settings', array(
                $this,
                'render_settings_page'
            ));
        }

        public function enqueue_scripts()
        {
            $site_id = $this->option('site_id');
            $use_wrapper = $this->option('include_script_wrapper');

            if ($site_id && $use_wrapper) {
                $this->mv_enqueue_script(array(
                    'handle' => 'mv-script-wrapper',
                    'src' => '//scripts.mediavine.com/tags/' . $site_id . '.js',
                    'attr' => array(
                        'async' => 'async',
                        'data-noptimize' => '1',
                        'data-cfasync' => 'false'
                    )
                ));
            }

            if($this->get_extension('amp')->option('disable_amphtml_link')) {
                // Remove the AMP frontend action right before wp_head fires
                remove_action('wp_head', 'amp_frontend_add_canonical');
            }
        }

        // Helpers/Utils

        public function render_settings_page()
        {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            include(sprintf("%s/views/settings.php", dirname(__FILE__)));
        }

        public function hasAMP()
        {
            return is_plugin_active('amp/amp.php');
        }

        public function hasAMPForWP()
        {
            return is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php');
        }

        public function get_extension($name)
        {
            if (array_key_exists($name, $this->_extensions)) {
                return $this->_extensions[$name];
            }

            return FALSE;
        }
    }
}

if (class_exists('MVCP')) {
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('MVCP', 'activate'));
    register_deactivation_hook(__FILE__, array('MVCP', 'deactivate'));

    // instantiate the plugin class
    $MVCP = new MVCP();
}
