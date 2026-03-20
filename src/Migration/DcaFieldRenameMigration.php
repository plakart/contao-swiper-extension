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

namespace Plakart\ContaoSwiperExtensionBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class DcaFieldRenameMigration extends AbstractMigration
{
    private const TABLE = 'tl_content';

    /**
     * @var array<string, string>
     */
    private const FIELD_RENAMES = [
        'sliderNavigation' => 'swiperSliderNavigation',
        'sliderPagination' => 'swiperSliderPagination',
        'sliderCustomOptions' => 'swiperSliderCustomOptions',
    ];

    /**
     * @var list<string>
     */
    private const BOOLEAN_FIELDS = [
        'sliderNavigation',
        'sliderPagination',
    ];

    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist([self::TABLE])) {
            return false;
        }

        foreach ($this->getExistingFieldRenames() as $oldField => $newField) {
            if (!$this->hasColumn($newField)) {
                return true;
            }

            if (false !== $this->connection->fetchOne($this->buildShouldRunQuery($oldField, $newField))) {
                return true;
            }
        }

        return false;
    }

    public function run(): MigrationResult
    {
        foreach ($this->getExistingFieldRenames() as $oldField => $newField) {
            $this->createNewColumn($oldField, $newField);
            $this->connection->executeStatement($this->buildMigrationQuery($oldField, $newField));
        }

        return $this->createResult(true);
    }

    /**
     * @return array<string, string>
     */
    private function getExistingFieldRenames(): array
    {
        $columns = [];

        foreach ($this->getTableColumns() as $column) {
            $columns[strtolower($column->getName())] = true;
        }

        return array_filter(
            self::FIELD_RENAMES,
            static fn (string $newField, string $oldField): bool => isset($columns[strtolower($oldField)]),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    private function hasColumn(string $fieldName): bool
    {
        foreach ($this->getTableColumns() as $column) {
            if (strtolower($column->getName()) === strtolower($fieldName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Bridge DBAL schema-manager API differences between Contao versions.
     *
     * @return list<object>
     */
    private function getTableColumns(): array
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (method_exists($schemaManager, 'introspectTableColumnsByUnquotedName')) {
            return $schemaManager->introspectTableColumnsByUnquotedName(self::TABLE);
        }

        return $schemaManager->listTableColumns(self::TABLE);
    }

    private function createNewColumn(string $oldField, string $newField): void
    {
        if ($this->hasColumn($newField)) {
            return;
        }

        $table = $this->quoteIdentifier(self::TABLE);
        $new = $this->quoteIdentifier($newField);

        if (\in_array($oldField, self::BOOLEAN_FIELDS, true)) {
            $this->connection->executeStatement("ALTER TABLE $table ADD $new TINYINT(1) DEFAULT 0 NOT NULL");

            return;
        }

        $this->connection->executeStatement("ALTER TABLE $table ADD $new LONGBLOB DEFAULT NULL");
    }

    private function buildShouldRunQuery(string $oldField, string $newField): string
    {
        $table = $this->quoteIdentifier(self::TABLE);
        $old = $this->quoteIdentifier($oldField);
        $oldHasValueCondition = $this->buildOldFieldHasValueCondition($oldField, $old);
        $newIsEmptyCondition = $this->buildNewFieldEmptyCondition($newField);

        return <<<SQL
            SELECT TRUE
            FROM $table
            WHERE $oldHasValueCondition
              AND $newIsEmptyCondition
            LIMIT 1
            SQL;
    }

    private function buildMigrationQuery(string $oldField, string $newField): string
    {
        $table = $this->quoteIdentifier(self::TABLE);
        $old = $this->quoteIdentifier($oldField);
        $new = $this->quoteIdentifier($newField);
        $oldHasValueCondition = $this->buildOldFieldHasValueCondition($oldField, $old);
        $newIsEmptyCondition = $this->buildNewFieldEmptyCondition($newField);

        return <<<SQL
            UPDATE $table
            SET $new = $old
            WHERE $oldHasValueCondition
              AND $newIsEmptyCondition
            SQL;
    }

    private function buildOldFieldHasValueCondition(string $oldField, string $quotedOldField): string
    {
        if (\in_array($oldField, self::BOOLEAN_FIELDS, true)) {
            return "($quotedOldField IS NOT NULL AND $quotedOldField != '' AND $quotedOldField != '0')";
        }

        return "($quotedOldField IS NOT NULL AND $quotedOldField != '')";
    }

    private function buildNewFieldEmptyCondition(string $newField): string
    {
        $new = $this->quoteIdentifier($newField);

        if (\in_array($this->getOldFieldName($newField), self::BOOLEAN_FIELDS, true)) {
            return "($new IS NULL OR $new = '' OR $new = '0')";
        }

        return "($new IS NULL OR $new = '')";
    }

    private function getOldFieldName(string $newField): string
    {
        return array_search($newField, self::FIELD_RENAMES, true) ?: $newField;
    }

    private function quoteIdentifier(string $identifier): string
    {
        if (method_exists($this->connection, 'quoteSingleIdentifier')) {
            return $this->connection->quoteSingleIdentifier($identifier);
        }

        return $this->connection->quoteIdentifier($identifier);
    }
}
