<?php
/**
 * {{name}} parent controller
 *{{array:authors}}
 * @link {{authors.url}} {{authors.name}}{{array:authors}}
 */
class {{class_name}}Controller extends AppController
{
    /**
     * Require admin to be login and setup the view
     */
    public function preAction()
    {
////    // This statement tells the controller to look in the core view directory instead of the plugin view directory
        $this->structure->setDefaultView(APPDIR);
        parent::preAction();

        $this->requireLogin();

        // Auto load language for the controller
        Language::loadLang(
            [Loader::fromCamelCase(get_class($this))],
            null,
            dirname(__FILE__) . DS . 'language' . DS
        );
        Language::loadLang(
            '{{snake_case_name}}_controller',
            null,
            dirname(__FILE__) . DS . 'language' . DS
        );

////    // These statements tell the controller to look again the plugin view directory
        // Override default view directory
        $this->view->view = "default";
        $this->orig_structure_view = $this->structure->view;
        $this->structure->view = "default";

        // Restore structure view location of the admin portal
        $this->structure->setDefaultView(APPDIR);
        $this->structure->setView(null, $this->orig_structure_view);
    }
}
