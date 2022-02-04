<?php

namespace App\Controller;

use App\Entity\SecurityUser;
use App\Entity\User;
use App\Entity\Video;
use App\Events\VideoCreatedEvent;
use App\Form\RegisterUserType;
use App\Form\VideoFormType;
use App\Services\MailService;
use App\Services\MyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends AbstractController
{
    public function __construct(MailService $mail, RequestStack $requestStack, EventDispatcherInterface $dispatcher, $logger) {
        $this->mail = $mail;
        $this->requestStack = $requestStack;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/add/{name}", name="add")
     */
    public function addUsers(string $name): Response {
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName($name);

        for ($i=1; $i <= 3; $i++) {
            $video = new Video();
            $video->setTitle('Video-' . $i);
            $user->addVideo($video);
            $em->persist($video);
        }

        $em->persist($user);
        $em->flush();

        $this->addFlash('notice', 'User saved!');

         return $this->redirectToRoute('tpl');
    }

    /**
     * @Route("/edit/{name}", name="edit")
     */
    public function editUsers(string $name): Response {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find(1);
        $user->setName($name);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('notice', 'User saved!');

         return $this->redirectToRoute('tpl');
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteUsers(int $id): Response {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);
        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('notice', 'User saved!');

         return $this->redirectToRoute('tpl');
    }

    /**
     * @Route("/tpl", name="tpl")
     */
    public function tpl(MyService $myService): Response {
        $repo = $this->getDoctrine()->getRepository(User::class);

        $users = $repo->findAll();

        //$user = $repo->find(1);
        //$user = $repo->findOneBy(['name' => 'Doot-1']);
        //$user = $repo->findBy(['name' => 'Doot-1']);
        //dump($user);

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
    public function _json(): Response {
        return $this->json(['hello' => 'world']);
    }

    /**
     * @Route("/param/{name}", name="param")
     */
    public function param($name): Response {
        return new Response("Hello $name");
    }

    /**
     * @Route("/redirect", name="redirect")
     */
    public function _redirect(): Response {
        return $this->redirectToRoute('target');
    }

    /**
     * @Route("/link", name="redirect")
     */
    public function link(): Response {
        return $this->render(
            'default/link.html.twig'
        );
    }

    /**
     * @Route("/target", name="target")
     */
    public function targetCtl(): Response {
        return new Response("Hello form redirect!");
    }

    /**
     * @Route("/adv_route/{param?}", name="adv_route", requirements={"param"="\d+"})
     */
    public function advRoute($param): Response {
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
    public function advRoute2($param1, $param2, $param3): Response {
        return new Response("Controller is reached and param is correct.");
    }

    /**
     * @Route("/cookie/set", name="set_cookie")
     */
    public function setCookie(): Response {
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
    public function clearCookie(): Response {
        $response = new Response();
        $response->headers->clearCookie('cookie_name');

        return $response;
    }

    /**
     * @Route("/session/set", name="set_session")
     */
    public function setSession() {
        $session = $this->requestStack->getSession();
        $session->set('foo', 'bar');
        exit();
    }

    /**
     * @Route("/session/get", name="get_session")
     */
    public function getSession() {
        $session = $this->requestStack->getSession();
        $value = $session->get('foo');
        exit($value);
    }

    /**
     * @Route("/session/clear", name="clear_session")
     */
    public function clearSession() {
        $session = $this->requestStack->getSession();
        $session->clear();
        exit();
    }

    /**
     * @Route("/params", name="params")
     */
    public function params(Request $request) {
        $get = $request->query->get('d'); //http://localhost:8080/params?d="oot"
        //$post = $request->request->get('d');
        //$file = $request->files->get('d');

        exit($get);
    }

    /**
     * @Route("/404", name="four_o_four")
     */
    public function fourOFour() {
        throw $this->createNotFoundException('Such a surprise! Exception 404! Something not found.');
    }

    /**
     * @Route("/get_url", name="get_url")
     */
    public function getUrl(): Response {
        $url = $this->generateUrl(
            'adv_route',
            ['param' => 10],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return new Response($url);
    }

    /**
     * @Route("/download", name="download")
     */
    public function download(): Response {
        $path = $this->getParameter('download_directory');
        return $this->file($path . 'PHP+7+Zend+Certification+Study+Guide.pdf');
    }

    /**
     * @Route("/redirect_from", name="redirect_from")
     */
    public function redirectTestFrom(): Response {
        return $this->redirectToRoute('redirect_to', ['param' => 10]);
    }


    /**
     * @Route("/redirect_to/{param?}", name="redirect_to")
     */
    public function redirectTestTo($param): Response {
        return new Response('Redirected with param: ' . $param);
    }

    /**
     * @Route("/forward_from", name="forward_from")
     */
    public function forwardFromCotrolller(): Response {
        return $this->forward(
            'App\Controller\DefaultController::forwardToCotrolller',
            ['param' => 42],
        );
    }

     /**
     * @Route("/forward_to/{param?}", name="forward_to")
     */
    public function forwardToCotrolller($param): Response {
        return new Response('Forwarded with param: ' . $param);
    }

    /**
     * @Route("/gib_monke/{param?}", name="gib_monke")
     */
    public function getMonkeys($param = 3): Response {

        $monkeys = ['monke 1', 'monke 2', 'monke 3', 'monke 4', 'monke 5', 'doot'];

        return $this->render(
            'default/monkeys.html.twig',
            [
                'monkeys' => $monkeys,
            ]
        );
    }

    /**
     * @Route("/cache", name="cache")
     */
    public function cache(): Response {
        $cache = new FilesystemAdapter();
        $posts = $cache->getItem('database.get_posts');

        if (!$posts->isHit()) {
            $posts_from_db = ['post 1', 'post 2', 'post 3'];

            dump('DB is reached for data.');

            $posts->set(serialize($posts_from_db));
            $posts->expiresAfter(5);
            $cache->save($posts);
        }

        dump(unserialize($posts->get()));

        return $this->render(
            'default/default.html.twig'
        );
    }

    /**
     * @Route("/create_video", name="create_video")
     */
    public function createVideo(): Response {

        $video = new \stdClass();
        $video->title = 'Doot';
        $video->duration = '1h';

        $event = new VideoCreatedEvent($video);
        $this->dispatcher->dispatch($event, 'video.created.event');

        return $this->render(
            'default/default.html.twig'
        );
    }

    /**
     * @Route("/form_video", name="form_video")
     */
    public function formVideo(Request $request): Response {

        $video = new Video();
        $video->setTitle('Hello World!');
        $video->setCreatedAt(new \DateTime('now'));

        $form = $this->createForm(VideoFormType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dump($form->getData());
            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();
            $this->redirectToRoute('tpl');
        }

        return $this->render(
            'default/formVideo.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response {

        $user = new SecurityUser();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $user->setEmail($form->get('email')->getData());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->redirectToRoute('tpl');
        }

        return $this->render(
            'default/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authUtils): Response {

        $error = $authUtils->getLastAuthenticationError();
        $lastEmail = $authUtils->getLastUsername();

        return $this->render(
            'default/login.html.twig',
            [
                'last_email' => $lastEmail,
                'error' => $error,
            ]
        );
    }
}
