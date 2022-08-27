<?php

namespace App\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

trait FileTrait
{
    protected function browseFiles(string|array $dirs, OutputInterface $output, callable $fileCallback): void
    {
        $finder = new Finder();
        $finder->files()->in($dirs)->notName('README.md');
        $last = $finder->count();
        $current = 1;
        $progressBar = new ProgressBar($output, $last);
        foreach ($finder as $file) {
            $progressBar->advance();
            $flush = !($current % 100) || $current === $last;
            call_user_func($fileCallback, $file, $flush);
            ++$current;
        }
        $progressBar->finish();
    }
}
