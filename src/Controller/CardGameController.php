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

class CardGameController extends AbstractController
{
    #[Route("/game/card", name: "card_start")]
    public function home(): Response
    {
        return $this->render('card/home.html.twig');
    }


    #[Route("/game/card/deck", name: "card_deck", methods: ['GET', 'POST'])]
    public function deck(
        SessionInterface $session
    ): Response {
        $deck = new Deck();
        for ($i = 1; $i <= 52; $i++) {
            $card = new CardGraphic();
            $card->setValue($i);
            $deck->add($card);
        }

        $session->set("deck", $deck);

        return $this->redirectToRoute('card_play');
    }

    #[Route("/game/card/shuffledeck", name: "shuffle_deck", methods: ['GET', 'POST'])]
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

        return $this->redirectToRoute('card_play');
    }

    #[Route("/session/delete", name: "session_delete", methods: ['GET', 'POST'])]
    public function sessionDelete(
        SessionInterface $session
    ): Response {

        $session->invalidate();

        $this->addFlash(
            'warning',
            'Session deleted'
        );

        return $this->render('card/home.html.twig');

    }

    #[Route("/game/card/draw", name: "draw_card", methods: ['GET', 'POST'])]
    public function drawCard(
        SessionInterface $session
    ): Response {
        $deck = $session->get("deck");
        $drawnCards = new Deck();

        if (!$session->has("deck")) {
            $this->addFlash(
                'warning',
                'You have no cards the draw! Shuffle deck to start playing',
            );

            $deck = new Deck();
        } else {

            // draws a card and remove the last card from the deck.
            $card = $deck->draw();

            if ($card != null) {
                $drawnCards->add($card);
            } else {
                $this->addFlash(
                    'warning',
                    'You have no cards the draw! Shuffle deck to start playing',
                );
            }

            $session->set("deck", $deck);

        }

        $numCards = $deck->getNumberCards();

        $data = [
            "cardValues" => $deck->getString(),
            "drawnCards" => $drawnCards->getString(),
            "numCards" => $numCards,
        ];


        return $this->render('card/draw.html.twig', $data);
    }



    #[Route("/game/card/draw/{num<\d+>}", name: "draw_num_cards", methods: ['GET', 'POST'])]
    public function drawCards(
        int $num,
        SessionInterface $session
    ): Response {

        $deck = $session->get("deck");
        $drawnCards = new Deck();
        $numCards = $deck->getNumberCards() ?? 0;

        if (!$session->has("deck")) {
            $this->addFlash(
                'warning',
                'You have no cards the draw! Shuffle deck to start playing',
            );

            $deck = new Deck();
        } else {

            for ($i = 1; $i <= $num; $i++) {
                if ($numCards > 0) {
                    $card = $deck->draw();
                    if ($card !== null) {
                        $drawnCards->add($card);
                        $numCards--;
                    }
                } else {
                    $this->addFlash(
                        'warning',
                        'You have no cards the draw! Shuffle deck to start playing',
                    );
                    break;
                }
            }

            $session->set("deck", $deck);

        }

        $data = [
            "cardValues" => $deck->getString(),
            "drawnCards" => $drawnCards->getString(),
            "numCards" => $numCards,
        ];

        return $this->render('card/draw.html.twig', $data);
    }

    #[Route("/game/card/play", name: "card_play", methods: ['GET'])]
    public function play(
        SessionInterface $session
    ): Response {
        $numcards = $session->get("num_cards");
        $deck = $session->get("deck");

        $data = [
            "cardValues" => $deck->getString(),
            "numCards" => $numcards
        ];

        return $this->render('card/play.html.twig', $data);
    }
}
