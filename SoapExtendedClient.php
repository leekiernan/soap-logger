<?php
namespace App\SoapExtendedClient;

use Illuminate\Support\Facades\Log;

require_once(__DIR__ . '/nusoap.php');
use nusoap_client;

Log::useDailyFiles(storage_path().'/logs/nova/soap-requests.log');

class SoapExtendedClient extends \nusoap_client
{
    public function __construct($url, $options) {
        parent::__construct($url, $options);
    }

    public function call($operation, $params=array(), $namespace='http://tempuri.org', $soapAction='', $headers=false, $rpcParams=null, $style='rpc', $use='encoded') {
        $this->log_request($operation, $params, $namespace);
        $response = parent::call($operation, $params, $namespace, $soapAction, $headers, $rpcParams, $style, $use);
        $this->log_response($response);
        return $response;
    }

    private function should_log() { return !!env('LOG_REQUESTS_TO_FILE'); }

    private function log_request($operation, $params=array(), $namespace) {
        if (!$this->should_log()) { return false; }
        $data = json_encode($params);
        Log::debug("REQUEST ($operation / $namespace): $data");
    }

    private function log_response($request) {
        if (!$this->should_log()) { return false; }
        $data = json_encode($request);
        Log::debug("RESPONSE: $data\n");
    }
}
