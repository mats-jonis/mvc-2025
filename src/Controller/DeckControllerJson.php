<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\CardGraphic;
use App\Card\Deck;
use App\Game\Game21;
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
            'api/deck/draw/{num<\d+>}' => 'Draws one or serveral cards from the deck',
            'api/game' => 'Shows the current state of the 21 game in JSON',
            ];

        $data = [
            'All JSON routes on the site:' => $routes,
        ];

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

        $data = [
            'All cards in the deck:' => $deck->getString(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;

    }

    #[Route("api/deck/shuffle")]
    public function shuffleDeck(
        SessionInterface $session
    ): Response {
        $deck = new Deck();
        for ($i = 1; $i <= 52; $i++) {
            $card = new CardGraphic();
            $card->setValue($i);
            $deck->add($card);
        }

        $deck->shuffle();

        $session->set("deck", $deck);

        $data = [
        'All shuffled cards in the deck:' => $deck->getString(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;

    }

    #[Route("api/deck/draw", methods: ['POST', 'GET'])]
    public function drawCard(
        SessionInterface $session
    ): Response {
        $drawnCards = new Deck();
        $sessionDeck = $session->get("deck");
        $deck = $sessionDeck instanceof Deck ? $sessionDeck : new Deck();
        $message = 'You have no cards to draw! Shuffle deck to start playing';

        if ($sessionDeck instanceof Deck) {
            $card = $deck->draw();
            $message = $card === null ? 'You have no cards to draw! Shuffle deck to start playing' : '';
            if ($card !== null) {
                $drawnCards->add($card);
            }
            $session->set("deck", $deck);
        }
        $data = [
            "message" => $message,
            "drawnCards" => $drawnCards->getString(),
            "numCards" => $deck->getNumberCards(),
            "cardValues" => $deck->getString(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }

    #[Route("api/deck/draw/{num<\d+>}", methods: ['POST', 'GET'])]
    public function drawCards(
        int $num,
        SessionInterface $session
    ): Response {

        $drawnCards = new Deck();
        $sessionDeck = $session->get("deck");
        $deck = $sessionDeck instanceof Deck ? $sessionDeck : new Deck();
        $message = 'You have no cards to draw! Shuffle deck to start playing';

        if ($sessionDeck instanceof Deck) {
            $message = '';
            for ($i = 1; $i <= $num; $i++) {
                $card = $deck->draw();
                if ($card === null) {
                    $message = 'You have no cards to draw! Shuffle deck to start playing';
                    break;
                }
                $drawnCards->add($card);
            }
            $session->set("deck", $deck);
        }

        $data = [
            "message" => $message,
            "drawnCards" => $drawnCards->getString(),
            "numCards" => $deck->getNumberCards(),
            "cardValues" => $deck->getString(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }

    #[Route("/api/game", name: "api_game", methods: ['GET'])]
    public function apiGame(SessionInterface $session): Response
    {

        $game = $session->get("game21");

        $data = ['message' => 'No active game. Start one at /game.'];
        if ($game instanceof Game21) {
            $data = [
                   'playerHand'  => $game->getPlayerHandStrings(),
                   'playerValue' => $game->getPlayerValue(),
                   'bankHand'    => $game->getBankHandStrings(),
                   'bankValue'   => $game->getBankValue(),
                   'cardsLeft'   => $game->getCardsLeft(),
                   'status'      => $game->getStatus(),
               ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }

}
