<?php

declare(strict_types=1);

namespace ModularityStepByStep\Module;

/**
 * Class StepByStep
 * @package ModularityStepByStep\Module
 */
class StepByStep extends \Modularity\Module
{
    public $slug = 'step-by-step';
    public $supports = [];

    public $isBlockCompatible = true;

    public function init(): void
    {
        $this->nameSingular = __('StepByStep', 'modularity-step-by-step');
        $this->namePlural = __('StepByStep', 'modularity-step-by-step');
        $this->description = __('A step-by-step module.', 'modularity-step-by-step');
    }

    /**
     * Get ACF fields for block compatibility
     * Required when isBlockCompatible = true
     * 
     * @return array
     */
    public function getFields(): array
    {
        return get_fields($this->ID) ?: [];
    }

    /**
     * Data array
     * @return array $data
     */
    public function data(): array
    {
        $data = array();

        // Get timeline steps repeater field
        // Field name matches what we defined in ACF: 'timeline_steps'
        $steps = get_field('timeline_steps', $this->ID);
        
        // Format steps for Accordion component (matches accordion.json structure)
        $data['list'] = array();
        if (!empty($steps) && is_array($steps)) {
            foreach ($steps as $index => $step) {
                $stepTitle = isset($step['step_title']) ? $step['step_title'] : '';
                $stepContent = isset($step['step_content']) ? $step['step_content'] : '';
                $openByDefault = isset($step['open_by_default']) ? (bool) $step['open_by_default'] : false;
                
                // Format as Accordion component expects
                $data['list'][] = array(
                    'heading' => $stepTitle,
                    'content' => $stepContent,
                    'open_by_default' => $openByDefault,
                    'index' => $index,
                    'step_number' => $index + 1,
                );
            }
        }

        // Accordion component configuration – unique ID per instance
        // Modularity: $this->ID is the post ID. Gutenberg: use Modularity/Block/Data filter for block id.
        $data['id'] = 'mod-step-by-step-' . ($this->ID ?: wp_unique_id('mod-'));
        $data['componentElement'] = 'div';
        $data['sectionElement'] = 'div';
        $data['sectionHeadingElement'] = 'button';
        $data['sectionContentElement'] = 'div';
        $data['beforeHeading'] = '';
        $data['afterHeading'] = '';
        $data['beforeContent'] = '<p>';
        $data['afterContent'] = '</p>';
        $data['baseClass'] = 'c-accordion';
        
        // Add timeline-specific classes
        $data['class'] = 'c-accordion c-accordion--timeline c-step-by-step';
        $data['attribute'] = 'data-module-id="' . esc_attr($data['id']) . '"';
        
        // Add helper data
        $data['total_steps'] = count($data['list']);
        $data['has_steps'] = !empty($data['list']);

        return $data;
    }

    /**
     * Blade Template
     * @return string
     */
    public function template(): string
    {
        return 'step-by-step.blade.php';
    }

    /**
     * Style - Register & adding css
     * @return void
     */
    public function style(): void
    {
        $this->wpEnqueue?->add('css/modularity-step-by-step.css', [], '1.0.0');
    }

    /**
     * Script - Register & adding scripts
     * @return void
     */
    public function script(): void
    {
        $this->wpEnqueue?->add('js/modularity-step-by-step.js', [], '1.0.0');
    }

    /**
     * Admin Enqueue - Scripts for module edit page
     * @return void
     */
    public function adminEnqueue(): void
    {
        // Get built admin script from manifest
        $manifestPath = MODULARITYSTEPBYSTEP_PATH . 'assets/dist/manifest.json';
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $adminScript = $manifest['js/modularity-step-by-step-admin']['file'] ?? null;
            
            if ($adminScript) {
                wp_enqueue_script(
                    'modularity-step-by-step-admin',
                    MODULARITYSTEPBYSTEP_URL . '/assets/dist/' . $adminScript,
                    ['acf-input', 'jquery'],
                    '1.0.0',
                    true
                );
            }
        }
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}

