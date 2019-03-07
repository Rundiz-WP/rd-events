<?php
/**
 * The main application file for this plugin.
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace RdEvents\App;

if (!class_exists('\\RdEvents\\App\\App')) {
    /**
     * The main application class for this plugin.<br>
     * This class is the only main class that were called from main plugin file and it will be load any hook actions/filters to work inside the run() method.
     */
    class App
    {


        use \RdEvents\App\AppTrait;


        /**
         * Check system requirement.<br>
         * Example: WordPress version, PHP version.
         * 
         * @throws Exception Throw exception on failed validation.
         */
        private function checkRequirement()
        {
            $wordpress_required_version = '4.6';
            $php_required_version = '5.5';
            $php_version = (defined('PHP_VERSION') ? PHP_VERSION : (function_exists('phpversion') ? phpversion() : '4'));

            if (version_compare(get_bloginfo('version'), $wordpress_required_version, '<')) {
                /* translators: %1$s: Current WordPress version, %2$s: Required WordPress version. */
                $error_message = sprintf(__('Your WordPress version does not meet the requirement. (%1$s < %2$s).', 'rd-events'), get_bloginfo('version'), $wordpress_required_version);
                throw new \Exception($error_message);
            }

            if (version_compare($php_version, $php_required_version, '<')) {
                /* translators: %1$s: Current PHP version, %2$s: Required PHP version. */
                $error_message = sprintf(__('Your PHP version does not meet the requirement. (%1$s < %2$s).', 'rd-events'), $php_version, $php_required_version);
                throw new \Exception($error_message);
            }

            unset($error_message, $php_required_version, $php_version, $wordpress_required_version);
        }// checkRequirement


        /**
         * load text domain. (language files)
         */
        public function loadLanguage()
        {
            load_plugin_textdomain('rd-events', false, dirname(plugin_basename(RDEVENTS_FILE)) . '/App/languages/');
        }// loadLanguage


        /**
         * Run the main application class (plugin).
         */
        public function run()
        {
            // Check system requirement.
            $this->checkRequirement();

            add_action('plugins_loaded', function() {
                // @link https://codex.wordpress.org/Function_Reference/load_plugin_textdomain Reference.
                // load language of this plugin.
                $this->loadLanguage();
            });

            // Initialize the loader class.
            $this->Loader = new \RdEvents\App\Libraries\Loader();
            $this->Loader->autoRegisterControllers();

            // The rest of controllers that is not able to register via loader's auto register.
            // They must be manually write it down here, below this line.
            // For example:
            // $SomeController = new \RdEvents\App\Controllers\SomeController();
            // $SomeController->runItHere();
            // unset($SomeController);// for clean up memory.
            // ------------------------------------------------------------------------------------

            // Auto register all widgets available here.
            $AutoWidgets = new Widgets\AutoRegisterWidgets();
            $AutoWidgets->registerAll();
            unset($AutoWidgets);
        }// run


    }
}