<?php

namespace App\Command;

use App\Dto\BookDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Service\BooksUrlGetter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'parse:books',
    description: 'Add a short description for your command',
)]
class ParseBooksCommand extends Command
{
    public function __construct(
        private BooksUrlGetter $booksUrlGetter,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $url = $this->booksUrlGetter->url;

        $json = $this->extractBooksJson($url);
        $bookDtos = $this->deserializeJson($json);

        $allCategories = $this->persistUniqueCategories($bookDtos);

        $this->persistUniqueBooks($bookDtos, $allCategories);


        $io->success('makes');

        return Command::SUCCESS;
    }

    private function persistUniqueCategories($bookDtos) : array
    {
        $categoryTitles = [];
        foreach ($bookDtos as $bookDto) {
            $categoryTitles = array_merge($categoryTitles, $bookDto->getCategories());
        }

        $existedCategories = $this->entityManager->getRepository(Category::class)->findByTitle($categoryTitles[0]);
        $existedTitles = array_map(fn ($e) => $e->getTitle(), $existedCategories);

        $newTitles = array_filter($categoryTitles, fn ($e) => !in_array($e, $existedTitles));
        $newTitles = array_unique($newTitles);

        $newCategories = [];
        foreach ($newTitles as $title) {
            $category = new Category();
            $category->setTitle($title);
            array_push($newCategories, $category);
            $this->entityManager->persist($category);
        }
        $this->entityManager->flush();

        $allCategories = array_merge($existedCategories, $newCategories);
        return $allCategories;
    }

    private function persistUniqueBooks($bookDtos, $allCategories)
    {
        // Filter the new book DTOs based on the ISBNs extracted from an array of books and existed ISBNs in database.
        $isbns = array_map(fn ($b) => $b->getIsbn(), $bookDtos);
        $existedBooks = $this->entityManager->getRepository(Book::class)->findByIsbn($isbns);
        $existedIsbns = array_map(fn ($e) => $e->getIsbn(), $existedBooks);
        $newBookDtos = array_filter($bookDtos, fn ($e) => !in_array($e->getIsbn(), $existedIsbns));

        // If book hasn't category, add 'Новинки'.
        $checkCategory = function ($bookDto) {
            if (count($bookDto->getCategories()) == 0)
                $bookDto->setCategories(['Новинки']);
            return $bookDto;
        };
        $newBookDtos = array_map($checkCategory, $newBookDtos);

        foreach ($newBookDtos as $dto) {
            
            foreach( $dto->getCategories() as $categoryTitle)
            {
                $bookCategories = array_filter($allCategories, fn ($e) => $e -> getTitle() == $categoryTitle);
            }

            $book = $dto->toEntity();
            $this->entityManager->persist($book);
        }

        $this->entityManager->flush();
    }

    private function deserializeJson($json): array
    {
        $serializer = new Serializer([new ObjectNormalizer(), new ArrayDenormalizer()], [new JsonEncoder()]);
        $books = $serializer->deserialize($json, 'App\Dto\BookDto[]', "json");
        return $books;
    }

    private function extractBooksJson($url): string
    {
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json",
                'method'  => 'GET',
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}
