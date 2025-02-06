<?php

namespace App\Enum;

enum Energie: string {
    case ESSENCE = "Essence";
    case DIESEL = "Diesel";
    case ELECTRIQUE = "Electrique";
    case HYBRIDE = "Hybride";
}