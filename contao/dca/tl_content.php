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

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addField('swiperSliderNavigation', 'sliderContinuous')
    ->addField('swiperSliderPagination', 'swiperSliderNavigation')
    ->addField('swiperSliderCustomOptions', 'swiperSliderPagination')
    ->applyToPalette('swiper', 'tl_content')
;

$GLOBALS['TL_DCA']['tl_content']['fields']['swiperSliderNavigation'] =
[
    'inputType'               => 'checkbox',
    'eval'                    => ['tl_class'=>'w25'],
    'sql'                     => ['type' => 'boolean', 'default' => false]
];

$GLOBALS['TL_DCA']['tl_content']['fields']['swiperSliderPagination'] =
[
    'inputType'               => 'checkbox',
    'eval'                    => ['tl_class'=>'w25'],
    'sql'                     => ['type' => 'boolean', 'default' => false]
];

$GLOBALS['TL_DCA']['tl_content']['fields']['swiperSliderCustomOptions'] =
[
    'inputType'               => 'textarea',
    'eval'                    => ['tl_class'=>'clr', 'rte' => 'ace|json'],
    'sql'                     => ['type' => 'blob', 'notnull' => false]
];
