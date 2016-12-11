<?php
if (defined('WPT_UTILS')) {
    return;
}

define('WPT_UTILS', true);

/**
 * utils.php
 *
 * A collection of .php utility functions for common use
 *
 * @package ToolsetCommon
 *
 * @since unknown
 */
if (!class_exists('Toolset_Utils')) {

    /**
     * ToolsetUtils
     *
     * A collection of static methods to be used across Toolset plugins
     *
     * @since 1.7
     */
    class Toolset_Utils {

        /**
         * help_box
         *
         * Creates the HTML version for the wpvToolsetHelp() javascript function
         *
         * @param data array containing the attributes
         * 		text					=> The content to show inside the help box.
         * 		tutorial-button-text	=> Optional button anchor text.
         * 		tutorial-button-url		=> Optional button url.
         * 		link-text				=> Optional link anchor text.
         * 		link-url				=> Optional link url.
         * 		footer					=> 'true'|'false' Whether the help box should have a footer with a Close button (managed) and a "dismiss forever" button (not managed). Defaults to 'false'.
         * 		classname				=> Additional classnames for the help box in a space-separated list.
         * 		close					=> 'true'|'false' Whether the help box should have a close button. Defaults to 'true'.
         * 		hidden					=> 'true'|'false' Whether the help box should be hidden by default. Defaults to 'false'.
         *
         * @since 1.7
         */
        public static function help_box($data = array()) {
            if (is_array($data) && !empty($data)) {
                $data_attr = '';
                foreach ($data as $key => $value) {
                    if ('text' != $key) {
                        $data_attr .= ' data-' . $key . '="' . esc_attr($value) . '"';
                    }
                }
                ?>
                <div class="js-show-toolset-message"<?php echo $data_attr; ?>>
                    <?php
                    if (isset($data['text'])) {
                        echo $data['text'];
                    }
                    ?>
                </div>
                <?php
            }
        }

        public static function get_image_sizes($size = '') {

            global $_wp_additional_image_sizes;

            $sizes = array();
            $get_intermediate_image_sizes = get_intermediate_image_sizes();

            // Create the full array with sizes and crop info
            foreach ($get_intermediate_image_sizes as $_size) {

                if (in_array($_size, array('thumbnail', 'medium', 'large'))) {

                    $sizes[$_size]['width'] = get_option($_size . '_size_w');
                    $sizes[$_size]['height'] = get_option($_size . '_size_h');
                    $sizes[$_size]['crop'] = (bool) get_option($_size . '_crop');
                } elseif (isset($_wp_additional_image_sizes[$_size])) {

                    $sizes[$_size] = array(
                        'width' => $_wp_additional_image_sizes[$_size]['width'],
                        'height' => $_wp_additional_image_sizes[$_size]['height'],
                        'crop' => $_wp_additional_image_sizes[$_size]['crop']
                    );
                }
            }

            // Get only 1 size if found
            if ($size) {

                if (isset($sizes[$size])) {
                    return $sizes[$size];
                } else {
                    return false;
                }
            }

            return $sizes;
        }

	    /**
	     * Check for a custom field value's "emptiness".
	     *
	     * "0" is also a valid value that we need to take into account.
	     *
	     * @param $field_value
	     * @return bool
	     * @since 2.2.3
	     */
        public static function is_field_value_truly_empty( $field_value ) {
			$is_truly_empty = ( empty( $field_value ) && ! is_numeric( $field_value ) );
	        return $is_truly_empty;
        }

    }

}

