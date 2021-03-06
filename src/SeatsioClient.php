<?php

namespace Seatsio;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Seatsio\Accounts\Accounts;
use Seatsio\Charts\Charts;
use Seatsio\Events\Events;
use Seatsio\HoldTokens\HoldTokens;
use Seatsio\Reports\Charts\ChartReports;
use Seatsio\Reports\Events\EventReports;
use Seatsio\Reports\Usage\UsageReports;
use Seatsio\Subaccounts\Subaccounts;
use Seatsio\Workspaces\Workspaces;

class SeatsioClient
{
    private $client;

    /**
     * @var Charts
     */
    public $charts;

    /**
     * @var Events
     */
    public $events;

    /**
     * @var EventReports
     */
    public $eventReports;

    /**
     * @var ChartReports
     */
    public $chartReports;

    /**
     * @var Accounts
     */
    public $accounts;

    /**
     * @var Subaccounts
     */
    public $subaccounts;

    /**
     * @var Workspaces
     */
    public $workspaces;

    /**
     * @var HoldTokens
     */
    public $holdTokens;

    public function __construct($secretKey, $workspaceKey = null, $baseUrl = 'https://api.seatsio.net/')
    {
        $client = new Client($this->clientConfig($secretKey, $workspaceKey, $baseUrl));
        $this->charts = new Charts($client);
        $this->events = new Events($client);
        $this->eventReports = new EventReports($client);
        $this->chartReports = new ChartReports($client);
        $this->usageReports = new UsageReports($client);
        $this->accounts = new Accounts($client);
        $this->subaccounts = new Subaccounts($client);
        $this->workspaces = new Workspaces($client);
        $this->holdTokens = new HoldTokens($client);
    }

    private function clientConfig($secretKey, $workspaceKey, $baseUrl)
    {
        $stack = HandlerStack::create();
        $stack->push(self::errorHandler());
        $config = [
            'base_uri' => $baseUrl,
            'auth' => [$secretKey, null],
            'http_errors' => false,
            'handler' => $stack,
            'headers' => ['Accept-Encoding' => 'gzip']
        ];
        if ($workspaceKey) {
            $config['headers']['X-Workspace-Key'] = $workspaceKey;
        }
        return $config;
    }

    private function errorHandler()
    {
        return function (callable $handler) {
            return function ($request, array $options) use ($handler) {
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($request, $handler) {
                        $code = $response->getStatusCode();
                        if ($code < 400) {
                            return $response;
                        }
                        throw new SeatsioException($request, $response);
                    }
                );
            };
        };
    }

}
