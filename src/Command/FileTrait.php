<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

trait FileTrait
{
    protected function getDirs(SplFileInfo $file, OutputInterface $output, string $expectedPattern): array
    {
        $dirs = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
        if (count(explode(DIRECTORY_SEPARATOR, $expectedPattern)) - 1 !== count($dirs)) {
            $output->writeln(sprintf('<error>Invalid file path %s. Expected pattern %s</error>', $file->getPathname(), $expectedPattern));

            return [];
        }

        return $dirs;
    }
}