if (!function_exists('wp_json_encode')) {

    function wp_json_encode($data, $options = 0, $depth = 512) {
        /*
         * json_encode() has had extra params added over the years.
         * $options was added in 5.3, and $depth in 5.5.
         * We need to make sure we call it with the correct arguments.
         */
        if (version_compare(PHP_VERSION, '5.5', '>=')) {
            $args = array($data, $options, $depth);
        } elseif (version_compare(PHP_VERSION, '5.3', '>=')) {
            $args = array($data, $options);
        } else {
            $args = array($data);
        }

        $json = call_user_func_array('json_encode', $args);

        // If json_encode() was successful, no need to do more sanity checking.
        // ... unless we're in an old version of PHP, and json_encode() returned
        // a string containing 'null'. Then we need to do more sanity checking.
        if (false !== $json && ( version_compare(PHP_VERSION, '5.5', '>=') || false === strpos($json, 'null') )) {
            return $json;
        }

        try {
            $args[0] = _wp_json_sanity_check($data, $depth);
        } catch (Exception $e) {
            return false;
        }

        return call_user_func_array('json_encode', $args);
    }

    if (!function_exists('_wp_json_sanity_check')) {

        function _wp_json_sanity_check($data, $depth) {
            if ($depth < 0) {
                throw new Exception('Reached depth limit');
            }

            if (is_array($data)) {
                $output = array();
                foreach ($data as $id => $el) {
                    // Don't forget to sanitize the ID!
                    if (is_string($id)) {
                        $clean_id = _wp_json_convert_string($id);
                    } else {
                        $clean_id = $id;
                    }

                    // Check the element type, so that we're only recursing if we really have to.
                    if (is_array($el) || is_object($el)) {
                        $output[$clean_id] = _wp_json_sanity_check($el, $depth - 1);
                    } elseif (is_string($el)) {
                        $output[$clean_id] = _wp_json_convert_string($el);
                    } else {
                        $output[$clean_id] = $el;
                    }
                }
            } elseif (is_object($data)) {
                $output = new stdClass;
                foreach ($data as $id => $el) {
                    if (is_string($id)) {
                        $clean_id = _wp_json_convert_string($id);
                    } else {
                        $clean_id = $id;
                    }

                    if (is_array($el) || is_object($el)) {
                        $output->$clean_id = _wp_json_sanity_check($el, $depth - 1);
                    } elseif (is_string($el)) {
                        $output->$clean_id = _wp_json_convert_string($el);
                    } else {
                        $output->$clean_id = $el;
                    }
                }
            } elseif (is_string($data)) {
                return _wp_json_convert_string($data);
            } else {
                return $data;
            }

            return $output;
        }

    }

    if (!function_exists('_wp_json_convert_string')) {

        function _wp_json_convert_string($string) {
            static $use_mb = null;
            if (is_null($use_mb)) {
                $use_mb = function_exists('mb_convert_encoding');
            }

            if ($use_mb) {
                $encoding = mb_detect_encoding($string, mb_detect_order(), true);
                if ($encoding) {
                    return mb_convert_encoding($string, 'UTF-8', $encoding);
                } else {
                    return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
                }
            } else {
                return wp_check_invalid_utf8($string, true);
            }
        }

    }
}

if (!class_exists('Toolset_ArrayUtils')) {

    Class Toolset_ArrayUtils {

        private $value = null;
        private $property = null;

        function __construct($property = null, $value = null) {
            $this->value = $value;
            $this->property = $property;
        }

        function filter_array($element) {
            if (is_object($element)) {

                if (property_exists($element, $this->property) === false)
                    return null;

                return $element->{$this->property} === $this->value;
            } elseif (is_array($element)) {

                if (isset($element[$this->property]) === false)
                    return null;

                return $element[$this->property] === $this->value;
            } else {

                throw new Exception(sprintf("Element parameter should be an object or an array, %s given.", gettype($element)));
            }
        }

        public function remap_by_property($data) {
            return $data[$this->property];
        }

        function value_in_array($array) {
            return in_array($this->value, array_values($array));
        }

        function sort_string_ascendant($a, $b) {
            return strcmp($a[$this->property], $b[$this->property]);
        }

    }

}



