<?php
/**
 * cpanel - /usr/local/cpanel/php/cpanel.php      Copyright(c) 2021 cPanel, L.L.C.
 *                                                          All rights reserved.
 * copyright@cpanel.net                                        http://cpanel.net
 */
if (version_compare(PHP_VERSION, '5.2.0', '<')) {
    trigger_error(
        "cPanel's Live PHP class must be executed with PHP >= 5.2",
        E_USER_ERROR
    );
    exit;
}
/** cPanel LiveAPI PHP Class
 *
 * This class allows for cPanel frontend pages to be developed in PHP using an
 * object for accessing the APIs.
 * For the full documentation, see https://go.cpanel.net/livephp.
 *
 * You are free to include this module in your program as long as it is for use
 * with cPanel. This module is only licensed for use with the version of cPanel
 * it is distributed with.
 *
 * The backend APIs are subject to change.  If you ignore this message you will
 * find that this module will not work in future versions.  This class will be
 * updated if the backend APIs change.  We will make all efforts to provide
 * backwards compatibility, but if you do use this class with any version
 * of cPanel other than the one it is distributed with the results could be
 * disasterous.
 *
 * FOR THE AVOIDANCE OF DOUBT: MAKE SURE YOU ONLY USE THIS MODULE WITH THE
 * VERSION OF CPANEL THAT IT CAME WITH
 *
 * For debugging purposes you can set the following two constants to enable
 * debug mode:
 *   - LIVEPHP_DEBUG_LEVEL - 0 or 1 - enable or disable debugging
 *   - LIVEPHP_DEBUG_LOG - path - The absolute path and filename for logging.
 *
 * This class also provides a set_debug() method for enabling/disabling debug
 * mode.
 *
 * Changes:
 *  Version 2.1
 *   - Corrected various documentation
 *   - Altered code and documentation for better adherence to PEAR PHP coding
 *     standards without breaking BC
 *   - Fixed bug (constructor should explicitly return "$this")
 *   - Altered methods cpanelif() and cpanelfeature() to enforce a boolean
 *     return.
 *   - Implemented the use pre-defined SPL Exception classes instead of generic
 *     Exception base class
 *   - Use Exceptions wherever possible instead of simple log via error_log()
 *   - Use trigger_error() instead of error_log()
 *   - Use error_log() only when logging cPanel related information (i.e., what
 *     normally might be E_NOTICE or E_DEPRECATED, but specific only to cPanel
 *     technicians and developers)
 *   - Added 'deprecated' PHP DocBlock to cpanellangprint(). (future versions
 *     will likely throw E_USER_DEPRECATED; instead use
 *     API1's Locale::maketext() (## no extract maketext)
 *
 *  Version 2.0
 *   - Changed the backend serialization format to JSON
 *   - Added debug logger
 *   - Added Exceptions
 *
 * @category  Cpanel
 * @package   CPANEL
 * @author    cPanel, Inc. <copyright@cpanel.net>
 * @copyright 1997-2020 cPanel, L.L.C.
 * @license   http://cpanel.net
 * @version   Release: 2.1
 * @link      https://go.cpanel.net/livephp
 */
class CPANEL
{
    /**
     * Socket resource for communicating with cPanel LiveAPI parser
     * @var resource Local socket
     */
    private $_cpanelfh;
    /**
     * State tracker for socket resource
     * @var boolean State of private resource
     */
    public $connected = 0;
    /**
     * Absolute path and filename of debug log
     *
     * NOTE: If LIVEPHP_DEBUG_LOG environment variable is not set, this variable
     * will be populated with a random log file (if debugging is enabled):
     * ~/.cpanel/livephp.log.$randomstring.
     *
     * @var string Log file
     */
    private $_debug_log;
    /**
     * File handle for debug log
     * @var resource File handle for debug log
     */
    private $_debug_fh;
    /**
     * Debug logging level
     *
     * Value values are:
     *  0 - Debugging disabled
     *  1 - Log all socket communication to debug log file
     *
     * @var integer Debug logging level
     */
    private $_debug_level = 0;
    /**
     * Storage location for last server response
     * @var array Array data structure of the last server response
     */
    private $_result;

