<?php

declare(strict_types=1);

/*
 * This file is part of ContaoSwiperExtensionBundle.
 *
 * (c) plakart GmbH & Co. KG (https://plakart.de)
 * author Jannik Nölke (https://jaynoe.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plakart\ContaoSwiperExtensionBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\CoreBundle\ContaoCoreBundle;
use Plakart\ContaoSwiperExtensionBundle\ContaoSwiperExtensionBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoSwiperExtensionBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}