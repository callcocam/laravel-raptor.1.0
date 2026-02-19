<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Enums;

enum PermissionStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
}
