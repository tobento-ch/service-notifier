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

namespace Tobento\Service\Notifier\Browser;

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
            new Column\Id(),
            new Column\Text('name')->type(length: 255),
            new Column\Text('recipient_id')->type(length: 36, nullable: true),
            new Column\Text('recipient_type')->type(length: 255),
            new Column\Json('data'),
            new Column\Datetime('expires_at')->type(nullable: true),
            new Column\Datetime('read_at')->type(nullable: true),
            new Column\Datetime('created_at')->type(nullable: true),
        ];
    }
}