<?php


namespace App\Services\Webhook;

/**
 * Class ServerResponse
 *
 * @method bool   getOk()          If the request was successful
 * @method mixed  getResult()      The result of the query
 * @method int    getErrorCode()   Error code of the unsuccessful request
 * @method string getDescription() Human-readable description of the result / unsuccessful request
 *
 * @todo method ResponseParameters getParameters()  Field which can help to automatically handle the error
 */
class ServerResponse extends Entity
{
    /**
     * ServerResponse constructor.
     *
     * @param array $data
     * @param string $username
     */
    public function __construct(array $data, $username)
    {
        // Make sure we don't double-save the raw_data
        unset($data['raw_data']);
        $data['raw_data'] = $data;

        $is_ok = isset($data['ok']) ? (bool)$data['ok'] : false;
        $result = isset($data['result']) ? $data['result'] : null;

        if ($is_ok && is_array($result)) {
            if ($this->isAssoc($result)) {
                $data['result'] = $this->createResultObject($result, $username);
            } else {
                $data['result'] = $this->createResultObjects($result, $username);
            }
        }

        parent::__construct($data, $username);
    }

    /**
     * Check if array is associative
     *
     * @link https://stackoverflow.com/a/4254008
     *
     * @param array $array
     *
     * @return bool
     */
    protected function isAssoc(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    /**
     * If response is ok
     *
     * @return bool
     */
    public function isOk()
    {
        return (bool)$this->getOk();
    }

    /**
     * Print error
     *
     * @see https://secure.php.net/manual/en/function.print-r.php
     *
     * @param bool $return
     *
     * @return bool|string
     */
    public function printError($return = false)
    {
        $error = sprintf('Error N: %s, Description: %s', $this->getErrorCode(), $this->getDescription());
        if ($return) {
            return $error;
        }
        echo $error;

        return true;
    }
}
