<?php

namespace App\Enums;

enum ContentStatus: string
{
    case Draft = 'Draft';
    case Published = 'Terbit';
    case Archived = 'Arsip';
}
