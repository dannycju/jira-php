<?php
/**
* Generic class containing some common tasks to be performed on JIRA server
* by extending JIRABase class
*/
require_once('JIRABase.php');

class JIRA extends JIRABase
{
    public function __construct($url, $username, $password)
    {
        parent::__construct($url, $username, $password);
    }

    /**
    * Retrive data in defined fields or all fields for specific issue
    * Parameter: $parameters: search parameters in array or string
    * Return: all data of a issue as PHP object
    */
    public function issue($parameters)
    {
        if (is_array($parameters)) {
            $id_or_key = $parameters[0];
            unset($parameters[0]);
            foreach ($parameters as $parameter => $value) {
                if ($parameter !== 'fields' && $parameter !== 'expand') {
                    trigger_error('Parameter discarded: ' . $parameter .
                                  ' is not a valid parameter for issue()', E_USER_NOTICE);
                    unset($parameters[$parameter]);
                }
            }
            return parent::query_use_get('issue/' . $id_or_key, $parameters);
        }

        return parent::query_use_get('issue/' . $parameters);
    }

    /**
    * Retrieve specific project
    * Parameter: $parameters: search parameters in array or string
    * Return: all data of a project as PHP object
    */
    public function project($parameters)
    {
        if (is_array($parameters)) {
            $id_or_key = $parameters[0];
            unset($parameters[0]);
            foreach ($parameters as $parameter => $value) {
                if ($parameter !== 'expand') {
                    trigger_error('Parameter discarded: ' . $parameter .
                                  ' is not a valid parameter for project()', E_USER_NOTICE);
                    unset($parameters[$parameter]);
                }
            }
            return parent::query_use_get('project/' . $id_or_key, $parameters);
        }

        return parent::query_use_get('project/' . $parameters);
    }

    /**
    * Retrieve all projects
    * Return: all data of all projects as PHP object
    */
    public function projects()
    {
        return parent::query_use_get('project');
    }

    /**
    * object search_issues(array or string $parameters)
    *
    * Perform search
    * Parameter: $parameters: search parameters in array or string
    *                         array: first element: query in JQL format
    *                                following elements: other parameters
    *                         string: query in JQL format
    * Return: PHP object
    */
    public function search_issues($parameters)
    {
        if (is_array($parameters)) {
            $parameters['jql'] = $parameters[0];
            unset($parameters[0]);
            foreach ($parameters as $parameter => $value) {
                if ($parameter !== 'jql' &&
                    $parameter !== 'startAt' &&
                    $parameter !== 'maxResults' &&
                    $parameter !== 'validateQuery' &&
                    $parameter !== 'fields' &&
                    $parameter !== 'expand') {
                    trigger_error('Parameter discarded: ' . $parameter .
                                  ' is not a valid parameter for search_issues()', E_USER_NOTICE);
                    unset($parameters[$parameter]);
                }
            }
            return parent::query_use_post('search', $parameters);
        }

        $parameter['jql'] = $parameters;
        return parent::query_use_post('search', $parameter);
    }

}
?>
