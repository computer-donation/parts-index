<?php

namespace App\Command;

use App\Enum\CsvAction;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

trait CsvTrait
{
    protected function checkCsv(string $path, array $expectedHeader, InputInterface $input, OutputInterface $output): void
    {
        $output->writeln(sprintf('<comment>Checking csv file %s...</comment>', $path));
        $action = CsvAction::OVERWRITE;
        if (file_exists($path)) {
            $action = $this->askQuestion($path, $input, $output);
        } else {
            $output->writeln(sprintf('<info>File %s does not exist. Creating new file...</info>', $path));
        }

        if (CsvAction::OVERWRITE === $action) {
            fputcsv($file = fopen($path, 'w'), $expectedHeader);
            fclose($file);
        } else {
            $this->checkHeader($path, $expectedHeader, $output);
        }
    }

    protected function askQuestion(string $path, InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            sprintf('<question>The file %s already exists. Please choose action (defaults to append)</question>', $path),
            [CsvAction::APPEND, CsvAction::OVERWRITE],
            0
        );
        $question->setErrorMessage('Action %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    protected function checkHeader(string $path, array $expectedHeader, OutputInterface $output): void
    {
        $header = fgetcsv($file = fopen($path, 'r'));
        fclose($file);
        if ($expectedHeader === $header) {
            $output->writeln(sprintf('<info>Header of csv file %s is expected</info>', $path));
        } else {
            throw new \Exception(sprintf('Header of csv file %s expected to be "%s", actual "%s".', $path, implode(',', $expectedHeader), implode(',', $header)));
        }
    }
}
