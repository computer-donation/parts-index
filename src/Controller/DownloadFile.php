<?php

namespace App\Controller;

use App\Resource\File;
use App\Service\CsvExport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class DownloadFile extends AbstractController
{
    public function __construct(
        protected CsvExport $csvExport
    ) {
    }

    #[Route()]
    public function __invoke(File $data): BinaryFileResponse
    {
        return $this->file($this->csvExport->getCsvPath($data->name.'.csv'));
    }
}
