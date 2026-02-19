<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Enums\Menu;

enum ContextEnum: string
{
    case Landlord = 'landlord';
    case Tenant = 'tenant';
    case Both = 'both';
}