    /**
    * Storage location for a stringified DOM as used by the header() and footer() methods
    * note: modern themes only
     */
    private $_dom = 0;
    /**
     * Instantiate the LiveAPI PHP Object
     *
     * This will create the "CPANEL" object; open the communication socket.
     *
     * @return CPANEL A LiveAPI object
     * @throws RuntimeException if CPANEL_PHPCONNECT_SOCKET environment variable
     * is not set
     * @throws RuntimeException if file socket cannot be established
     * @throws RuntimeException if stream blocking cannot be set for file socket
     */
    public function __construct()
    {
        $this->connected = 1;
        // Attempt to set debugging based on defined PHP constants
        if (defined('LIVEPHP_DEBUG_LOG')) {
            $this->_debug_log = LIVEPHP_DEBUG_LOG;
        }
        if (defined('LIVEPHP_DEBUG_LEVEL')) {
            $this->set_debug(LIVEPHP_DEBUG_LEVEL);
        }
        // prepare socket to communicate with cPanel API parser
        $socketfile = getenv('CPANEL_PHPCONNECT_SOCKET');
        if (!$socketfile) {
            throw new RuntimeException(
                'There was a problem fetching the env variable'
                . 'containing the path to the socket'
            );
        }
        $this->_cpanelfh = fsockopen("unix://" . $socketfile);
        if (!$this->_cpanelfh) {
            $this->connected = 0;
            throw new RuntimeException(
                'There was a problem connecting back to the cPanel engine.'
                .' Make sure your script ends with .live.php or .livephp'
            );
        }
        stream_set_blocking($this->_cpanelfh, 1) || $this->connected = 0;
        if (!$this->connected) {
            throw new RuntimeException(
                'There was a problem connecting back to the cPanel engine.'
                .' Make sure your script ends with .live.php or .livephp'
            );
        }
        // enable enbedded json in the protocol
        $this->exec('<cpaneljson enable="1">');
        return $this;
    }
    /**
     * Enable debugging mode
     *
     * Passing this a non-zero value will enable socket logging.
     *
     * NOTE: This should only be used when attempting to debug the transactions
     * that happen over the socket. ALL data will be log!
     *
     * The valid logging level are as follows:
     *   0 - Disable logging (default)
     *   1 - Write socket transactions to the log.
     *
     * @param int $debug_level The debug level
     *
     * @return void
     * @throws UnexpectedValueException if $debug_level is not numeric
     */
    public function set_debug($debug_level)
    {
        if (is_numeric($debug_level)) {
            // Open the debug log if it isn't already
            if ($debug_level > 0 && !is_resource($this->_debug_fh)) {
                // Set the debug log
                if (!isset($this->_debug_log)) {
                    $user_pwnam = posix_getpwuid(posix_getuid());
                    $this->_debug_log = $user_pwnam['dir']
                    . '/.cpanel/livephp.log.' . mt_rand(10000000, 99999999);
                }
                $this->_debug_fh = fopen($this->_debug_log, 'a');
            } elseif (is_resource($this->_debug_fh) && $debug_level == 0) {
                // Close debug_log if debug logging is being disabled
                fclose($this->_debug_fh);
            }
            $this->_debug_level = $debug_level;
        } else {
            $this->set_debug(0);
            throw new UnexpectedValueException(
                'CPANEL::set_debug given non-integer value.'
            );
        }
    }
    /**
     * Write to the debug log
     *
     * Write a message to the debug log
     *
     * @param int    $level   The desired logging level for $log_msg to appear.
     * @param string $log_msg The message you wish to have logged
     *
     * @return void
     * @throws RuntimeException if log filehandle does not exist
     */
    public function debug_log($level, $log_msg)
    {
        if ($level > 0 && $level <= $this->_debug_level) {
            if (is_resource($this->_debug_fh)) {
                fwrite($this->_debug_fh, date("[d-M-Y H:i:s] ") . $log_msg . "\n");
            } else {
                throw new RuntimeException(
                    'Attempted to execute debugging statement on closed filehandle'
                );
            }
        }
    }

