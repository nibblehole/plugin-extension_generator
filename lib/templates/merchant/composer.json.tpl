
{
    "_comment": "This is currently non-functional.  Use this file to make your gateway installable through composer.  Replace 'parent_repository' in the name property below with the appropriate location",
    "name": "parent_repository/{{snake_case_name}}",
    "description": "{{description}}",
    "license": "proprietary",
    "type": "blesta-gateway-merchant",
    "require": {
        "blesta/composer-installer": "~1.0"
    },
    "authors": [{{array:authors}}
        {
            "name": "{{authors.name}}",
            "url": "{{authors.url}}"
        },{{array:authors}}
    ]
}
