# jira-php
Very simple classes writen in PHP to use JIRA REST API.
(Very baby-stage >.<)


Usage
-----
```php
requere_once('JIRA.php');
```

Create object of JIRA
```php
$jira = new JIRA('https://jira.atlassian.com', 'username', 'password');
```

Search with JQL query as the only parameter
```php
$result = $jira->search_issues('updated > startOfDay("-7")');
```

Search with extra parameters. Write both JQL query and the extra parameter in an array.
```php
$result = $jira->search_issues([ 'updated > startOfDay("-7")', 'fields' => [''] ]);
```

Another example with more parameters
```php
$result = $jira->search_issues([
    'updated > startOfDay("-60") and updated < startOfDay("-30")',
    'fields' => ['project', 'worklog'],
    'startAt' => 0,
    'maxResults' => 50
]);
```

Another example
```php
$jql = 'updated > startOfDay("-60") and updated < startOfDay("-30")';
$fields = ['project', 'worklog'];
$start_at = 0;
$max_results = 50;

$result = $jira->search_issues([
    $jql,
    'fields' => $fields,
    'startAt' => $start_at,
    'maxResults' => $max_results
]);
```

Handle situation when JIRA returned an error message
```php
if (property_exists($result, 'errors')) {
    echo "[JIRA] Error messages: " . $jira->error_messages();
    exit(1);
}
```

All parameters can be written as a JSON string
```php
$result = $jira->search_issues('{
    "jql": "updated > startOfDay("-7")",
    "fields": ""
}');
```

Example of using function of the parent class of JIRA class, JIRABase.
Add "true" as third parameter to have result returned in JSON string format
```php
$parameters = '{ "jql": "issue = 35213", "fields": [""], "expand": ["changelog"] }';
$json_result = $jira->query_use_post('search', $parameters, true);
```
