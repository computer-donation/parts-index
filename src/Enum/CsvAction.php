<?php

namespace App\Enum;

enum CsvAction: string
{
    public const APPEND = 'append';
    public const OVERWRITE = 'overwrite';
}
