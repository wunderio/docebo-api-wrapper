# Docebo Api Wrapper

A PHP wrapper for Docebo REST API. [https://www.docebo.com/lms-docebo-api-third-party-integration](https://www.docebo.com/lms-docebo-api-third-party-integration)

## Installation

### With Composer

```json
{
    "require": {
        "suru/doceboapiwrapper": "1.*"  
	},
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/surupartners/docebo-api-wrapper"
        }
    ],
}
```

```php
<?php
require 'vendor/autoload.php';

use Suru\Docebo\DoceboApiWrapper;

// initialise Docebo API Wrapper
$docebo = new DoceboApiWrapper(
    $base_url,      // URL of your Docebo platform
    $client_id,     // Client ID of your third-party application
    $client_secret  // Client secret of your third-party application
);

// retrieving access token
$token = $docebo->getAccessToken();

// example of endpoint request
$courses = $docebo->course()->getCourseList();
```