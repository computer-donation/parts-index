<?php

namespace App\Enum;

enum ComputerType: string
{
    case ALL_IN_ONE = 'All In One';
    case CONVERTIBLE = 'Convertible';
    case DESKTOP = 'Desktop';
    case MINI_PC = 'Mini Pc';
    case NOTEBOOK = 'Notebook';
    case SERVER = 'Server';
    case STICK_PC = 'Stick Pc';
}
