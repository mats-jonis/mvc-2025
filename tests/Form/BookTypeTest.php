<?php

namespace App\Tests\Form;

use App\Entity\Book;
use App\Form\BookType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Unit tests for the BookType form.
 */
class BookTypeTest extends TypeTestCase
{
    /**
     Valid data must map to a Book object, verifying both the form fields and the data_class configuration.
     */
    public function testSubmitValidData(): void
    {
        $formData = [
            'title' => 'Clean Code',
            'isbn' => '9780132350884',
            'author' => 'Robert C. Martin',
            'img' => 'cover.jpg',
        ];

        $model = new Book();
        $form = $this->factory->create(BookType::class, $model);

        $expected = new Book();
        $expected->setTitle('Clean Code');
        $expected->setIsbn('9780132350884');
        $expected->setAuthor('Robert C. Martin');
        $expected->setImg('cover.jpg');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $model);
    }
    /**
     * The form should expose exactly the four book fields.
     */
    public function testFormHasExpectedFields(): void
    {
        $form = $this->factory->create(BookType::class);

        $this->assertTrue($form->has('title'));
        $this->assertTrue($form->has('isbn'));
        $this->assertTrue($form->has('author'));
        $this->assertTrue($form->has('img'));
    }

}
