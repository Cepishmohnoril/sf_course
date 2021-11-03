<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(MailService $mail)
    {
        $this->mail = $mail;
    }

    /**
     * @Route("/add/{name}", name="add")
     */
    public function addUsersCtl(string $name): Response
    {
        $user1 = new User();
        $user1->setName($name);
        $this->getDoctrine()->getManager()->persist($user1);
        $this->getDoctrine()->getManager()->flush();

         return $this->redirectToRoute('tpl');
    }

    /**
     * @Route("/tpl", name="tpl")
     */
    public function tplCtl(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        foreach($users as $key => $user) {
            $canSendData[$key] = $this->mail->canSend();
        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => $users,
            'can_send' => $canSendData,
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
