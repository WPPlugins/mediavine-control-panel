<?php
//require_once( __DIR__ . '/../lib/mvextension.php' );
if (!class_exists('MVAMP')) {
    class MVAMP extends MVExtension
    {

        public $SETTINGS = array(
            'analytics_code' => 'string',
            'ad_frequency' => 'string',
            'ad_offset' => 'string',
            'use_analytics' => 'bool',
            'ua_code' => 'string',
            'disable_amphtml_link' => 'bool',
            'disable_in_content' => 'bool',
            'disable_sticky' => 'bool'
        );

        public $SETTINGS_DEFAULTS = array(
            'analytics_code' => '',
            'ad_frequency' => 6,
            'ad_offset' => 6,
            'use_analytics' => FALSE,
            'ua_code' => '',
            'disable_amphtml_link' => FALSE,
            'disable_in_content' => FALSE,
            'disable_sticky' => FALSE
        );

        public $SETTING_PREFIX = 'MVCP_';

        public function __construct($mvcp_instance)
        {
            parent::__construct($mvcp_instance);

            add_action('init', array($this, 'init'));
        }

        public function init()
        {
            $this->init_views();
            $this->init_plugin_filters();
        }

        public function init_views()
        {
            add_action('amp_post_template_css', array($this, 'amp_post_template_css'));
        }

        public function init_plugin_filters()
        {
            add_filter('amp_content_sanitizers', array($this, 'load_sanitizer'), 10, 2);
            add_filter('amp_content_embed_handlers', array($this, 'load_embeds'), 10, 2);

            if (!class_exists('Ampforwp_Init') && $this->option('use_analytics')) {
                add_filter('amp_post_template_analytics', array($this, 'amp_post_template_analytics'), 10, 2);
            }
        }

        // Hooks
        /**
         * Use the amp plugin hook rather than enqueue style per
         * https://github.com/Automattic/amp-wp#custom-css
         */
        public function amp_post_template_css()
        {
            require('styles/ad-wrapper.css');
        }

        // Helpers/Utils
        public function hasAMP()
        {
            return $this->mvcp->hasAMP();
        }

        function load_embeds($embed_handler_classes, $post)
        {
            require_once(dirname(__FILE__) . '/embeds/amp-iframe.php');
            require_once(dirname(__FILE__) . '/embeds/amp-sticky-ad.php');

            $embed_handler_classes['MVAMP_iFrame_Embed'] = array();
            $embed_handler_classes['MVAMP_Sticky_Ad_Embed'] = array();

            if (!class_exists('Ampforwp_Init')) {
                require_once(dirname(__FILE__) . '/embeds/amp-ad.php');

                $embed_handler_classes['MVAMP_Ad_Embed'] = array();
            }
            return $embed_handler_classes;
        }

        function load_sanitizer($sanitizer_classes, $post)
        {
            require_once(dirname(__FILE__) . '/sanitizers/mediavine-sanitizer.php');
            $sanitizer_classes['MVAMP_Sanitizer'] = array(
                'site_id' => $this->mvcp->option('site_id'),
                'use_analytics' => $this->option('use_analytics'),
                'ad_offset' => $this->option('ad_offset'),
                'ad_frequency' => $this->option('ad_frequency'),
                'ua_code' => $this->option('ua_code'),
                'disable_in_content' => $this->option('disable_in_content'),
                'disable_sticky' => $this->option('disable_sticky')
            );

            return $sanitizer_classes;
        }

        public function amp_post_template_analytics($analytics)
        {
            if (!is_array($analytics)) {
                $analytics = array();
            }

            $analytics['mv-googleanalytics'] = array(
                'type' => 'googleanalytics',
                'attributes' => array(
                    'id' => 'mvanalytics'
                ),
                'config_data' => array(
                    'vars' => array(
                        'account' => $this->option('ua_code')
                    ),
                    'triggers' => array(
                        'trackPageview' => array(
                            'on' => 'visible',
                            'request' => 'pageview',
                        ),
                    ),
                ),
            );

            return $analytics;
        }
    }
}
?>
