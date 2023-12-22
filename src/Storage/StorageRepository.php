<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Notifier\Storage;

use Tobento\Service\Repository\Storage\StorageRepository as DefaultStorageRepository;
use Tobento\Service\Repository\Storage\Column\ColumnsInterface;
use Tobento\Service\Repository\Storage\Column\ColumnInterface;
use Tobento\Service\Repository\Storage\Column;

class StorageRepository extends DefaultStorageRepository
{
    /**
     * Returns the configured columns.
     *
     * @return iterable<ColumnInterface>|ColumnsInterface
     */
    protected function configureColumns(): iterable|ColumnsInterface
    {
        return [
            Column\Text::new('name')->type(length: 255),
            Column\Text::new('recipient_id')->type(length: 36),
            Column\Text::new('recipient_type')->type(length: 255),
            Column\Json::new('data'),
            Column\Datetime::new('read_at')->type(nullable: true),
            Column\Datetime::new('created_at')->type(nullable: true),
        ];
    }
}