<?php

namespace App\Command;

use App\Entity\TimeSlot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-slots',
    description: 'Initialize academic time slots (90min and 120min)',
)]
class InitSlotsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 90 Min Slots
        $slots90 = [
            ['08:00', '09:30'],
            ['09:45', '11:15'],
            ['11:30', '13:00'],
            ['14:00', '15:30'],
            ['15:45', '17:15'],
        ];

        // 120 Min Slots
        $slots120 = [
            ['08:00', '10:00'],
            ['10:15', '12:15'],
            ['13:30', '15:30'],
            ['15:45', '17:45'],
        ];

        foreach ($slots90 as $s) {
            $slot = new TimeSlot();
            $slot->setStartTime(new \DateTime($s[0]));
            $slot->setEndTime(new \DateTime($s[1]));
            $slot->setType('90MIN');
            $this->em->persist($slot);
        }

        foreach ($slots120 as $s) {
            $slot = new TimeSlot();
            $slot->setStartTime(new \DateTime($s[0]));
            $slot->setEndTime(new \DateTime($s[1]));
            $slot->setType('120MIN');
            $this->em->persist($slot);
        }

        $this->em->flush();
        $io->success('Time slots initialized!');

        return Command::SUCCESS;
    }
}
