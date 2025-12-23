<?php

namespace ModularityStepByStep;

use ModularityStepByStep\Helper\CacheBust;

/**
 * Class App
 * 
 * Main application bootstrap class.
 * Initialize your plugin components here.
 * 
 * @package ModularityStepByStep
 */
class App
{
    public function __construct()
    {
        // Register module with Modularity
        add_action('init', [$this, 'registerModule']);

        // Enqueue styles
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);

        // Validate that only one step can be set to "open_by_default"
        add_action('acf/validate_save_post', [$this, 'validateOpenByDefault']);
    }

    /**
     * Enqueue styles
     * 
     * @return void
     */
    public function enqueueStyles(): void
    {
        $styleFile = CacheBust::name('css/modularity-step-by-step.css');

        if ($styleFile) {
            wp_enqueue_style(
                'modularity-step-by-step',
                MODULARITYSTEPBYSTEP_URL . '/assets/dist/' . $styleFile,
                [],
                null
            );
        }
    }

    /**
     * Register the module with Modularity
     * 
     * @return void
     */
    public function registerModule(): void
    {
        if (function_exists('modularity_register_module')) {
            modularity_register_module(
                MODULARITYSTEPBYSTEP_MODULE_PATH,
                'StepByStep',
            );
        }
    }

    /**
     * Validate that only one step can be set to "open_by_default"
     * 
     * @return void
     */
    public function validateOpenByDefault(): void
    {
        // ACF blocks store data in $_POST['acf-block_{block-id}']
        // Regular post type modules store data in $_POST['acf']
        // We need to check both locations
        
        $steps = null;
        $repeaterKey = 'field_step-by-step_steps';
        $open_by_default_key = 'field_6949387375053';
        $blockKey = null;
        
        // First, check for ACF block data (keys starting with 'acf-block_')
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'acf-block_') === 0 && is_array($value)) {
                // Check if this block contains our repeater field
                if (isset($value[$repeaterKey]) && is_array($value[$repeaterKey])) {
                    $steps = $value[$repeaterKey];
                    $blockKey = $key; // Store the block key for error attachment
                    break;
                }
            }
        }
        
        // If not found in blocks, check regular ACF post data
        if (!$steps && isset($_POST['acf']) && is_array($_POST['acf'])) {
            if (isset($_POST['acf'][$repeaterKey]) && is_array($_POST['acf'][$repeaterKey])) {
                $steps = $_POST['acf'][$repeaterKey];
            } elseif (isset($_POST['acf']['timeline_steps']) && is_array($_POST['acf']['timeline_steps'])) {
                $steps = $_POST['acf']['timeline_steps'];
            }
        }

        // If our field doesn't exist, this isn't our module - skip validation
        if (!$steps || !is_array($steps)) {
            return;
        }
        
        // Count how many steps have open_by_default set to true
        // Block repeater rows are keyed as 'row-0', 'row-1', etc.
        $open_count = 0;
        
        foreach ($steps as $row_index => $row_data) {
            if (!is_array($row_data)) {
                continue;
            }
            
            // Check if this row has open_by_default set to true
            // For true_false fields, value can be '1', 1, or true
            if (isset($row_data[$open_by_default_key])) {
                $value = $row_data[$open_by_default_key];
                
                if ($value == '1' || $value === 1 || $value === true || $value === 'true') {
                    $open_count++;
                }
            }
        }

        // If more than one step has open_by_default = true, show error
        if ($open_count > 1) {
            $error = __('Only one step can be set to "Open by Default". Please uncheck the other step first.', 'modularity-step-by-step');
            
            // For blocks, try attaching error to the block field or use general error
            if ($blockKey) {
                // Try attaching to the block field itself
                acf_add_validation_error($blockKey . '[' . $repeaterKey . ']', $error);
                // Also add general error to ensure it shows
                acf_add_validation_error('', $error);
            } else {
                // For regular post type modules, use the standard format
                acf_add_validation_error('acf[' . $repeaterKey . ']', $error);
            }
        }
    }
}
