<?php

namespace App\Module\Proxy\UI\Console;

use App\Message\ParseUrlMessage;
use App\Module\AddressNormalizer\Service\AvitoDriver;
use App\Module\AddressNormalizer\Service\StreetNormalizer;
use App\Module\Proxy\Entity\Proxy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'proxy:load',
    description: 'Загрузка в БД списка прокси через файл (HidemeVPN)',
)]
class LoadProxyCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StreetNormalizer $streetNormalizer,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

//        $text = fopen("files/proxy.txt", 'rb');
//        if ($text) {
//            $i = 0;
//            while (($buffer = fgets($text)) !== false) {
//                $proxyEntity = new Proxy();
//                $proxyEntity->setStatus(true);
//                $proxyEntity->setProxy($buffer);
//                $this->entityManager->persist($proxyEntity);
//                if ($i === 500) {
//                    $this->entityManager->flush();
//                    $i = 0;
//                    continue;
//                }
//                $i++;
//            }
//        }
//        fclose($text);

        $this->streetNormalizer->setDriver(new AvitoDriver());
        print_r($this->streetNormalizer->normalize("Большая Охта, просп. Энергетиков, 11к2"));


        $io->success('Прокси сохранены!');
        return Command::SUCCESS;
    }
}
