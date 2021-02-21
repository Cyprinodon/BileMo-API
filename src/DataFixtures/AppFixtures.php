<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Color;
use App\Entity\Dimensions;
use App\Entity\Display;
use App\Entity\OS;
use App\Entity\Processor;
use App\Entity\Product;
use App\Entity\StoreAccount;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Brand;
use App\Entity\Phone;
use App\Entity\Storage;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected  $passwordEncoder;
    protected $data = [
        "storages" => [
            16, 32, 64, 128, 256, 512
        ],
        "manufacturers" => [
            "Shamesung", "Notkia", "Banana", "Ouaoueille", "Chat-o-mie"
        ],
        "series" => [
            100, 200, 300, 400, 600, 700, 800
        ],
        "systems" => [
            [ "manufacturer" => "Gogol", "name" => "Blandroid" ],
            [ "manufacturer" => "Banana", "name" => "AïeOS" ],
            [ "manufacturer" => "Mehzilla", "name" => "Fireslug OS" ],
            [ "manufacturer" => "Mediocresoft", "name" => "Widow phone 12" ]
        ],
        "phones" => [
            "names" => [
                "Platipus", "Octopus", "Caterpillar", "Eucaryote",
                "Slug", "Worm", "Seaweed", "Blobfish", "Raflesia", "Weasel",
                "Aye-Aye", "Hagfish", "physarum"
            ]
        ],
        "sizes" => [
            ["width" => 1080, "height" => 1920],
            ["width" => 828, "height" => 792],
            ["width" => 1125, "height" => 2436],
            ["width" => 1242, "height" => 2688],
            ["width" => 1125, "height" => 2436],
            ["width" => 828, "height" => 792],
            ["width" => 750, "height" => 1334],
            ["width" => 640, "height" => 1136]
        ],
        "colors" => [
            ["name" => "Night", "hexadecimal" => "0C090A"],
            ["name" => "Gunmetal", "hexadecimal" => "2C3539"],
            ["name" => "Platinum", "hexadecimal" => "E5E4E2"],
            ["name" => "Steel Blue", "hexadecimal" => "4863A0"],
            ["name" => "Spring Green", "hexadecimal" => "4AA02C"],
            ["name" => "Goldenrod", "hexadecimal" => "EDDA74"],
            ["name" => "Cornsilk", "hexadecimal" => "FFF8DC"],
            ["name" => "Copper", "hexadecimal" => "B87333"],
            ["name" => "Sepia", "hexadecimal" => "7F462C"],
            ["name" => "Love Red", "hexadecimal" => "E41B17"],
            ["name" => "Plum Velvet", "hexadecimal" => "7D0552"],
            ["name" => "Rose", "hexadecimal" => "E8ADAA"],
            ["name" => "Milk White", "hexadecimal" => "FEFCFF"]
        ],
        "stores" => [
            ["name" => "Phonogonie", "email" => "phonogonie.service@gmail.com"],
            ["name" => "A-Phonie", "email" => "welcome@aphonie.com"],
            ["name" => "Phony Business", "email" => "reception@phonybusiness.us"]
        ],
        "customers" => [
            ["firstname" => "Amir", "lastname" => "Najjar"],
            ["firstname" => "George", "lastname" => "Abitbol"],
            ["firstname" => "Susan", "lastname" => "Mustard"],
            ["firstname" => "Francis", "lastname" => "Levasseur"],
            ["firstname" => "Mathilda", "lastname" => "Sandevoir"],
            ["firstname" => "Dimitri", "lastname" => "Ivanovich Sokoulov"],
            ["firstname" => "Asmee", "lastname" => "Kshatriya"]
        ],
        "password" => "multipass"
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        //Hydratation des dimensions possibles ============================
        $persistedDimensions = [];
        foreach($this->data["sizes"] as $sizeData) {
            $dimensions = new Dimensions();
            $dimensions->setHeight($sizeData["height"]);
            $dimensions->setWidth($sizeData["width"]);
            $dimensions->setUnit("px");
            $manager->persist($dimensions);
            array_push($persistedDimensions, $dimensions);
        }

        //Hydratation des capacités de stockage mémoire ============================
        $persistedStorage = [];
        foreach($this->data["storages"] as $storageData) {
            $storage = new Storage();
            $storage->setCapacity($storageData);
            $storage->setUnit("Go");
            $manager->persist($storage);
            array_push($persistedStorage, $storage);
        }

        //Hydratation des systèmes d'exploitation ============================
        $persistedOs = [];
        foreach($this->data["systems"] as $systemData) {
            $os = new OS();
            $os->setManufacturer($systemData["manufacturer"]);
            $os->setName($systemData["name"]);
            $manager->persist($os);
            array_push($persistedOs, $os);
        }

        //Hydratation des couleurs ============================
        $persistedColor = [];
        foreach($this->data["colors"] as $colorData) {
            $color = new Color();
            $color->setName($colorData["name"]);
            $color->setHexadecimal($colorData["hexadecimal"]);
            $manager->persist($color);
            array_push($persistedColor, $color);
        }

        //Hydratation des boutiques ============================
        $persistedStore = [];
        foreach($this->data["stores"] as $storeData) {
            $store = new StoreAccount();
            $store->setName($storeData["name"]);
            $store->setEmail($storeData["email"]);

            $plainPassword = $this->data["password"];
            $store->setPassword($this->passwordEncoder->encodePassword($store, $plainPassword));
            $manager->persist($store);
            array_push($persistedStore, $store);
        }

        //Hydratation des consommateurs ============================
        $persistedCustomer = [];
        foreach($this->data["customers"] as $customerData) {
            $customer = new Customer();
            $customer->setFirstName($customerData["firstname"]);
            $customer->setLastName($customerData["lastname"]);
            $store = $persistedStore[array_rand($persistedStore)];
            $customer->setStoreAccount($store);
            $customer->setCreatedAt(new DateTime());
            $manager->persist($customer);
            array_push($persistedCustomer, $customer);
        }

        //Hydratation des téléphones ============================
        //copie superficielle de noms de téléphones
        $phoneNames = $this->data["phones"]["names"];

        //Par simplicité, un téléphone par fabricant
        foreach($this->data["manufacturers"] as $manufacturerData ) {
            $phone = new Phone();

            //Hydratation de la marque du téléphone ---------------------------
            $brand = new Brand();
            $brand->setManufacturer($manufacturerData);
            $phoneName = $phoneNames[array_rand($phoneNames)];
            $brand->setName($phoneName);

            // Retrait du nom de la liste temporaire (pour éviter les doublons)
            $key = array_search($phoneName,$phoneNames);
            array_splice($phoneNames, $key, 1);

            //Ajout du numéro de série
            $serial = $this->data["series"][array_rand($this->data["series"])];
            $brand->setSerie($serial);
            $manager->persist($brand);

            //Hydratation du processeur ---------------------------
            $processor = new Processor();
            $processor->setBrand($brand);
            $processor->setCores(rand(1, 5));
            $processor->setFrequency(2400);
            $manager->persist($processor);

            //Hydratation des écrans ---------------------------
            $size = $persistedDimensions[array_rand($persistedDimensions)];
            $display = new Display();
            $display->setPixelSize($size);
            $display->setViewport($size);
            $display->setTouchscreen(true);
            $manager->persist($display);

            //Hydratation des produits ---------------------------
            $product = new Product();
            $product->setPrice(rand(200, 2000));
            $product->setDescription("Juste un téléphone. Rien de plus !");
            $product->setPhone($phone);
            $product->setCreatedAt(new DateTime());
            $manager->persist($product);

            //Assemblage ---------------------------
            $phone->setBrand($brand);
            $phone->setDisplay($display);
            $phone->setProcessor($processor);
            $phone->setSize($size);
            $phone->addProduct($product);
            $phone->setWeight(rand(100, 200)); //en grammes
            $storage = $persistedStorage[array_rand($persistedStorage)];
            $phone->addStorage($storage);

            // un ou plusieurs OS aléatoires
            $systems = array_rand($persistedOs, rand(1, count($persistedOs)));
            //Dans le cas ou une seule clé est retournée, on l'enveloppe dans un tableau
            if(!is_array($systems)) {
                $systems = [$systems];
            }

            foreach($systems as $key) {
                $phone->addPossibleOS($persistedOs[$key]);
            }

            //Une ou plusieurs couleurs aléatoires
            $colors = array_rand($persistedColor, rand(1, 4));
            //Dans le cas ou une seule clé est retournée, on l'enveloppe dans un tableau
            if(!is_array($colors)) {
                $colors = [$colors];
            }

            foreach($colors as $key) {
                $phone->addPossibleColor($persistedColor[$key]);
            }

            $manager->persist($phone);
        }

        $manager->flush();
    }
}