if (!class_exists('Toolset_ErrorHandler')) {

    /**
     * ErrorHandler that can be used to catch internal PHP errors
     * and convert to an ErrorException instance.
     */
    abstract class Toolset_ErrorHandler {

        /**
         * Active stack
         *
         * @var array
         */
        protected static $stack = array();

        /**
         * Check if this error handler is active
         *
         * @return bool
         */
        public static function started() {
            return (bool) self::getNestedLevel();
        }

        /**
         * Get the current nested level
         *
         * @return int
         */
        public static function getNestedLevel() {
            return count(self::$stack);
        }

        /**
         * Starting the error handler
         *
         * @param int $errorLevel
         */
        public static function start($errorLevel = E_WARNING) {
            if (!self::$stack) {
                set_error_handler(array(get_called_class(), 'addError'), $errorLevel);
                register_shutdown_function(array(get_called_class(), 'handle_shutdown'), true);
            }

            self::$stack[] = null;
        }

        /**
         * Stopping the error handler
         *
         * @param  bool $throw Throw the ErrorException if any
         * @return null|ErrorException
         * @throws ErrorException If an error has been catched and $throw is true
         */
        public static function stop($throw = false) {
            $errorException = null;

            if (self::$stack) {
                $errorException = array_pop(self::$stack);

                if (!self::$stack) {
                    restore_error_handler();
                }

                if ($errorException && $throw) {
                    throw $errorException;
                }
            }

            return $errorException;
        }

        public static function handle_shutdown() {
            if (self::is_fatal()) {
                do_action('toolset-shutdown-hander');
            }
            exit;
        }

        public static function is_fatal() {
            $error = error_get_last();
            $ignore = E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_STRICT | E_DEPRECATED | E_USER_DEPRECATED;
            if (($error['type'] & $ignore) == 0) {
                return true;
            }
            return false;
        }

        /**
         * Stop all active handler
         *
         * @return void
         */
        public static function clean() {
            if (self::$stack) {
                restore_error_handler();
            }

            self::$stack = array();
        }

        /**
         * Add an error to the stack
         *
         * @param int    $errno
         * @param string $errstr
         * @param string $errfile
         * @param int    $errline
         * @return void
         */
        public static function addError($errno, $errstr = '', $errfile = '', $errline = 0) {
            if (count(self::$stack)) {
                $stack = & self::$stack[count(self::$stack) - 1];
            } else {
                $stack = null;
            }
            $stack = new ErrorException((string) $errstr, 0, (int) $errno, (string) $errfile, (int) $errline);
        }

    }

}

/**
 * PHP 5.2 support.
 *
 * get_called_class() is only in PHP >= 5.3, this is a workaround.
 * This function is needed by WPDDL_Theme_Integration_Abstract.
 */
if (!function_exists('get_called_class')) {

    function get_called_class() {
        $bt = debug_backtrace();
        $l = 0;
        $matches = null;
        do {
            $l++;
            if (isset($bt[$l]['file'])) {
                $lines = file($bt[$l]['file']);
                $callerLine = $lines[$bt[$l]['line'] - 1];
                preg_match('/([a-zA-Z0-9\_]+)::' . $bt[$l]['function'] . '/', $callerLine, $matches);
            }
        } while (isset($matches[1]) && $matches[1] === 'parent');

        return isset($matches[1]) ? $matches[1] : "";
    }

}


/**
 * Safely retrieve a key from $_GET variable.
 *
 * This is a wrapper for toolset_get_from_array(). See that for more information.
 *
 * @param string $key See toolset_getarr().
 * @param mixed $default See toolset_getarr().
 * @param null|array $valid See toolset_getarr().
 *
 * @return mixed See toolset_getarr().
 *
 * @since 1.8
 */
if (!function_exists('toolset_getget')) {

    function toolset_getget($key, $default = '', $valid = null) {
        return toolset_getarr($_GET, $key, $default, $valid);
    }

}



/**
 * Safely retrieve a key from given array (meant for $_POST, $_GET, etc).
 *
 * Checks if the key is set in the source array. If not, default value is returned. Optionally validates against array
 * of allowed values and returns default value if the validation fails.
 *
 * @param array $source The source array.
 * @param string $key The key to be retrieved from the source array.
 * @param mixed $default Default value to be returned if key is not set or the value is invalid. Optional.
 *     Default is empty string.
 * @param null|array $valid If an array is provided, the value will be validated against it's elements.
 *
 * @return mixed The value of the given key or $default.
 *
 * @since 1.8
 */
if (!function_exists('toolset_getarr')) {

    function toolset_getarr(&$source, $key, $default = '', $valid = null) {
        if (isset($source[$key])) {
            $val = $source[$key];
            if (is_array($valid) && !in_array($val, $valid)) {
                return $default;
            }

            return $val;
        } else {
            return $default;
        }
    }

}