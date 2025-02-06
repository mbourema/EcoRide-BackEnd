<?php

namespace App\Enum;

enum Avancement: string {
    case EFFECTUE = "Effectué";
    case ANNULE = "Annulé";
    case ATTENTE = "En attente";
}