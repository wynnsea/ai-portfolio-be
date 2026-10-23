<?php

namespace App\Enums;

enum MessageStatus: string
{
    case New = 'Baru';
    case Read = 'Dibaca';
    case Replied = 'Dibalas';
}
