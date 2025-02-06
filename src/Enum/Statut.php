<?php

namespace App\Enum;

enum Statut: string {
    case EN_ATTENTE = "En attente";
    case ANNULE = "Annulé";
    case EN_COURS = "En cours";
    case Termine = "Terminé";
}