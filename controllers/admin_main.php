<?php
require_once PLUGINDIR . 'extension_generator' . DS . 'lib' . DS . 'extension_file_generator.php';

/**
 * Extension Generator admin main controller
 *
 * @package blesta
 * @subpackage blesta.plugins.extension_generator
 * @copyright Copyright (c) 2020, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class AdminMain extends ExtensionGeneratorController
{
    /**
     * Setup
     */
    public function preAction()
    {
        parent::preAction();

        $this->uses(['ExtensionGenerator.ExtensionGeneratorExtensions']);

        $this->structure->set('page_title', Language::_('AdminMain.index.page_title', true));
    }

    /**
     * Returns the view for a list of extensions
     */
    public function index()
    {
        // Set current page of results
        $page = (isset($this->get[1]) ? (int) $this->get[1] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_updated');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        $extensions = $this->ExtensionGeneratorExtensions->getList(
            Configure::get('Blesta.company_id'),
            $page, [$sort => $order]
        );
        $total_results = $this->ExtensionGeneratorExtensions->getListCount(Configure::get('Blesta.company_id'));

        $this->set('types', $this->ExtensionGeneratorExtensions->getTypes());
        $this->set('form_types', $this->ExtensionGeneratorExtensions->getFormTypes());
        $this->set('extensions', $extensions);
        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $total_results,
                'uri' => $this->base_uri . 'plugin/extension_generator/admin_main/index/[p]/',
                'params' => ['sort' => $sort, 'order' => $order],
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * Delete extension
     */
    public function delete()
    {
        // Redirect if invalid extension ID given
        if (!isset($this->post['id'])
            || !($extension = $this->ExtensionGeneratorExtensions->get((int) $this->post['id']))
            || ($extension->company_id != $this->company_id)
        ) {
            $this->redirect($this->base_uri . 'plugin/extension_generator/admin_main/');
        }

        // Attempt to delete the extension
        $this->ExtensionGeneratorExtensions->delete($extension->id);

        if (($errors = $this->ExtensionGeneratorExtensions->errors())) {
            // Error
            $this->flashMessage('error', $errors);
        } else {
            // Success
            $this->flashMessage('message', Language::_('AdminMain.!success.package_deleted', true));
        }

        $this->redirect($this->base_uri . 'plugin/extension_generator/admin_main/');
    }

    /**
     * Returns the view to be rendered when configuring the general settings for an extension
     */
    public function general()
    {
        // Ensure any submitted extension ID is valid
        if (isset($this->get[0])
            && (!($extension = $this->ExtensionGeneratorExtensions->get($this->get[0]))
                || $extension->company_id != $this->company_id)
        ) {
            $this->redirect($this->base_uri . 'plugin/extension_generator/admin_main/');
        }

        // Add/update the extension
        if (!empty($this->post))
        {
            if (!isset($this->post['code_examples'])) {
                $this->post['code_examples'] = 0;
            }

            if (isset($extension)) {
                $extension_id = $extension->id;
                $this->ExtensionGeneratorExtensions->edit($extension_id, $this->post);
            } else {
                $this->post['company_id'] = Configure::get('Blesta.company_id');
                $extension_id = $this->ExtensionGeneratorExtensions->add($this->post);
            }

            if (($errors = $this->ExtensionGeneratorExtensions->errors())) {
                $this->setMessage('error', $errors, false, null, false);

                $vars = (object) $this->post;
            } else {
                // Redirect to the next step in the configuration process
                $extension = $this->ExtensionGeneratorExtensions->get($extension_id);
                $this->redirect(
                    $this->base_uri
                    . 'plugin/extension_generator/admin_main/' . $extension->type . 'basic/' . $extension->id
                );
            }
        } else {
            $vars = isset($extension) ? $extension : null;
        }

        // Set the view to render for all actions under this controller
        $this->set('extension_types', $this->ExtensionGeneratorExtensions->getTypes());
        $this->set('form_types', $this->ExtensionGeneratorExtensions->getFormTypes());
        $this->set('vars', $vars);

        // Set the node progress bar
        $nodes = $this->getNodes(isset($extension) ? $extension : null);
        $page_step = array_search('general', array_keys($nodes));
        $this->set(
            'progress_bar',
            $this->partial(
                'partial_progress_bar',
                ['nodes' => $nodes, 'page_step' => $page_step, 'extension' => isset($extension) ? $extension : null]
            )
        );
    }

    /**
     * Returns the view to be rendered when configuring the basic settings for an extension
     */
    public function modulebasic()
    {
        // Ensure extension exists
        if (!isset($this->get[0])
            || !($extension = $this->ExtensionGeneratorExtensions->get($this->get[0]))
            || $extension->company_id != $this->company_id
        ) {
            $this->redirect($this->base_uri . 'plugin/extension_generator/admin_main/');
        }

        if (!isset($this->SettingsCollection)) {
            Loader::loadComponents($this, ['SettingsCollection', 'Upload']);
        }
        if (!isset($this->Upload)) {
            Loader::loadComponents($this, ['Upload']);
        }

        // Handle logo upload
        $errors = null;
        if (!empty($this->files['logo'])) {
            // Set the uploads directory
            $temp = $this->SettingsCollection->fetchSetting(
                null,
                Configure::get('Blesta.company_id'),
                'uploads_dir'
            );
            $upload_path = $temp['value'] . Configure::get('Blesta.company_id') . DS . 'extension_generator' . DS;

            $this->Upload->setFiles($this->files);
            $this->Upload->setUploadPath($upload_path);
            $file_name = $extension->id . '_' . $this->files['logo']['name'];

            if (!($errors = $this->Upload->errors())) {
                @unlink($upload_path . $file_name);

                $this->Upload->writeFile('logo', false, $file_name);
                $data = $this->Upload->getUploadData();

                // Set the file name of the file that was uploaded
                if (isset($data['logo']['full_path'])) {
                    $this->post['logo_path'] = $data['logo']['full_path'];
                }

                $errors = $this->Upload->errors();
            }

            // Error, could not upload the file
            if ($errors) {
                // Attempt to remove the file if it was somehow written
                @unlink($upload_path . $file_name);
            } elseif (isset($extension->data['logo_path'])
                && $extension->data['logo_path'] != $this->post['logo_path']
            ) {
                // Remove the old logo file
                @unlink($extension->data['logo_path']);
            }
        }

        if (!$errors) {
            // Perform edit/redirect or error/set vars
            $vars = $this->processStep('modulebasic', $extension);
        } else {
            $vars = $this->post;

            $this->setMessage('error', $errors, false, null, false);
        }

        // Set the view to render for all actions under this controller
        $this->set('form_type', $extension->form_type);
        $this->set('vars', $vars);

        // Set the node progress bar
        $nodes = $this->getNodes($extension);
        $page_step = array_search('modulebasic', array_keys($nodes));
        $this->set(
            'progress_bar',
            $this->partial(
                'partial_progress_bar',
                ['nodes' => $nodes, 'page_step' => $page_step, 'extension' => $extension]
            )
        );
    }

    /**
     * Returns the view to be rendered when configuring the module fields for an extension
     */
    public function modulefields()
    {
        // Ensure extension exists
        if (!isset($this->get[0])
            || !($extension = $this->ExtensionGeneratorExtensions->get($this->get[0]))
            || $extension->company_id != $this->company_id
        ) {
            $this->redirect($this->base_uri . 'plugin/extension_generator/admin_main/');
        }

        if (!empty($this->post)) {
            $array_fields = ['module_rows', 'package_fields', 'service_fields'];
            foreach ($array_fields as $array_field) {
                if (!isset($this->post[$array_field])) {
                    // Set empty array inputs
                    $this->post[$array_field] = [];
                } else {
                    // Get the index of the record that should be marked as the name key
                    $key_index = isset($this->post[$array_field]['name_key'])
                        ? $this->post[$array_field]['name_key']
                        : 0;

                    // Mark all records as NOT the name key
                    $this->post[$array_field]['name_key'] = array_fill(
                        0,
                        count($this->post[$array_field]['name']),
                        'false'
                    );

                    // Mark the given record as the name key
                    if (isset($this->post[$array_field]['name_key'][$key_index])) {
                        $this->post[$array_field]['name_key'][$key_index] = 'true';
                    }
                }
            }
        }

        // Perform edit/redirect or error/set vars
        $vars = $this->processStep('modulefields', $extension);

        // Set the view to render for all actions under this controller
        $this->set('field_types', $this->getFieldTypes());
        $this->set('vars', $vars);

        // Set the node progress bar
        $nodes = $this->getNodes($extension);
        $page_step = array_search('modulefields', array_keys($nodes));
        $this->set(
            'progress_bar',
            $this->partial(
                'partial_progress_bar',
                ['nodes' => $nodes, 'page_step' => $page_step, 'extension' => $extension]
            )
        );
    }

    /**
     * Returns the view to be rendered when configuring the additional module features for an extension
     */
    public function modulefeatures()
    {
        // Ensure extension exists
        if (!isset($this->get[0])
            || !($extension = $this->ExtensionGeneratorExtensions->get($this->get[0]))
            || $extension->company_id != $this->company_id
        ) {
            $this->redirect($this->base_uri . 'plugin/extension_generator/admin_main/');
        }

        // Set empty array inputs
        if (!empty($this->post)) {
            if (!isset($this->post['service_tabs'])) {
                $this->post['service_tabs'] = [];
            }

            if (!isset($this->post['cron_tasks'])) {
                $this->post['cron_tasks'] = [];
            }
        }

        // Perform edit/redirect or error/set vars
        $vars = $this->processStep('modulefeatures', $extension);

        // Set the view to render for all actions under this controller
        $this->set('tab_levels', $this->getTabLevels());
        $this->set('task_types', $this->getTaskTypes());
        $this->set('optional_functions', $this->getOptionalFunctions());
        $this->set('vars', $vars);

        // Set the node progress bar
        $nodes = $this->getNodes($extension);
        $page_step = array_search('modulefeatures', array_keys($nodes));
        $this->set(
            'progress_bar',
            $this->partial(
                'partial_progress_bar',
                ['nodes' => $nodes, 'page_step' => $page_step, 'extension' => $extension]
            )
        );
    }

    /**
     * Returns the view to be rendered when configuring the module fields for an extension
     */
    public function confirm()
    {
        // Ensure extension exists
        if (!isset($this->get[0])
            || !($extension = $this->ExtensionGeneratorExtensions->get($this->get[0]))
            || $extension->company_id != $this->company_id
        ) {
            $this->redirect($this->base_uri . 'plugin/extension_generator/admin_main/');
        }

        // Update the extension
        if (!empty($this->post['location']))
        {
            $directories = [
                'module' => COMPONENTDIR . 'modules' . DS,
                'plugin' => PLUGINDIR,
                'merchant' => COMPONENTDIR . 'gateways' . DS . 'merchant' . DS,
                'nonmerchant' => COMPONENTDIR . 'gateways' . DS . 'nonmerchant' . DS,
            ];

            $directory = '';
            switch ($this->post['location']) {
                case 'custom':
                    $directory = isset($this->post['custom_path'])
                        ? $this->post['custom_path']
                        : $directories['module'];
                    break;
                case 'upload':
                    $this->uses(['Companies']);
                    $this->components(['SettingsCollection']);
                    $temp = $this->SettingsCollection->fetchSetting(
                        $this->Companies,
                        Configure::get('Blesta.company_id'),
                        'uploads_dir'
                    );
                    $directory = $temp['value'];
                    break;
                default:
                    $directory = isset($directories[$extension->type])
                        ? $directories[$extension->type]
                        : $directories['module'];
                    break;
            }
            $directory = rtrim($directory, DS) . DS;

            try {
                // Load the extension generator
                $generator = new ExtensionFileGenerator($extension->type, (array)$extension);
                $generator->setOutputDir($directory);

                // Generate the extension files.  This is where the magic happens.
                $generator->parseAndOutput();

                $this->flashMessage(
                    'message',
                    Language::_(
                        'AdminMain.!success.' . $extension->type . '_created',
                        true,
                        $directory . str_replace(' ', '_', strtolower($extension->name))
                    ),
                    null,
                    false
                );

                // Redirect to the list page
                $this->redirect($this->base_uri . 'plugin/extension_generator/admin_main/');
            } catch (Exception $ex) {
                $this->setMessage(
                    'error',
                    Language::_('AdminMain.!error.generation_failed', true, $ex->getMessage()),
                    false,
                    null,
                    false
                );
            }
        }

        $this->set('locations', $this->getFileLocations($extension->type));

        // Set the node progress bar
        $nodes = $this->getNodes($extension);
        $page_step = array_search('confirm', array_keys($nodes));
        $this->set(
            'progress_bar',
            $this->partial(
                'partial_progress_bar',
                ['nodes' => $nodes, 'page_step' => $page_step, 'extension' => $extension]
            )
        );
    }

    private function processStep($step, $extension)
    {
        $this->ArrayHelper = $this->DataStructure->create('Array');
        // Update the extension
        if (!empty($this->post))
        {
            $temp_vars = $this->post;

            // Convert array input to a more usable form before storing
            foreach ($temp_vars as $key => $value) {
                if (is_array($value) && $key != 'optional_functions') {
                    $temp_vars[$key] = $this->ArrayHelper->keyToNumeric($value);
                }
            }

            // If this step contains optional functions, set unchecked options
            $optional_function_steps = ['modulefeatures'];
            if (in_array($step, $optional_function_steps)) {
                if (!isset($temp_vars['optional_functions'])) {
                    $temp_vars['optional_functions'] = [];
                }

                foreach ($this->getOptionalFunctions() as $optional_function => $settings) {
                    if (!isset($temp_vars['optional_functions'][$optional_function])) {
                        $temp_vars['optional_functions'][$optional_function] = 'false';
                    }
                }
            }

            // Update the extension with the new data
            $vars = ['data' => array_merge($extension->data, $temp_vars)];
            $this->ExtensionGeneratorExtensions->edit($extension->id, $vars);

            if (($errors = $this->ExtensionGeneratorExtensions->errors())) {
                $this->setMessage('error', $errors, false, null, false);

                $vars = $this->post;
            } else {
                $next_step = $this->getNextStep($step, $extension->form_type);

                // Redirect to the next step in the configuration process
                $this->redirect(
                    $this->base_uri
                    . 'plugin/extension_generator/admin_main/' . $next_step . '/' . $extension->id
                );
            }
        } else {
            // Set vars stored by the extension record
            $vars = $extension->data;

            // Convert array input to a more usable form before displaying
            foreach ($vars as $key => $value) {
                if (is_array($value) && $key != 'optional_functions') {
                    $vars[$key] = $this->ArrayHelper->numericToKey($value);
                }
            }
        }

        return $vars;
    }

    /**
     * Gets a list of field types and their languages
     *
     * @return A list of field types and their languages
     */
    private function getFieldTypes()
    {
        return [
            'Text' => Language::_('AdminMain.getfieldtypes.text', true),
            'Textarea' => Language::_('AdminMain.getfieldtypes.textarea', true),
            'Checkbox' => Language::_('AdminMain.getfieldtypes.checkbox', true)
        ];
    }

    /**
     * Gets a list of cron task types and their languages
     *
     * @return A list of cron task types and their languages
     */
    private function getTaskTypes()
    {
        return [
            'time' => Language::_('AdminMain.gettasktypes.time', true),
            'interval' => Language::_('AdminMain.gettasktypes.interval', true)
        ];
    }

    /**
     * Gets a list of tab levels and their languages
     *
     * @return A list of tab levels and their languages
     */
    private function getTabLevels()
    {
        return [
            'staff' => Language::_('AdminMain.gettablevels.staff', true),
            'client' => Language::_('AdminMain.gettablevels.client', true)
        ];
    }

    /**
     * Gets a list of optional functions and their settings
     *
     * @return A list of optional functions and their settings
     */
    private function getOptionalFunctions()
    {
        $functions = [
            'upgrade' => ['enabled' => 'true'],
            'cancelService' => ['enabled' => 'true'],
            'suspendService' => ['enabled' => 'true'],
            'unsuspendService' => ['enabled' => 'true'],
            'renewService' => ['enabled' => 'true'],
            'addPackage' => ['enabled' => 'true'],
            'editPackage' => ['enabled' => 'true'],
            'deletePackage' => ['enabled' => 'true'],
            'addModuleRow' => ['enabled' => 'true'],
            'editModuleRow' => ['enabled' => 'true'],
            'deleteModuleRow' => ['enabled' => 'false'],
            'getGroupOrderOptions' => ['enabled' => 'false'],
            'selectModuleRow' => ['enabled' => 'false'],
            'getAdminServiceInfo' => ['enabled' => 'false'],
            'getClientServiceInfo' => ['enabled' => 'false']
        ];

        foreach ($functions as $function => &$settings) {
            $settings['tooltip'] = Language::_(
                'AdminMain.getoptionalfunctions.tooltip_' . $function,
                true
            );
        }

        return $functions;
    }

    /**
     * Gets a list of file generation locations and their languages
     *
     * @return A list of file generation locations and their languages
     */
    private function getFileLocations($extention_type)
    {
        $locations = [];
        switch ($extention_type)
        {
            case 'plugin':
                $locations['extension'] = Language::_('AdminMain.getfilelocations.plugin', true);
                break;
            case 'gateway':
                $locations['extension'] = Language::_('AdminMain.getfilelocations.gateway', true);
                break;
            default:
                $locations['extension'] = Language::_('AdminMain.getfilelocations.module', true);
                break;
        }

        return $locations + [
            'upload' => Language::_('AdminMain.getfilelocations.upload', true),
            'custom' => Language::_('AdminMain.getfilelocations.custom', true)
        ];
    }

    /**
     * Gets a list of progress nodes for the given extension
     *
     * @param stdClass $extension The extension for which to get progress nodes
     * @return array A list of progress nodes, keyed by the step to which they should link
     */
    private function getNodes($extension = null)
    {
        // Use a simplified set of steps if set to use the basic extension form
        $node_sets = [];
        if (isset($extension) && $extension->form_type == 'basic') {
            $node_sets = [
                'module' => [
                    'modulebasic' => Language::_('AdminMain.getnodes.basic_info', true),
                ],
                'plugin' => [],
                'merchant' => [],
                'nonmerchant' => [],
            ];
        } else {
            $node_sets = [
                'module' => [
                    'modulebasic' => Language::_('AdminMain.getnodes.basic_info', true),
                    'modulefields' => Language::_('AdminMain.getnodes.module_fields', true),
                    'modulefeatures' => Language::_('AdminMain.getnodes.additional_features', true),
                ],
                'plugin' => [],
                'merchant' => [],
                'nonmerchant' => [],
            ];
        }

        $nodes = ['general' => Language::_('AdminMain.getnodes.general_settings', true)];

        // Add the nodes for the given extension type
        if (isset($extension)) {
            foreach ($node_sets as $type => $node_set) {
                if ($extension->type == $type) {
                    $nodes += $node_set;
                    break;
                }
            }
        } else {
            $nodes['modulebasic'] = Language::_('AdminMain.getnodes.basic_info', true);
        }

        $nodes['confirm'] = Language::_('AdminMain.getnodes.confirm', true);

        return $nodes;
    }


    /**
     * Returns the next step in the chain of extension forms
     *
     * @param string $current_step The current step being displayed
     * @return string The next step to be displayed
     */
    private function getNextStep($current_step, $form_type = 'advanced')
    {
        $step_mapping = [
            'general' => 'modulebasic',
            'modulebasic' => 'modulefields',
            'modulefields' => 'modulefeatures',
            'modulefeatures' => 'confirm',
            'confirm' => 'general',
        ];

        // Use a simplified set of steps if set to use the basic extension form
        if ($form_type == 'basic') {
            $step_mapping = [
                'general' => 'modulebasic',
                'modulebasic' => 'confirm',
                'confirm' => 'general',
            ];
        }

        return isset($step_mapping[$current_step]) ? $step_mapping[$current_step] : 'general';
    }
}
