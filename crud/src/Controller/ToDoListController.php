<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{
    /**
     * @Route("/", name="list_task")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $tasks = $doctrine->getRepository(Task::class)->findBy([], ['id' => 'DESC']);

        return $this->render('index.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/create", name="create_task", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $title = trim($request->request->get('title'));

        if (!empty($title)) {
            $em = $doctrine->getManager();
            $task = new Task();
            $task->setTitle($title);
            $em->persist($task);
            $em->flush();
        }

        return $this->redirectToRoute('list_task');
    }

    /**
     * @Route("/switch-status/{id}", name="switch_status")
     */
    public function switchStatus(int $id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $task = $doctrine->getRepository(Task::class)->find($id);
        $task->setStatus(!$task->getStatus());
        $em->flush();

        return $this->redirectToRoute('list_task');
    }

    /**
     * @Route("/delete/{id}", name="delete_task")
     */
    public function delete(Task $task, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute('list_task');
    }
}
