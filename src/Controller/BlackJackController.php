<?php

namespace App\Controller;

use App\Game\BlackJack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BlackJackController extends AbstractController
{
    #[Route("/proj/game", name: "proj_game", methods: ['GET'])]
    public function start(): Response
    {
        return $this->render('proj/game/start.html.twig');
    }

    #[Route("/proj/game/init", name: "proj_game_init", methods: ['POST'])]
    public function init(Request $request, SessionInterface $session): Response
    {
        $name = trim((string) $request->request->get('playerName', ''));
        $money = (int) $request->request->get('startMoney', 1000);

        if ($name === '') {
            $this->addFlash('warning', 'Du måste ange ett spelarnamn.');
            return $this->redirectToRoute('proj_game');
        }
        if ($money < 10) {
            $this->addFlash('warning', 'Startsaldot måste vara minst 10.');
            return $this->redirectToRoute('proj_game');
        }

        $session->set('blackjack', new BlackJack($name, $money));
        return $this->redirectToRoute('proj_game_play');
    }

    #[Route("/proj/game/play", name: "proj_game_play", methods: ['GET'])]
    public function play(SessionInterface $session): Response
    {
        $game = $session->get('blackjack');
        if (!$game instanceof BlackJack) {
            return $this->redirectToRoute('proj_game');
        }
        return $this->render('proj/game/play.html.twig', [
            'state' => $game->getState(),
        ]);
    }

    #[Route("/proj/game/deal", name: "proj_game_deal", methods: ['POST'])]
    public function deal(Request $request, SessionInterface $session): Response
    {
        $game = $session->get('blackjack');
        if (!$game instanceof BlackJack) {
            return $this->redirectToRoute('proj_game');
        }

        /** @var array<int, string> $rawBets */
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
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('proj_game_play');
        }

        $session->set('blackjack', $game);
        return $this->redirectToRoute('proj_game_play');
    }

    #[Route("/proj/game/hit", name: "proj_game_hit", methods: ['POST'])]
    public function hit(SessionInterface $session): Response
    {
        $game = $session->get('blackjack');
        if (!$game instanceof BlackJack) {
            return $this->redirectToRoute('proj_game');
        }
        $game->hit();
        $session->set('blackjack', $game);
        return $this->redirectToRoute('proj_game_play');
    }

    #[Route("/proj/game/stand", name: "proj_game_stand", methods: ['POST'])]
    public function stand(SessionInterface $session): Response
    {
        $game = $session->get('blackjack');
        if (!$game instanceof BlackJack) {
            return $this->redirectToRoute('proj_game');
        }
        $game->stand();
        $session->set('blackjack', $game);
        return $this->redirectToRoute('proj_game_play');
    }

    #[Route("/proj/game/newround", name: "proj_game_newround", methods: ['POST'])]
    public function newRound(SessionInterface $session): Response
    {
        $game = $session->get('blackjack');
        if (!$game instanceof BlackJack) {
            return $this->redirectToRoute('proj_game');
        }
        $game->newRound();
        $session->set('blackjack', $game);
        return $this->redirectToRoute('proj_game_play');
    }
}
