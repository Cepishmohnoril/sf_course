<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(MailService $mail, RequestStack $requestStack)
    {
        $this->mail = $mail;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/add/{name}", name="add")
     */
    public function addUsers(string $name): Response
    {
        $user1 = new User();
        $user1->setName($name);
        $this->getDoctrine()->getManager()->persist($user1);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('notice', 'User saved!');

         return $this->redirectToRoute('tpl');
    }

    /**
     * @Route("/tpl", name="tpl")
     */
    public function tpl(): Response
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
    public function _json(): Response
    {
        return $this->json(['hello' => 'world']);
    }

    /**
     * @Route("/param/{name}", name="param")
     */
    public function param($name): Response
    {
        return new Response("Hello $name");
    }

    /**
     * @Route("/redirect", name="redirect")
     */
    public function _redirect(): Response
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

    /**
     * @Route("/adv_route/{param?}", name="adv_route", requirements={"param"="\d+"})
     */
    public function advRoute($param): Response
    {
        return new Response("Controller is reached and param is correct.");
    }

    /**
     * @Route("/adv_route2/{param1}/{param2}/{param3}",
     * name="adv_route2",
     * defaults={"param3": 1},
     * requirements={
     *  "param1": "bar|baz",
     *  "param2": "doot|doom",
     *  "param3": "\d+",
     * })
     */
    public function advRoute2($param1, $param2, $param3): Response
    {
        return new Response("Controller is reached and param is correct.");
    }

    /**
     * @Route("/cookie/set", name="set_cookie")
     */
    public function setCookie(): Response
    {
        $cookie = new Cookie(
            'cookie_name',
            'coookie_value',
            time() + (24 * 60 * 60),
        );

        $response = new Response();
        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * @Route("/cookie/clear", name="clear_cookie")
     */
    public function clearCookie(): Response
    {
        $response = new Response();
        $response->headers->clearCookie('cookie_name');

        return $response;
    }

    /**
     * @Route("/session/set", name="set_session")
     */
    public function setSession()
    {
        $session = $this->requestStack->getSession();
        $session->set('foo', 'bar');
        exit();
    }

    /**
     * @Route("/session/get", name="get_session")
     */
    public function getSession()
    {
        $session = $this->requestStack->getSession();
        $value = $session->get('foo');
        exit($value);
    }

        /**
     * @Route("/session/clear", name="clear_session")
     */
    public function clearSession()
    {
        $session = $this->requestStack->getSession();
        $session->clear();
        exit();
    }

    /**
     * @Route("/params", name="params")
     */
    public function params(Request $request)
    {
        $get = $request->query->get('d'); //http://localhost:8080/params?d="oot"
        //$post = $request->request->get('d');
        //$file = $request->files->get('d');

        exit($get);
    }

    /**
     * @Route("/404", name="fourOfour")
     */
    public function fourOFour()
    {
        throw $this->createNotFoundException('Such a surprise! Exception 404! Something not found.');
    }
}
