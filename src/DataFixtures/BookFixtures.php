<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $books = [
            ['Clean Code', '9780132350884', 'Robert C. Martin', 'cleancode.jpg'],
            ['Design Patterns', '9780201633610', 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides', 'design_patterns.jpg'],
            ['Refactoring', '9780134757599', 'Martin Fowler', 'refactoring.jpg'],
        ];
        foreach ($books as [$title, $isbn, $author, $img]) {
            $book = (new Book())
                ->setTitle($title)
                ->setIsbn($isbn)
                ->setAuthor($author)
                ->setImg($img);
            $manager->persist($book);
        }
        $manager->flush();
    }
}
