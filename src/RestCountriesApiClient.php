<?php


namespace App;


use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RestCountriesApiClient
{
    private const API_URL = 'https://restcountries.eu';
    private const SERVICE_NAME =  'region';
    private const API_VERSION = 'v2';
    public const DEFAULT_REGIONS = ['europe', 'asia'];

    /**
     * @var DenormalizerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var CountryDTOToCountry
     */
    private $transformer;

    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        DenormalizerInterface $serializer,
        ValidatorInterface $validator,
        CountryDTOToCountry $transformer,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->transformer = $transformer;
        $this->client = $client = HttpClient::create();
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function syncByRegion(string $region): void
    {
        $countries = $this->getCountriesInRegion($region);

        foreach ($countries as $country) {
            $countryDTO = $this->serializer->denormalize($country, CountryDTO::class, null);
            $violations = $this->validator->validate($countryDTO);
            if($violations->count()) {
                $this->logger->critical('There is a problem during validation....');
                continue;
            }
            $country = $countryDTO->toEntity();
            $this->entityManager->persist($country);
        }

        $this->entityManager->flush();
    }

    /**
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getCountriesInRegion(string $region): array
    {
        return $this->client->request(
            'GET',
            sprintf('%s/rest/%s/%s/%s', self::API_URL, self::API_VERSION, self::SERVICE_NAME, $region)
        )->toArray();
    }
}