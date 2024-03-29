<?php
declare(strict_types=1);


namespace App\API\Controller;

use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
{

    private LoggerInterface $logger;
    private HttpClientInterface $httpClient;
    private Client $redis;


    public function __construct(
        LoggerInterface     $logger,
        HttpClientInterface $httpClient,
        Client              $redis

    )
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->redis = $redis;

    }

    #[Route('/index', name: 'index')]
    public function list(Request $request): Response
    {
        $this->logger->info("Called!");
        return new Response("INDEX");
    }


    #[Route('/redis', name: 'redis')]
    public function redis(Request $request): Response
    {
        $this->redis->set('api', 'testValue', 'EX', 2);

        return new Response($this->redis->get('api'));
    }


}