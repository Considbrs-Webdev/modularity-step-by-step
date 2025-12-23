/**
 * Modularity Step By Step - Admin JavaScript
 * 
 * Dynamically updates the "Steps to Open by Default" checkbox choices
 * based on the timeline_steps repeater field.
 */

(function($) {
    'use strict';

    /**
     * Update checkbox choices based on repeater items
     */
    function updateDefaultOpenChoices() {
        // Get the repeater field
        const repeaterField = acf.getField('field_step-by-step_steps');
        if (!repeaterField) {
            return;
        }

        // Get the checkbox field
        const checkboxField = acf.getField('field_step-by-step_default_open');
        if (!checkboxField) {
            return;
        }

        // Get all repeater rows
        const rows = repeaterField.$el.find('.acf-row:not(.acf-clone)');
        const choices: Record<string, string> = {};

        // Build choices from repeater rows
        rows.each(function(index: number) {
            const $row = $(this);
            const titleField = $row.find('[data-name="step_title"] input');
            const stepTitle = titleField.length ? titleField.val() as string : '';
            
            // Use step title or fallback to "Step X"
            const label = stepTitle 
                ? `Step ${index + 1}: ${stepTitle}` 
                : `Step ${index + 1}`;
            
            choices[index.toString()] = label;
        });

        // Update checkbox field choices
        checkboxField.set('choices', choices);
        
        // Refresh the field to show updated choices
        checkboxField.render();
    }

    /**
     * Initialize when ACF is ready
     */
    acf.addAction('ready_field/type=repeater', function(field: any) {
        if (field.get('name') === 'timeline_steps') {
            // Update choices when repeater is ready
            updateDefaultOpenChoices();

            // Listen for repeater add/remove events
            field.on('append', updateDefaultOpenChoices);
            field.on('remove', updateDefaultOpenChoices);
        }
    });

    /**
     * Also listen for changes to step titles
     */
    acf.addAction('change_field/type=text', function(field: any) {
        if (field.get('name') === 'step_title') {
            // Small delay to ensure repeater is updated
            setTimeout(updateDefaultOpenChoices, 100);
        }
    });

    /**
     * Update on page load if repeater already has data
     */
    acf.addAction('ready', function() {
        setTimeout(updateDefaultOpenChoices, 500);
    });

})(jQuery);

