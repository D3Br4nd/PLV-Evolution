<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Direzione = 'direzione';
    case Segreteria = 'segreteria';
    case Member = 'member';
}


