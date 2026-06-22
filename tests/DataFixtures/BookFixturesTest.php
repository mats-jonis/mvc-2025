<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\BookFixtures;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for BookFixtures.

 * Uses a mocked ObjectManager to avoid database calls.
 * It simply verifies that load() persists at least one book and flushes the changes once.
 */
class BookFixturesTest extends TestCase
{
    public function testLoadPersistsBooksAndFlushes(): void
    {
        $manager = $this->createMock(ObjectManager::class);

        $manager->expects($this->atLeastOnce())
            ->method('persist');

        $manager->expects($this->once())
            ->method('flush');

        $fixtures = new BookFixtures();
        $fixtures->load($manager);
    }
}
