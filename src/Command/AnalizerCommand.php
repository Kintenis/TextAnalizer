<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnalizerCommand extends Command
{
    protected static $defaultName = 'app:analizer';
    protected static $defaultDescription = 'Analizes .txt files. Finds the longest and the shortest sentence in file.';

    private $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setDescription(self::$defaultDescription)
        ->addArgument('txtPath', InputArgument::REQUIRED, 'Path to .txt file.')
        ;
    }

    public function longest_string_in_array($array) {    
        $mapping = array_combine($array, array_map('strlen', $array));     
        return array_keys($mapping, max($mapping));     
    }
    
    public function shortest_string_in_array($array) {    
        $mapping = array_combine($array, array_map('strlen', $array));     
        return array_keys($mapping, min($mapping));     
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $argPath = $input->getArgument('txtPath');

        $txtPath = $this->projectDir . '/' . $argPath;

        if (file_exists($txtPath)) {
            $fileContents = file_get_contents($txtPath, true);
            $sentences = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $fileContents);

            dd($this->shortest_string_in_array($sentences));

            $io->success($txtPath);
        } else {
            $io->error(sprintf('Could not find %s', $txtPath));
        }
        

        return Command::SUCCESS;
    }
}
