<?php

namespace Helper;

class LumenRequest extends \Codeception\Module
{
    /**
     * Send POST data to URL
     * @param $url
     * @param array $data
     * @return mixed
     * @throws \Codeception\Exception\ModuleException
     */
    public function post($url, array $data = [])
    {
        $result = $this->getModule('Lumen')->_request('POST', $url, $data);
        return json_decode($result, true);
    }

    /**
     * Send PUT data to URL
     * @param $url
     * @param array $data
     * @return mixed
     * @throws \Codeception\Exception\ModuleException
     */
    public function put($url, array $data = [])
    {
        $result = $this->getModule('Lumen')->_request('PUT', $url, $data);
        return json_decode($result, true);
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed
     * @throws \Codeception\Exception\ModuleException
     */
    public function get($url, array $data = [])
    {
        $result = $this->getModule('Lumen')->_request('GET', $url, $data);
        return json_decode($result, true);
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed
     * @throws \Codeception\Exception\ModuleException
     */
    public function delete($url, array $data = [])
    {
        $result = $this->getModule('Lumen')->_request('DELETE', $url, $data);
        return json_decode($result, true);
    }

}
