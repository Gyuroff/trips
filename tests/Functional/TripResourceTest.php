<?php


namespace App\Tests\Functional;

use App\Entity\Country;
use App\Entity\Trip;
use App\Test\HelperApiTestCase;
use DateTime;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class TripResourceTest extends HelperApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateTrip()
    {
        $client = self::createClient();
        $client->request('POST', '/api/trips');
        $this->assertResponseStatusCodeSame(401);

        $this->createUserAndLogIn($client, 'example@example.com', 'password');

    }

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();
        $country = $this->crerateCountry();
        $user = $this->createUser('user1@example.com', 'foo');
        $user2 = $this->createUser('user2@example.com', 'foo');

        $em = self::$container->get('doctrine')->getManager();

        $trip = new Trip();
        $trip->addUser($user);
        $trip->setCountry($country);
        $trip->setNotes('Some notes');
        $trip->setStartDate(new DateTime('01-01-2020'));
        $trip->setEndDate(new DateTime('01-08-2020'));
        $em->persist($trip);
        $em->flush();

        $this->logIn($client, 'user1@example.com', 'foo');
        $client->request('PUT', '/api/trips/'.$trip->getId(), [
            'json' => ['notes' => 'other notes']
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->logIn($client, 'user2@example.com', 'foo');
        $client->request('PUT', '/api/trips/'.$trip->getId(), [
            'json' => ['notes' => 'some other notes']
        ]);
        $this->assertResponseStatusCodeSame(403, 'Only the creators can edit a trip');
    }

    private function crerateCountry()
    {
        $country = new Country();
        $country->setName('Bulgaria');
        $country->setAlpha3Code('BGL');

        return $country;
    }
}