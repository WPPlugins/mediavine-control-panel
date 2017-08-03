<?php

if (!class_exists('MVSecurity')) {
    class MVSecurity extends MVExtension
    {
        public $SETTINGS = array(
            'enable_forced_ssl' => 'bool'
        );

        public $SETTINGS_DEFAULTS = array(
            'enable_forced_ssl' => FALSE,
        );

        public $SETTING_PREFIX = 'MVCP_';

        public function __construct($mvcp_instance)
        {
            parent::__construct($mvcp_instance);

            $this->init_plugin_actions();
        }

        public function init_plugin_actions()
        {
            add_action('send_headers', array($this, 'send_headers' ) );
        }

        public function send_headers()
        {
            if ($this->option('enable_forced_ssl')) {
                header('Content-Security-Policy: upgrade-insecure-requests');
            }
        }

        // Hooks
    }
}

?>
