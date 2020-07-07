<?php

// Success messages
$lang['AdminMain.!success.module_created'] = 'The new module has been successfully generated and can be found at %1$s'; // Where %1$s is the path to the created module's directory
$lang['AdminMain.!success.plugin_created'] = 'The new plugin has been successfully generated and can be found at %1$s'; // Where %1$s is the path to the created plugin's directory
$lang['AdminMain.!success.gateway_created'] = 'The new gateway has been successfully generated and can be found at %1$s'; // Where %1$s is the path to the created gateway's directory


// Controller get functions
$lang['AdminMain.getextensiontypes.module'] = 'Module';
$lang['AdminMain.getextensiontypes.plugin'] = 'Plugin';
$lang['AdminMain.getextensiontypes.gateway'] = 'Gateway';

$lang['AdminMain.getformtypes.basic'] = 'Basic';
$lang['AdminMain.getformtypes.advanced'] = 'Advanced';

$lang['AdminMain.getfieldtypes.text'] = 'Text';
$lang['AdminMain.getfieldtypes.textarea'] = 'Textarea';
$lang['AdminMain.getfieldtypes.checkbox'] = 'Checkbox';

$lang['AdminMain.getfieldtypes.text'] = 'Text';
$lang['AdminMain.getfieldtypes.textarea'] = 'Textarea';
$lang['AdminMain.getfieldtypes.checkbox'] = 'Checkbox';

$lang['AdminMain.gettablevels.staff'] = 'Staff';
$lang['AdminMain.gettablevels.client'] = 'Client';

$lang['AdminMain.gettasktypes.time'] = 'Time';
$lang['AdminMain.gettasktypes.interval'] = 'Interval';

$lang['AdminMain.getnodes.general_settings'] = 'General Settings';
$lang['AdminMain.getnodes.basic_info'] = 'Basic Info';
$lang['AdminMain.getnodes.module_fields'] = 'Module Fields';
$lang['AdminMain.getnodes.additional_features'] = 'Additional Features';
$lang['AdminMain.getnodes.complete'] = 'Complete';

$lang['AdminMain.getoptionalfunctions.tooltip_cancelService'] = 'Called to perform module actions on service cancellation.';
$lang['AdminMain.getoptionalfunctions.tooltip_suspendService'] = 'Called to perform module actions on service suspension.';
$lang['AdminMain.getoptionalfunctions.tooltip_unsuspendService'] = 'Called to perform module actions on service unsuspension.';
$lang['AdminMain.getoptionalfunctions.tooltip_renewService'] = 'Called to perform module actions when a service is renewed.';
$lang['AdminMain.getoptionalfunctions.tooltip_addPackage'] = 'Performs any action required to add the package on the remote server. Sets Input errors on failure, preventing the package from being added.';
$lang['AdminMain.getoptionalfunctions.tooltip_editPackage'] = 'Performs any action required to edit the package on the remote server. Sets Input errors on failure, preventing the package from being edited.';
$lang['AdminMain.getoptionalfunctions.tooltip_deletePackage'] = 'Deletes the package on the remote server. Sets Input errors on failure, preventing the package from being deleted.';
$lang['AdminMain.getoptionalfunctions.tooltip_addModuleRow'] = 'Adds the module row on the remote server. Sets Input errors on failure, preventing the row from being added.';
$lang['AdminMain.getoptionalfunctions.tooltip_editModuleRow'] = 'Edits the module row on the remote server. Sets Input errors on failure, preventing the row from being updated.';
$lang['AdminMain.getoptionalfunctions.tooltip_manageAddRow'] = 'Returns the rendered view of the add module row page';
$lang['AdminMain.getoptionalfunctions.tooltip_manageEditRow'] = 'Returns the rendered view of the edit module row page';
$lang['AdminMain.getoptionalfunctions.tooltip_getGroupOrderOptions'] = 'Returns an array of available service delegation order methods. The module will determine how each method is defined. For example, the method "first" may be implemented such that it returns the module row with the least number of services assigned to it.';
$lang['AdminMain.getoptionalfunctions.tooltip_selectModuleRow'] = 'Determines which module row should be attempted when a service is provisioned for the given group based upon the order method set for that group.';
$lang['AdminMain.getoptionalfunctions.tooltip_getAdminServiceInfo'] = 'Fetches the HTML content to display when viewing the service info in the admin interface.';
$lang['AdminMain.getoptionalfunctions.tooltip_getClientServiceInfo'] = 'Fetches the HTML content to display when viewing the service info in the client interface.';


