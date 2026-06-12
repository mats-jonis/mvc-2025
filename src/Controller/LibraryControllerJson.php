<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * JSON API for the library.
 */
#[Route('/api/library')]
class LibraryControllerJson extends AbstractController
{
    /**
     * Return all books as JSON.
     */
    #[Route('/books', name: 'api_library_books', methods: ['GET'])]
    public function books(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        $response = $this->json($books);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
    * Return a single book by its ISBN as JSON.
    */
    #[Route('/book/{isbn}', name: 'api_library_book', methods: ['GET'])]
    public function book(string $isbn, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->findOneBy(['isbn' => $isbn]);

        if (!$book) {
            return $this->json(['error' => 'No book found for ISBN ' . $isbn], 404);
        }

        $response = $this->json($book);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }
}
