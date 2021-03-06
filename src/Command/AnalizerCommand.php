<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

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
        $logPath = $this->projectDir . '/var/log/';

        if (file_exists($txtPath)) {
            $fileContents = file_get_contents($txtPath, true);
            $sentences = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $fileContents);

            $longestSentenceStr = implode("", $this->longest_string_in_array($sentences));
            $shortestSentenceStr = implode("", $this->shortest_string_in_array($sentences));
            
            $longestSentenceLen = str_word_count($longestSentenceStr);
            $shortestSentenceLen = str_word_count($shortestSentenceStr);

            $filesystem = new Filesystem;
            $filesystem->dumpFile($logPath . 'output.txt', sprintf("Longest: %d words. Sentence: %s\nShortest: %d words. Sentence: %s", $longestSentenceLen, $longestSentenceStr, $shortestSentenceLen, $shortestSentenceStr));

            $io->success(sprintf("Longest: %d words. Sentence: %s\n\nShortest: %d words. Sentence: %s", $longestSentenceLen, $longestSentenceStr, $shortestSentenceLen, $shortestSentenceStr));
        } else {
            $io->error(sprintf('Could not find %s :(. Try again.', $txtPath));
        }

        return Command::SUCCESS;
    }
}
