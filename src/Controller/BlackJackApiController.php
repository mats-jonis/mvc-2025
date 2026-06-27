<?php

namespace App\Controller;

use App\Card\CardGraphic;
use App\Card\Deck;
use App\Game\BlackJack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BlackJackApiController extends AbstractController
{
    /**
     * The API landing page listing every route.
     */
    #[Route("/proj/api", name: "proj_api", methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('proj/api.html.twig');
    }

    /**
     * Return a full ordered deck of 52 cards as JSON.
     */
    #[Route("/proj/api/deck", name: "proj_api_deck", methods: ['GET'])]
    public function deck(): JsonResponse
    {
        $deck = $this->buildDeck();
        return $this->json(['deck' => $deck->getString()]);
    }

    /**
     * Return a shuffled deck of 52 cards as JSON.
     */
    #[Route("/proj/api/deck/shuffle", name: "proj_api_deck_shuffle", methods: ['GET'])]
    public function shuffle(): JsonResponse
    {
        $deck = $this->buildDeck();
        $deck->shuffle();
        return $this->json(['deck' => $deck->getString()]);
    }

    /**
     * Return the current game state, or a hint if no game is active.
     */
    #[Route("/proj/api/game", name: "proj_api_game", methods: ['GET'])]
    public function game(SessionInterface $session): JsonResponse
    {
        $game = $session->get('blackjack');
        if (!$game instanceof BlackJack) {
            return $this->json(['message' => 'No active game. Start one at /proj/game.']);
        }
        return $this->json($game->getState());
    }

    /**
     * Deal a new round. Expects bets in the POST body.
     */
    #[Route("/proj/api/game/deal", name: "proj_api_game_deal", methods: ['POST'])]
    public function deal(Request $request, SessionInterface $session): JsonResponse
    {
        $game = $session->get('blackjack');
        if (!$game instanceof BlackJack) {
            return $this->json(['error' => 'No active game. Start one at /proj/game.'], 400);
        }

        $rawBets = (array) $request->request->all('bets');
        $bets = [];
        foreach ($rawBets as $bet) {
            $value = (int) $bet;
            if ($value > 0) {
                $bets[] = $value;
            }
        }

        try {
            $game->deal($bets);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        $session->set('blackjack', $game);
        return $this->json($game->getState());
    }

    /**
     * The current hand draws a card.
     */
    #[Route("/proj/api/game/hit", name: "proj_api_game_hit", methods: ['POST'])]
    public function hit(SessionInterface $session): JsonResponse
    {
        $game = $session->get('blackjack');
        if (!$game instanceof BlackJack) {
            return $this->json(['error' => 'No active game.'], 400);
        }
        $game->hit();
        $session->set('blackjack', $game);
        return $this->json($game->getState());
    }

    /**
     * The current hand stands, play moves on.
     */
    #[Route("/proj/api/game/stand", name: "proj_api_game_stand", methods: ['POST'])]
    public function stand(SessionInterface $session): JsonResponse
    {
        $game = $session->get('blackjack');
        if (!$game instanceof BlackJack) {
            return $this->json(['error' => 'No active game.'], 400);
        }
        $game->stand();
        $session->set('blackjack', $game);
        return $this->json($game->getState());
    }

    /**
     * Build a new ordered 52-card deck.
     */
    private function buildDeck(): Deck
    {
        $deck = new Deck();
        for ($i = 1; $i <= 52; $i++) {
            $card = new CardGraphic();
            $card->setValue($i);
            $deck->add($card);
        }
        return $deck;
    }

}
