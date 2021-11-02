<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/tpl", name="tpl")
     */
    public function tplCtl(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'list' => ['Foo', 'Bar', 'Hello Todd'],
        ]);
    }

    /**
     * @Route("/json", name="json")
     */
    public function jsonCtl(): Response
    {
        return $this->json(['hello' => 'world']);
    }

    /**
     * @Route("/param/{name}", name="param")
     */
    public function paramCtl($name): Response
    {
        return new Response("Hello $name");
    }

    /**
     * @Route("/redirect", name="redirect")
     */
    public function redirectCtl(): Response
    {
        return $this->redirectToRoute('target');
    }

        /**
     * @Route("/target", name="target")
     */
    public function targetCtl(): Response
    {
        return new Response("Hello form redirect!");
    }
}
