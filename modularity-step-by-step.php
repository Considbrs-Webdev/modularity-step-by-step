<?php

/**
 * Plugin Name:       Modularity StepByStep
 * Plugin URI:        https://github.com/helsingborg-stad/modularity-step-by-step
 * Description:       A step-by-step for creating Modularity modules.
 * Version: 1.0.0
 * Author:            Starter
 * Author URI:        https://github.com/helsingborg-stad
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       modularity-step-by-step
 * Domain Path:       /languages
 */

// Protect against direct file access
if (! defined('WPINC')) {
    die;
}

define('MODULARITYSTEPBYSTEP_PATH', plugin_dir_path(__FILE__));
define('MODULARITYSTEPBYSTEP_URL', plugins_url('', __FILE__));
define('MODULARITYSTEPBYSTEP_MODULE_VIEW_PATH', plugin_dir_path(__FILE__) . 'source/php/Module/views');
define('MODULARITYSTEPBYSTEP_MODULE_PATH', MODULARITYSTEPBYSTEP_PATH . 'source/php/Module/');

// Load text domain
add_action('init', function () {
    load_plugin_textdomain('modularity-step-by-step', false, plugin_basename(dirname(__FILE__)) . '/languages');
});

// Autoload from plugin
if (file_exists(MODULARITYSTEPBYSTEP_PATH . 'vendor/autoload.php')) {
    require_once MODULARITYSTEPBYSTEP_PATH . 'vendor/autoload.php';
}

// ACF auto import and export
add_action('acf/init', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('modularity-step-by-step');
    $acfExportManager->setExportFolder(MODULARITYSTEPBYSTEP_PATH . 'source/php/AcfFields/');
    $acfExportManager->autoExport(array(
        'step-by-step-module' => 'group_step-by-step_module',
    ));
    $acfExportManager->import();
});

// Modularity 3.0 ready - ViewPath for Component library
add_filter('/Modularity/externalViewPath', function ($arr) {
    $arr['mod-step-by-step'] = MODULARITYSTEPBYSTEP_MODULE_VIEW_PATH;
    return $arr;
}, 10, 3);

// Start application
new ModularityStepByStep\App();

