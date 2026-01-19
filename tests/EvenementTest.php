<?php

namespace App\Tests;

use App\Entity\Evenement;
use App\Tests\Helper\TestEntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EvenementTest extends WebTestCase
{
    public function testLoggedUserCanCreateEvenementwITHcATEGORIE(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $user = TestEntityFactory::createUser($em,$hasher);
        $categorie1 = TestEntityFactory::createCategorie($em);
        // $val0 = $checkboxes->eq(0)->attr('value');
        $categorie2 = TestEntityFactory::createCategorie(($em));
        $categorie3 = TestEntityFactory::createCategorie($em);
        $client->loginUser($user);
        $client->request('GET','/evenement/new');
        // $crawler = $client->request('GET', '/evenement/new');

// dump($crawler->filter('[name^="evenement[categories]"]')->count());
// dump($crawler->filter('[name="evenement[categories][]"]')->count());
// dump($crawler->filter('[name="evenement[categories]"]')->count());

// // Optionnel : voir le HTML autour
// dump($crawler->filter('form')->html());

        $this->assertResponseIsSuccessful();
        $titre = "Spectacle ".uniqid();
        $dateDebut = (new DateTimeImmutable('2022-12-23 12:20'))->format("Y-m-d\TH:i");
        $dateFin = (new DateTimeImmutable('2025-02-15 14:23'))->format('Y-m-d\TH:i');
        $client->submitForm('Save', [
            'evenement[titre]' => $titre,
            'evenement[description]' => "Description test",
            'evenement[dateDebut]' => $dateDebut,
            'evenement[dateFin]' =>$dateFin,
            'evenement[lieu]' => "salle 1",
           
            'evenement[capacite]' => '20',
            'evenement[categories]' =>[$categorie1->getId(), $categorie2->getId(),$categorie3->getId()]

        ]);
        $this->assertResponseRedirects("/evenement");
       
    }
}
