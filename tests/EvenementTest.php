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
        $user = TestEntityFactory::createUser($em, $hasher);
        $categorie1 = TestEntityFactory::createCategorie($em);
        $categorie2 = TestEntityFactory::createCategorie(($em));
        $categorie3 = TestEntityFactory::createCategorie($em);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/evenement/new');
        $this->assertResponseIsSuccessful();

        $titre = 'Spectacle ' . uniqid();
        $dateDebut = (new DateTimeImmutable('2022-12-23 12:20'))->format('Y-m-d\TH:i');
        $dateFin = (new DateTimeImmutable('2025-02-15 14:23'))->format('Y-m-d\TH:i');


        $token = $crawler->filter('#evenement__token')->attr('value');

        $client->request('POST', '/evenement/new', [
            'evenement' => [
                'titre' => $titre,
                'description' => 'Description test',
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'lieu' => 'salle 1',
                'capacite' => '20',
                'categories' => [
                    (string) $categorie1->getId(),
                    (string) $categorie2->getId(),
                    (string) $categorie3->getId(),
                ],
                '_token' => $token,
            ],
        ]);

        $this->assertResponseRedirects('/evenement');
        // verification de la bd 
        $repository = $em->getRepository(Evenement::class);
        $evenement = $repository->findOneBy(['titre' => $titre]);
        self::assertNotNull($evenement);
        self::assertSame($user->getId(), $evenement->getAuteur()->getId());
        //relation manytomany 
        $tab = [];
        foreach ($evenement->getCategories() as $value) {
            // dump($value->getId());
            array_push($tab, $value->getId()) ;
            // dump($tab);
        }
       
        $tabcat = [$categorie3->getId(),$categorie1->getId(),$categorie2->getId()]; $tabdiff = array_diff($tab,$tabcat);
        // dump($tabdiff);
        self::assertEmpty($tabdiff);
       
        $this->assertResponseIsSuccessful();

    }
}
