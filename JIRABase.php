<?php
/**
* This class contains functions to send request to JIRA server by using GET or POST method
*/
class JIRABase
{
    protected $headers = [
        'Accept: application/json',
        'Content-type: application/json'
    ];
    protected $errors = [];

    public function __construct($url, $username, $password)
    {
        $this->jira_base_url = $url;
        $this->username = $username;
        $this->password = $password;
    }

    /**
    * Retrieve data from JIRA through REST API by using GET method
    * Parameters: $resource: the resource to look into
    *             $parameters: parameters in array or string to specify the request
    *                          example in array: [
    *                                                'fields' => ['id', 'key'],
    *                                                'expand' => ['editmeta']
    *                                            ]
    *                          example in string: '?fields=id,key&expand=editmeta'
    *             $option: false: return data as PHP object
    *                      true: return data as JSON string
    * Return: data as PHP object or JSON string
    */
    public function query_use_get($resource, $parameters='', $option=false)
    {
        if (empty($resource)) {
            return false;
        }

        $params = '';

        if (is_array($parameters)) {
            // Compile $parameters into URI format
            $is_first_parameter = true;
            while (list($parameter, $value) = each($parameters)) {
                if ($is_first_parameter) {
                    $params .= '?' . $parameter . '=';
                    $is_first_parameter = false;
                } else {
                    $params .= '&' . $parameter . '=';
                }

                if (is_array($value)) {
                    $is_first_value_ = true;
                    while (list($dummy, $value_) = each($value)) {
                        if (is_array($value_)) {
                            continue;
                        }

                        if ($is_first_value_) {
                            $params .= $value_;
                            $is_first_value_ = false;
                        } else {
                            $params .= ',' . $value_;
                        }
                    }
                } else {
                    $params .= $value;
                }
            }
        } else {
            $params = $parameters;
        }

        $options = array(
            CURLOPT_URL            => $this->jira_base_url . '/rest/api/latest/' . $resource . $params,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_HTTPGET        => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $result_json = curl_exec($curl);
        curl_close($curl);

        if ($option) {
            return $result_json;
        }

        $result = json_decode($result_json);

        if ($result) {
            if (property_exists($result, 'errors')) {
                $this->errors = $result->errorMessages;
            }
        }

        return $result;
    }

    /**
    * Retrieve data from JIRA through REST API by using POST method
    * Parameters: $resource: the resource to look into
    *             $parameters: parameters in array or JSON string to specify the request
    *                          example in array: [
    *                                                'fields' => ['id', 'key'],
    *                                                'expand' => ['editmeta']
    *                                            ]
    *                          example in JSON string: ''
    *             $option: false: return data as PHP object
    *                      true: return data as JSON string
    * Return: data as PHP object or JSON string
    */
    public function query_use_post($resource, $parameters='', $option=false)
    {
        if (empty($resource)) {
            return false;
        }

        if (is_array($parameters)) {
            $params = json_encode($parameters);
        } else {
            $params = $parameters;
        }

        $options = array(
            CURLOPT_URL            => $this->jira_base_url . '/rest/api/latest/' . $resource,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $params,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $result_json = curl_exec($curl);
        curl_close($curl);

        if ($option) {
            return $result_json;
        }

        $result = json_decode($result_json);

        if ($result) {
            if (property_exists($result, 'errors')) {
                $this->errors = $result->errorMessages;
            }
        }

        return $result;
    }

    /**
    * UNTESTED: Retrieve data from JIRA through REST API by using methods other than GET and POST
    */
    /* public function query_use_custom($request, $resource, $parameters, $option=false)
    {
        if (empty($resource)) {
            return false;
        }

        if (is_array($parameters)) {
            $params = json_encode($parameters);
        } else {
            $params = $parameters;
        }

        $options = array(
            CURLOPT_URL            => $this->jira_base_url . '/rest/api/latest/' . $resource,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_CUSTOMREQUEST  => $request,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $result_json = curl_exec($curl);
        curl_close($curl);

        if ($option) {
            return $result_json;
        }

        $result = json_decode($result_json);

        if ($result) {
            if (property_exists($result, 'errors')) {
                $this->errors = $result->errorMessages;
            }
        }

        return $result;
    } */

    /**
    * Return saved error message as string
    */
    public function error_messages()
    {
        $error_msgs = '';
        if ($error = current($this->errors)) {
            $error_msgs = '{ "' . $error . '"';
            while ($error = next($this->errors)) {
                $error_msgs .= ', "' . $error . '"';
            }
            $error_msgs .= ' }';
        }
        return $error_msgs;
    }
}
?>
