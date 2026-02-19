<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Enums;

enum TenantStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Suspended = 'suspended';
}
