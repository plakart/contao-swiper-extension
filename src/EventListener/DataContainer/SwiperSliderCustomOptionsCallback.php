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

namespace Plakart\ContaoSwiperExtensionBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\StringUtil;
class SwiperSliderCustomOptionsCallback
{
    #[AsCallback(table: 'tl_content', target: 'fields.swiperSliderCustomOptions.save')]
    public function onSave($value, DataContainer $dc)
    {
        $value = $this->normalizeJsonString($value);

        if ($value === '') {
            return '';
        }

        $decoded = json_decode($value, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException($GLOBALS['TL_LANG']['ERR']['invalidJsonData']);
        }

        return serialize($decoded);
    }

    #[AsCallback(table: 'tl_content', target: 'fields.swiperSliderCustomOptions.load')]
    public function onLoad($value, DataContainer $dc)
    {
        $decoded = $this->decodeValue($value);

        if ($decoded === []) {
            return '';
        }

        return (string) json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    private function decodeValue($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $deserialized = StringUtil::deserialize($value, true);

        if (\is_array($deserialized)) {
            return $deserialized;
        }

        $normalizedValue = $this->normalizeJsonString($value);

        if ($normalizedValue === '') {
            return [];
        }

        $decoded = json_decode($normalizedValue, true);

        if (JSON_ERROR_NONE !== json_last_error() || !\is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    private function normalizeJsonString($value): string
    {
        return trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5));
    }
}
