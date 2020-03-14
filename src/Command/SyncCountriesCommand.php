<?php


namespace App\Command;


use App\RestCountriesApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

class SyncCountriesCommand extends Command
{
    private const REGION = 'region';
    protected static $defaultName = 'sync:countries';
    /**
     * @var RestCountriesApiClient
     */
    private $apiClient;

    public function __construct(RestCountriesApiClient $apiClient)
    {
        parent::__construct();
        $this->apiClient = $apiClient;
    }


    protected function configure()
    {
        $this
            ->setDescription('Synchronize countries from external API.')
            ->addArgument(self::REGION, InputArgument::OPTIONAL, 'Regions divided by comma to sync from');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $regions = $input->getArgument(self::REGION) !== null
            ? explode(',', $input->getArgument(self::REGION))
            : RestCountriesApiClient::DEFAULT_REGIONS;

        foreach ($regions as $region) {
            $this->apiClient->syncByRegion($region);
        }

        return Response::HTTP_OK;
    }
}