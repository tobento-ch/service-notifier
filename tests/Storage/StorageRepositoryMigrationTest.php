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

namespace Tobento\Service\Notifier\Test\Storage;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Storage\StorageRepositoryMigration;
use Tobento\Service\Notifier\Storage\StorageRepository;
use Tobento\Service\Storage\InMemoryStorage;
use Tobento\Service\Repository\Storage\Migration\RepositoryAction;
use Tobento\Service\Repository\Storage\Migration\RepositoryDeleteAction;

class StorageRepositoryMigrationTest extends TestCase
{
    public function testMigrationInstall()
    {
        $repository = new StorageRepository(
            storage: new  InMemoryStorage(items: []),
            table: 'notifications',
        );
        
        $migration = new StorageRepositoryMigration(storageRepository: $repository);
        $actions = $migration->install();
        
        $this->assertInstanceof(RepositoryAction::class, $actions->first());
    }
    
    public function testMigrationUninstall()
    {
        $repository = new StorageRepository(
            storage: new  InMemoryStorage(items: []),
            table: 'notifications',
        );
        
        $migration = new StorageRepositoryMigration(storageRepository: $repository);
        $actions = $migration->uninstall();
        
        $this->assertInstanceof(RepositoryDeleteAction::class, $actions->first());
    }
}