// Boxtitle
$lang['AdminMain.index.boxtitle_extension_generator'] = 'Extension Generator';


// General settings page
$lang['AdminMain.index.heading_general_settings'] = 'General Settings';

$lang['AdminMain.index.extension_type'] = 'Extension Type';
$lang['AdminMain.index.form_type'] = 'Form Type';
$lang['AdminMain.index.example_code'] = 'Include Example Code';
$lang['AdminMain.index.tooltip_example_code'] = 'Check to include commented out lines of sample code for features such as Cron Task, Event lists and tie-ins, etc.';
$lang['AdminMain.index.basic_info'] = 'Next - Basic Info';

$lang['AdminMain.index.tooltip_form_type'] = 'Basic to use a highly truncated version of the extension form, including only the minimum necessary to generate the extension.';


// Basic info page
$lang['AdminMain.index.heading_module_basic'] = 'Basic Information';
$lang['AdminMain.index.heading_module_authors'] = 'Authors';

$lang['AdminMain.index.name'] = 'Name';
$lang['AdminMain.index.description'] = 'Description';
$lang['AdminMain.index.logo'] = 'Logo';
$lang['AdminMain.index.module_row'] = 'Module Row Name';
$lang['AdminMain.index.module_row_plural'] = 'Module Row Name (Plural)';
$lang['AdminMain.index.author_name'] = 'Author Name';
$lang['AdminMain.index.author_url'] = 'Author URL';
$lang['AdminMain.index.text_options'] = 'Options';
$lang['AdminMain.index.author_row_add'] = 'Add Author';
$lang['AdminMain.index.text_remove'] = 'Remove';
$lang['AdminMain.index.module_fields'] = 'Next - Module Fields';

$lang['AdminMain.index.tooltip_module_name'] = 'The display name of the module';
$lang['AdminMain.index.tooltip_module_description'] = 'The description shown in the module listing';
$lang['AdminMain.index.tooltip_module_logo'] = 'The logo displayed in the module listing';
$lang['AdminMain.index.tooltip_module_row'] = 'The term by which to refer to a single module row for this module';
$lang['AdminMain.index.tooltip_module_row_plural'] = 'The term by which to refer to multiple module rows for this module';

$lang['AdminMain.index.placeholder_module_name'] = 'Module Name';
$lang['AdminMain.index.placeholder_module_row'] = 'e.g. Server';
$lang['AdminMain.index.placeholder_module_row_plural'] = 'e.g. Servers';
$lang['AdminMain.index.placeholder_author_name'] = 'e.g. Blesta';
$lang['AdminMain.index.placeholder_author_url'] = 'e.g. https://blesta.com/';


// Fields page
$lang['AdminMain.index.heading_module_row_fields'] = 'Module Row Fields';
$lang['AdminMain.index.heading_package_fields'] = 'Package Fields';
$lang['AdminMain.index.heading_service_fields'] = 'Service Fields';

$lang['AdminMain.index.label'] = 'Label';
$lang['AdminMain.index.type'] = 'Type';
$lang['AdminMain.index.tooltip'] = 'Tooltip Text';
$lang['AdminMain.index.module_features'] = 'Next - Additional Features';

$lang['AdminMain.index.module_row_add'] = 'Add Module Row Field';
$lang['AdminMain.index.package_row_add'] = 'Add Package Field';
$lang['AdminMain.index.service_row_add'] = 'Add Service Field';


// Additional features page
$lang['AdminMain.index.heading_module_features'] = 'Additional Features';
$lang['AdminMain.index.heading_service_tabs'] = 'Service Management Tabs';
$lang['AdminMain.index.heading_cron_tasks'] = 'Cron Tasks';
$lang['AdminMain.index.heading_optional_functions'] = 'Optional Functions';

$lang['AdminMain.index.method_name'] = 'Method Name';
$lang['AdminMain.index.level'] = 'Level';
$lang['AdminMain.index.time'] = 'Start Time/Interval';

$lang['AdminMain.index.service_tab_row_add'] = 'Add Service Management Tab';
$lang['AdminMain.index.cron_task_row_add'] = 'Add Cron Task';

$lang['AdminMain.index.module_complete'] = 'Complete';
