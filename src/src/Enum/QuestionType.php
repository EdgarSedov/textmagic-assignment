<?php

declare(strict_types=1);

namespace App\Enum;

enum QuestionType: string
{
    case INPUT = 'input';
    case SELECT = 'select';
    case MULTI_SELECT = 'multi-select';
}
