<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\CardGraphic;
use App\Card\Deck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpFoundation\JsonResponse;

class DeckControllerJson
{

    
    #[Route("/api/")]
    public function allRoutes(): Response
    {
        
        $routes = [
            'api/' => 'Shows all JSON routes',
            'api/lucky/number' => 'Draw a random number in Json format',
            'api/quote' => 'Get a famous quote',
            'api/deck' => 'Deck of cards, sorted in color and order',
            'api/deck/shuffle' => 'Shuffled deck of cards',
            'api/deck/draw' => 'Draws a card from the deck',
            'api/deck/draw/{num<\d+>}' => 'Draws one or serveral cards from the deck'
            ];

        $allRoutes = '';
        foreach ($routes as $route) {
        $allRoutes .= $route . "\n";
        }

        $data = [
            'All JSON routes on the site:' => $routes,
        ];
        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
        
    }
    #[Route("api/deck")]
    public function deck(): Response
    {
        
        $deck = new Deck();
        for ($i = 1; $i <= 52; $i++) {
            $card = new CardGraphic();
            $card->setValue($i);
            $deck->add($card);
        }

        $allRoutes = '';
        foreach ($deck as $route) {
        $allRoutes .= $route . "\n";
        }

        $data = [
            'All cards in the deck:' => $deck->getString(),
        ];

        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
        
    }

    #[Route("api/deck/shuffle")]
    public function shuffleDeck(
        SessionInterface $session
    ): Response
    {
         $deck = new Deck();
        for ($i = 1; $i <= 52; $i++) {
            $card = new CardGraphic();
            $card->setValue($i);
            $deck->add($card);
        }

        $deck->shuffle();

        $session->set("deck", $deck);

        $allRoutes = '';
        foreach ($deck as $route) {
        $allRoutes .= $route . "\n";
        }

        $data = [
            'All shuffled cards in the deck:' => $deck->getString(),
        ];

        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
        
    }

    #[Route("api/deck/draw", methods: ['POST', 'GET'])]
    public function drawCard(
        SessionInterface $session
    ): Response
    {
            $deck = $session->get("deck");
            $drawnCards = new Deck();
            $message = '';

            if (!$session->has("deck")) {

                $message = 'You have no cards the draw! Shuffle deck to start playing'; 
                $deck = new Deck();
            }

            else {

            // draws a card and remove the last card from the deck.
            $card = $deck->draw();
            
            if ($card != null) {
                $drawnCards->add($card);
            }
            
            else {
                $message = 'You have no cards the draw! Shuffle deck to start playing'; 
            }
            
            $session->set("deck", $deck);

            }

            $numCards = $deck->getNumberCards();

            $data = [
                "message" => $message,
                "drawnCards" => $drawnCards->getString(),
                "numCards" => $numCards,
                "cardValues" => $deck->getString(),
            ];

        

        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
        
    }

    #[Route("api/deck/draw/{num<\d+>}", methods: ['POST', 'GET'])]
    public function drawCards(int $num,
        SessionInterface $session
    ): Response
    {
          
        $deck = $session->get("deck");
        $drawnCards = new Deck();
        $numCards = $deck->getNumberCards() ?? 0;
        $message = '';

        if (!$session->has("card_deck")) {
            $message = 'You have no cards the draw! Shuffle deck to start playing'; 

            $deck = new Deck();
        }

        else {

        for ($i = 1; $i <= $num; $i++) {
            if ($numCards > 0 ) {
                $card = $deck->draw();
                if ($card !== null) {
                    $drawnCards->add($card);
                    $numCards--;
                }
            }
            else {
                $message = 'You have no cards the draw! Shuffle deck to start playing';               
                break;
            }
        }

        $session->set("deck", $deck);

        }

        $data = [
            "message" => $message,
            "drawnCards" => $drawnCards->getString(),
            "numCards" => $numCards,
            "cardValues" => $deck->getString(),
        ];

        

        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
        
    }

}
