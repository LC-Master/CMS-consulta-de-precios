<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case DRAFT = 'Borrador';
    case ACTIVE = 'Activa';
    case FINISHED = 'Finalizada';
    case CANCELLED = 'Cancelada';
}
