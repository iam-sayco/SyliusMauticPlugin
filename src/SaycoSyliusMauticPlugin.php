<?php

declare(strict_types=1);

namespace Sayco\SyliusMauticPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SaycoSyliusMauticPlugin extends Bundle
{
    use SyliusPluginTrait;
}
