<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the Book entity lik getters, setters and fluent returns.
 */
class BookTest extends TestCase
{
    /**
     * A new book has no id until it is persisted.
     */
    public function testNewBookHasNullId(): void
    {
        $book = new Book();
        $this->assertNull($book->getId());
    }

    public function testSetAndGetTitle(): void
    {
        $book = new Book();
        $book->setTitle('Clean Code');
        $this->assertSame('Clean Code', $book->getTitle());
    }

    public function testSetAndGetIsbn(): void
    {
        $book = new Book();
        $book->setIsbn('9780132350884');
        $this->assertSame('9780132350884', $book->getIsbn());
    }

    public function testSetAndGetAuthor(): void
    {
        $book = new Book();
        $book->setAuthor('Robert C. Martin');
        $this->assertSame('Robert C. Martin', $book->getAuthor());
    }

    public function testSetAndGetImg(): void
    {
        $book = new Book();
        $book->setImg('cover.jpg');
        $this->assertSame('cover.jpg', $book->getImg());
    }

    public function testImgCanBeNull(): void
    {
        $book = new Book();
        $book->setImg(null);
        $this->assertNull($book->getImg());
    }

    public function testSettersAreFluent(): void
    {
        $book = new Book();

        $result = $book
            ->setTitle('A')
            ->setIsbn('B')
            ->setAuthor('C')
            ->setImg('D');

        $this->assertSame($book, $result);
    }

}
