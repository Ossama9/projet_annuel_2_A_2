<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Feature;
use App\Entity\Model;
use App\Entity\Offer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $brand = new Brand();
        $brand->setName('Apple');
        $brand->setDescription("Apple est créé le 1 er avril 1976 dans la maison d'enfance de Steve Jobs à Los Altos, puis constituée sous forme de société le 3 janvier 1977.Elle prend diverses facettes coordonnées avec l'évolution du monde informatique qu'elle précède, partant d'un monde sans ordinateur personnel à une société du XXI e siècle interconnectée par l'intermédiaire de terminaux fixes et mobiles.");
        $brand->setFoundationDate(new \DateTime('01-01-1977'));
        $brand->setLocation('Cupertino, USA');
        $manager->persist($brand);

        $feature = new Feature();
        $feature->setBattery(1);
        $feature->setCamera("16MP");
        $feature->setGraphicCard("RTX 2080");
        $feature->setHardDisk("256GO");
        $feature->setOsVersion("V10.1");
        $feature->setProcessor("A11");
        $feature->setRam(6);
        $feature->setScreenSize('5"8');
        $feature->setTactile(true);
        $manager->persist($feature);

        $model = new Model();
        $model->setName('Iphone 8');
        $model->setDescription("L'iPhone 8 et l'iPhone 8 Plus sont deux smartphones, modèles de la 11ᵉ génération d'iPhone de la marque Apple.");
        $model->setReleaseDate(new \DateTime('09-01-2017'));
        $model->setFeature($feature);
        $model->setBrand($brand);
        $manager->persist($model);

        $offer = new Offer();
        $offer->setProductCondition(1);
        $offer->setAmount(599);
        $offer->setModel($model);
        $manager->persist($offer);

        $offer = new Offer();
        $offer->setProductCondition(2);
        $offer->setAmount(699);
        $offer->setModel($model);
        $manager->persist($offer);

        $manager->flush();
    }
}
