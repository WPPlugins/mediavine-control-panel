<?php
require_once('mvutil.php');

if (!class_exists('MVBase')) {
    class MVBase
    {
        public $SETTINGS = array();

        public $SETTINGS_DEFAULTS = array();

        public $SETTING_PREFIX = 'MV_';
        public $KEYWORD_ATTR_ONLY = '__SINGLE__';

        protected $_script_attrs = array();

        public $mvcp;

        public function __construct($mvcp_instance)
        {
            $this->mvcp = $mvcp_instance;
            if ($this->has_script_loader_filter()) {
                add_filter('script_loader_tag', array($this, 'filter_script_loader'), 10, 2);
            } else {
                add_filter('clean_url', array($this, 'filter_script_legacy'), 10, 2);
            }
        }

        public function filter_script_loader($tag, $handle)
        {
            if (array_key_exists($handle, $this->_script_attrs)) {
                foreach ($this->_script_attrs[$handle] as $attrName => $value) {
                    if ($this->KEYWORD_ATTR_ONLY === $value) {
                        $tag = str_replace(' src', " {$attrName} src", $tag);
                    } else {
                        $tag = str_replace(' src', " {$attrName}=\"{$value}\" src", $tag);
                    }
                }
            }

            return $tag;
        }

        public function filter_script_legacy($url)
        {
            if (array_key_exists($url, $this->_script_attrs)) {
                foreach ($this->_script_attrs[$url] as $attrName => $value) {
                    $url = "{$url}' {$attrName}='${value}";
                }
            }

            return $url;
        }

        public function get_wp_ver()
        {
            return get_bloginfo('version');
        }

        public function has_script_loader_filter()
        {
            $wp_version = $this->get_wp_ver();
            $pattern = '/\d+\.\d+/';
            if (preg_match($pattern, $wp_version, $match) && is_array($match) && sizeof($match) > 0) {
                return floatval($match[0]) >= 4.1;
            } else {
                return FALSE;
            }
        }


        public function mv_enqueue_script($opts)
        {
            $handle = $opts['handle'];
            $src = $opts['src'];
            $deps = MVUtil::get_or_null($opts, 'deps');
            $ver = MVUtil::get_or_null($opts, 'ver');
            $in_footer = MVUtil::get_or_null($opts, 'in_footer');
            $attr = MVUtil::get_or_null($opts, 'attr');

            $wp_enqueue_args = MVUtil::filter_null(array($handle, $src, $deps, $ver, $in_footer));

            if (is_array($attr)) {
                if ($this->has_script_loader_filter()) {
                    $this->_script_attrs[$handle] = $attr;
                } else {
                    $this->_script_attrs[$src] = $attr;
                }
            }

            call_user_func_array('wp_enqueue_script', $wp_enqueue_args);
        }

        public function get_key($setting_name)
        {
            return $this->SETTING_PREFIX . $setting_name;
        }

        public function initialize_settings()
        {
            $group = $this->SETTING_PREFIX;

            foreach ($this->SETTINGS as $key => $value) {
                register_setting($group, $group . $key);
            }
        }

        public function option($name, $newValue = null)
        {
            $key = $this->get_key($name);

            if (isset($newValue)) {
                $update = update_option($key, $newValue);
                $opt = get_option($key);
            }

            $opt = get_option($key);

            if (FALSE === $opt && array_key_exists($name, $this->SETTINGS) && 'bool' !== $this->SETTINGS[$name]) {
                return $this->option($name, $this->SETTINGS_DEFAULTS[$name]);
            }

            if('bool' === $this->SETTINGS[$name]){
                return ($opt && strtolower($opt) !== 'false');
            }

            return $opt;
        }
    }
}
?>
