<?php

namespace App\Controller;

use App\Game\Game21;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class Game21Controller extends AbstractController
{
    #[Route("/game", name: "game_landing")]
    public function landing(): Response
    {
        return $this->render('game/home.html.twig');
    }

    #[Route("/game/doc", name: "game_doc")]
    public function doc(): Response
    {
        return $this->render('game/doc.html.twig');
    }

    #[Route("/game/init", name: "game_init", methods: ['POST'])]
    public function init(SessionInterface $session): Response
    {
        $session->set("game21", new Game21());
        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play", name: "game_play", methods: ['GET'])]
    public function play(SessionInterface $session): Response
    {
        $game = $session->get("game21");
        if (!$game instanceof Game21) {
            return $this->render('game/home.html.twig');
        }
        return $this->render('game/play.html.twig', [
            'playerHand'  => $game->getPlayerHandStrings(),
            'bankHand'    => $game->getBankHandStrings(),
            'playerValue' => $game->getPlayerValue(),
            'bankValue'   => $game->getBankValue(),
            'status'      => $game->getStatus(),
            'cardsLeft'   => $game->getCardsLeft(),
        ]);
    }

    #[Route("/game/draw", name: "game_draw", methods: ['POST'])]
    public function draw(SessionInterface $session): Response
    {
        $game = $session->get("game21");

        if (!$game instanceof Game21) {
            return $this->redirectToRoute('game_landing');
        }
        $game->playerDraw();
        $session->set("game21", $game);
        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/stand", name: "game_stand", methods: ['POST'])]
    public function stand(SessionInterface $session): Response
    {
        $game = $session->get("game21");
        if (!$game instanceof Game21) {
            return $this->redirectToRoute('game_landing');
        }
        $game->stand();
        $session->set("game21", $game);
        return $this->redirectToRoute('game_play');
    }
}