    /**
     * Parse and log a JSON formatted string.
     *
     * @param string $str JSON formatted string to decode and log
     *
     * @return void
     */
    public function debug_log_json($str)
    {
        $parsed = json_decode($str, true);
        if ($parsed !== null) {
            ob_start();
            var_dump($parsed);
            $log_msg = ob_get_clean();
        } elseif (function_exists('json_last_error') && json_last_error()) {
            // json_last_error is only PHP>=5.3
            switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                $log_msg = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $log_msg = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $log_msg = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_NONE:
                //do nothing;
                break;
            }
        } else {
            $log_msg = "Error decoding JSON string";
        }
        $this->debug_log(1, 'JSON_decode: ' . $log_msg);
    }
    /**
     * Get the filename of the debug log currently in use.
     *
     * @return string Current log file
     */
    public function get_debug_log()
    {
        return $this->_debug_log;
    }
    /**
     * Return the currently set debug level.
     *
     * @return int Current debug level
     */
    public function get_debug_level()
    {
        return $this->_debug_level;
    }
    /**
     * Return the value of a cPvar
     *
     * @param string $var The cPvar to fetch (e.g. $CPDATA{'DNS'} )
     *
     * @return array Response containing cPvar value
     */
    public function fetch($var)
    {
        if (!$this->connected) {
            return;
        }
        return $this->exec('<cpanel print="' . $var . '">');
    }
    /**
     * Execute an API1 call
     *
     * @param string $module An API1 module name.
     * @param string $func   An API1 function.
     * @param array  $args   An ordinal array containing arguments for the API1 function
     *
     * @return array Returned response from the API1 function
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function api1($module, $func, $args = array())
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        return $this->api('exec', "1", $module, $func, $args);
    }
    /**
     * Execute an API2 call
     *
     * @param string $module An API2 module name.
     * @param string $func   An API2 function.
     * @param array  $args   An associative array containing arguments for the API2 function
     *
     * @return array Returned response from the API2 function
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function api2($module, $func, $args = array())
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        return $this->api('exec', "2", $module, $func, $args);
    }
     /**
     * Execute an API3 call, an alias for a UAPI call
     *
     * @param string $module A UAPI module name.
     * @param string $func   A UAPI function.
     * @param array  $args   An associative array containing arguments for the UAPIfunction
     *
     * @return array Returned response from the UAPI function
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function api3($module, $func, $args = array())
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        return $this->api('exec', "3", $module, $func, $args);
    }
    /**
     * Execute an UAPI call
     *
     * @param string $module A UAPI module name.
     * @param string $func   A UAPI function.
     * @param array  $args   An associative array containing arguments for the UAPIfunction
     *
     * @return array Returned response from the UAPI function
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function uapi($module, $func, $args = array())
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        return $this->api('exec', "uapi", $module, $func, $args);
    }
    /**
     * Evaluate a cpanelif statement
     *
     * This method will return a boolean value based on the evaluation of the
     * code expression
     *
     * @param string $code A cPvar or logical test condition
     *
     * @link https://go.cpanel.net/PluginVars ExpVar Reference Chart
     * @return boolean Whether the $code expression evaluates as true or false
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function cpanelif($code)
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        $value = (simple_result($this->api('if', '1', 'if', 'if', $code)))? 1 : 0;
        return $value;
    }
    /**
     * Determine if the current cPanel account has access to a specific feature
     *
     * @param string $feature A feature name
     *
     * @return boolean Whether the current cPanel account has access to queried
     * feature.
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function cpanelfeature($feature)
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        $value = (simple_result($this->api('feature', '1', 'feature', 'feature', $feature)))? 1 : 0;
        return $value;
    }
    /**
     * Return the value of a cPvar
     *
     * This method will return the value of a cPvar. This differs from fetch()
     * which returns the complete response as an array.  The method will only
     * return the cPvar value as a string.
     *
     * @param string $var The cPvar to retrieve (e.g. $CPDATA{'DNS'} )
     *
     * @return string The value of the queried cPvar
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function cpanelprint($var)
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        return simple_result($this->api1('print', '', $var));
    }
    /**
     * Process a language key for the cPanel account's current language
     *
     * @param string $key A language key
     *
     * @deprecated The cpanellongprint tag is no longer supported. Use API1
     * Locale::maketext ## no extract maketext
     * @see https://go.cpanel.net/maketext ## no extract maketext
     *
     * @return string Translated version of the requested language key
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function cpanellangprint($key)
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        return simple_result($this->api1('langprint', '', $key));
    }
    /**
     * Execute a cpanel tag
     *
     * In most cases there is no need to call this method directly.  Instead one
     * should use the api1(), api2() or cpanel*() methods (which all call this
     * method internally).
     *
     * @param string  $code        A cPanel tag to execute.
     * @param boolean $skip_return (optional) If set to true, this function will
     * not return anything.
     *
     * @return array Returned response in an array data structure
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function exec($code, $skip_return = 0)
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        // SEND CODE
        $buffer = '';
        $result = '';
        if ($this->_debug_level) {
            $this->debug_log(1, '(exec) SEND:' . $code);
        }
        fwrite($this->_cpanelfh, strlen($code) . "\n" . $code);
        //RECV CODE
        while ($buffer = fgets($this->_cpanelfh)) {
            $result = $result . $buffer;
            if (strstr($buffer, '</cpanelresult>') !== false) {
                break;
            }
        }
        if ($this->_debug_level) {
            $this->debug_log(1, '(exec) RECV:' . $result);
        }
        if ($skip_return) {
            $this->_result = null;
            return;
        }
        // Parse out return code, build livePHP result
        $json_start_pos = strpos($result, "<cpanelresult>{");
        if ($json_start_pos !== false) {
            $json_start_pos+= 14;
            if ($this->_debug_level) {
                $this->debug_log_json(
                    substr(
                        trim($result),
                        $json_start_pos,
                        strpos(
                            $result,
                            "</cpanelresult>"
                        ) - $json_start_pos
                    )
                );
            }
            $parsed = json_decode(
                substr(
                    trim($result),
                    $json_start_pos,
                    strpos(
                        $result,
                        "</cpanelresult>"
                    ) - $json_start_pos
                ),
                true
            );
            if (strpos($result, '<cpanelresult>{"cpanelresult"') === false
                && $parsed !== null
            ) {
                /**
                 * needed for compat: API2 tags will end up with both due to
                 * the internals
                 */
                $this->_result = array('cpanelresult' => $parsed);
            } else {
                $this->_result = $parsed;
            }
        } elseif (strpos($result, "<cpanelresult></cpanelresult>") !== false) {
            /* This is a hybird api1/api2/api3 response to ensure that
                the developer using api gets the error field in the position
                they are looking for */
            $this->_result = array('cpanelresult' => array('error' => 'Error cannot be propagated to liveapi, please check the cPanel error_log.', 'result' => array('errors' => array('Error cannot be propagated to liveapi, please check the cPanel error_log.'))));
        } elseif (strpos($result, "<cpanelresult>") !== false) {
            /**
             * This logic flow is provide for BC in the unlikely event that the
             * cPanel engine doesn't not handle JSON.
             * - log this directly to the PHP error log in hopes that it gets
             *   reported
             */

            if ($this->_debug_level) {
                $this->debug_log(1, 'XML_unserialize:' . $result);
            }
            error_log(
                'cPanel LiveAPI parser returned XML, which is deprecated. '
                .'Please file a bug report at https://tickets.cpanel.net/submit/'
            );
            include_once '/usr/local/cpanel/php/xml.php';
            # XML_unserialize takes a reference, and PHP doesn't like it if we
            # pass a non-variable by reference.
            $temp = trim($result);
            $this->_result = XML_unserialize($temp);
        }
        return $this->_result;
    }
    /**
     * Execute an API call
     *
     * In most cases there is no need to call this method directly.  Instead one
     * should use the api1(), api2() or cpanel*() methods (which all call this
     * method, or exec(), internally).
     *
     * @param string $reqtype The type of request used by the cPanel API parser;
     *  valid values are 'exec', 'feature' or 'if'
     * @param int    $version The version of the API; valid values are either
     *  '1' or '2'
     * @param string $module  An API module name
     * @param string $func    An API function name
     * @param mixed  $args    Associate array for API2, ordered array for API1,
     *  string for non exec $reqtypes
     *
     * @see api1()
     * @see api2()
     * @return array Returned response in an array data structure
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function api($reqtype, $version, $module, $func, $args = array())
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        $input = array(
                "module" => $module,
                "reqtype" => $reqtype,
                "func" => $func,
                "apiversion" => $version
                );

        // Args may actually be a string instead of an array.
        // yay for php 4.x-isms which would automagically turn 'string' into array('string') when accessed as array
        // As such, just check that it isn't empty instead of doing count()
        if (!empty($args)) {
            $input['args'] = $args;
        }
        /**
         *  cPanel engine can process the JSON much much faster than XML
         */
        if (function_exists('json_encode')) {
            $code = "<cpanelaction>\n" . json_encode($input) . "\n</cpanelaction>";
        } else {
            /**
            * This logic flow is provide for BC in the unlikely event that the
            * cPanel engine doesn't not handle JSON.
            * - log this directly to the PHP error log in hopes that it gets
            *   reported
            */
            error_log(
                'cPanel LiveAPI parser returned XML, which is deprecated. '
                .'Please file a bug report at https://tickets.cpanel.net/submit/'
            );
            include_once '/usr/local/cpanel/php/xml.php';
            $temp = array("cpanelaction" => array($input));
            $code = XML_serialize($temp);
        }
        return $this->exec($code);
    }
    /**
    * Get the data result node of the last call
    *
    * This method will return the ['cpanelresult']['data']['result'] node from
    * the last call that was made.
    *
    * @return mixed A string if the last call was API1, an array or array of
    * associative arrays if the last call was API2
    * @throws UnexpectedValueException if no data is available from a previous
    * call
    * @throws OutOfBoundsException if previous data response does not contain
    * proper hierarchy
    *
    */
    public function get_result()
    {
        if ( !$this->_result ) {
            throw new UnexpectedValueException('No previous result exists');
        }
        if (!is_array($this->_result)
            || !is_array($this->_result['cpanelresult'])
            || !is_array($this->_result['cpanelresult']['data'])
        ) {
            throw new OutOfBoundsException(
                'cpanelresult->data associative array key does not exist or '
                .'previous call did not return array'
            );
        }
        if (array_key_exists('result', $this->_result['cpanelresult']['data'])) {
            return $this->_result['cpanelresult']['data']['result'];
        } else {
            return $this->_result['cpanelresult']['data'];
        }
    }

    /**
    * Get the string containing all of the output up until the header
    *
    * This method will return everything up until just past the body-content div
    * this intended as a method of writing a liveAPI application that matches cpanel's
    * presentation.
    *
    * @return string A string containing all output before the body-content div
    * @throws UnexpectedValueException if no header value is detected
    */
    public function header( $title = '', $app_key = '' ) {
        if ( !$this->_dom ) {
            $result = $this->uapi( 'Chrome', 'get_dom', array( 'page_title' => $title, 'app_key' => $app_key ) );
            $this->_dom = $result['cpanelresult']['result']['data'];
        }
        if ( !array_key_exists( 'header', $this->_dom ) ) {
            throw new UnexpectedValueException('No header in DOM response!');
        }
        return $this->_dom['header'];
    }

    /**
    * Get the string containing all of the output after the body
    *
    * This method will return everything past the body-content div
    * this intended as a method of writing a liveAPI application that matches cpanel's
    * presentation.
    *
    * @return string A string containing all output after the body-content div
    * @throws UnexpectedValueException if no footer value is detected
    */
    public function footer( $title = '' ) {
        if ( !$this->_dom ) {
            $result = $this->uapi( 'Chrome', 'get_dom', array( 'title' => $title ) );
            $this->_dom = $result['cpanelresult']['result']['data'];
        }
        if ( !array_key_exists( 'footer', $this->_dom ) ) {
            throw new UnexpectedValueException('No footer in DOM response!');
        }
        return $this->_dom['footer'];
    }



    /**
     * Close the connection and destroy the object
     *
     * Calling this method should not be required since all logic actually
     * resides in the class deconstructor. This is provided for BC.
     *
     * @return void
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function end()
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        $this->__destruct();
    }
    /**
     * Deconstructor is responsible for closing communication with the cPanel
     * engine
     *
     * @return void
     * @throws RuntimeException if LiveAPI socket is not available
     */
    public function __destruct()
    {
        if (!$this->connected) {
            throw new RuntimeException(
                'The LiveAPI PHP socket has closed, unable to continue.'
            );
        }
        if (is_resource($this->_cpanelfh)) {
            $this->exec('<cpanelxml shutdown="1" />', 1);
            while (!feof($this->_cpanelfh)) {
                fgets($this->_cpanelfh);
            }
            fclose($this->_cpanelfh);
            if ($this->_debug_level) {
                $this->debug_log(1, 'MAX_MEM: ' . memory_get_peak_usage());
                if (is_resource($this->_debug_fh)) {
                    fclose($this->_debug_fh);
                }
            }
        }
    }
}
/**
 * Retrieve the contents of the 'result' node within a return response data
 * structure
 *
 * This function is only valid for responses which have only a single response
 * in their data structure, i.e., special cpanel tags.  In most cases, one
 * should consider using CPANEL::get_result() immediately following an API or
 * cPanel tag query.  This function may be deprecated in future versions of the
 * LiveAPI PHP client code.
 *
 * @param array $result_data Returned response in array format
 *
 * @return string Contents of the "result" node in the provided data structure
 */
function simple_result($result_data)
{
    return $result_data['cpanelresult']['data']['result'];
}
?>
