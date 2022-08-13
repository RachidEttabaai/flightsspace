<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\MixRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends AbstractController
{
    #[Route('/news', name: "newspage")]
    public function news(MixRepository $mixRepository): Response
    {
        $mixRepository->setUrl("https://api.spaceflightnewsapi.net/v3/articles?_limit=10");
        $news = $mixRepository->apirequest();
        //dd($news);
        return $this->render("news/news.html.twig", compact("news"));
    }
}
