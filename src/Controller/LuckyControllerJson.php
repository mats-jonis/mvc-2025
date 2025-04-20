<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyControllerJson
{
    #[Route("/api/lucky/number")]
    public function jsonNumber(): Response
    {
        $number = random_int(0, 100);

        $data = [
            'lucky-number' => $number,
            'lucky-message' => 'Hi there!',
        ];

        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
        
    }

   
    #[Route("/api/quote")]
    public function quotes(): Response
    {
        $number = random_int(0, 2);

        $quotes = array(
            'Carpe diem. Seize the day, boys',
             'May the Force be with you',
             'There is no place like home'
            );

        date_default_timezone_set('Europe/Stockholm');
        $timeStamp = date("Y-m-d H:i:s",time());
           
        $data = [
            'Quote of the day:' => $quotes[$number],
            'Date:' => $timeStamp,
        ];

        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
        
    }

    #[Route("/api/")]
    public function allRoutes(): Response
    {
        
        $routes = array(
            '/lucky/number',
            '/lucky/hi',
            '/api/lucky/number',
            '/api/quote',
            '/api/',
            '/lucky/number/twig',
            '/',
            '/home',
            '/about',
            '/report'
            );

        $allRoutes = '';
        foreach ($routes as $route) {
        $allRoutes .= $route . "\n";
        }

        $data = [
            'All routes on the site:' => $routes,
        ];
        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
        
    }
}


