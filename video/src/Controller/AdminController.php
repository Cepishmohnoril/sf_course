<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\CategoryTreeAdminList;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Utils\CategoryTreeAdminOptionList;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_main_page")
     */
    public function index()
    {
        return $this->render('admin/my_profile.html.twig');
    }


    /**
     * @Route("/su/categories", name="categories", methods={"GET", "POST"})
     */
    public function categories(CategoryTreeAdminList $categories, Request $request, ManagerRegistry $registry)
    {
        $categories->getCategoryList($categories->buildTree());
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $isInvalid = false;

        if($this->saveCategory($category, $form, $request, $registry)) {
            return $this->redirectToRoute('categories');
        } elseif($request->isMethod('POST')) {
            $isInvalid = true;
        }

        return $this->render('admin/categories.html.twig',[
            'categories' => $categories->categoryList,
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
        ]);
    }

    /**
     * @Route("/su/edit-category/{id}", name="edit_category", methods={"GET", "POST"})
     */
    public function editCategory(Category $category, Request $request, ManagerRegistry $registry)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $isInvalid = false;

        if($this->saveCategory($category, $form, $request, $registry)) {
            return $this->redirectToRoute('categories');
        } elseif($request->isMethod('POST')) {
            $isInvalid = true;
        }

        return $this->render('admin/edit_category.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
        ]);
    }

    private function saveCategory($category, $form, $reuqest, $registry): bool
    {
        $isInvalid = false;

        if($form->isSubmitted() && $form->isValid()) {
            $repository = $registry->getRepository(Category::class);
            $parent = $repository->find($reuqest->request->all('category')['parent']);
            $category->setName($reuqest->request->all('category')['name']);
            $category->setParent($parent);
            $registry->getManager()->persist($category);
            $registry->getmanager()->flush();

            return true;
        }

        return false;
    }

    /**
     * @Route("/su/delete-category/{id}", name="delete_category")
     */
    public function deleteCategory(Category $category, ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('categories');
    }

    /**
     * @Route("/videos", name="videos")
     */
    public function videos()
    {
        return $this->render('admin/videos.html.twig');
    }

    /**
     * @Route("/su/upload-video", name="upload_video")
     */
    public function uploadVideo()
    {
        return $this->render('admin/upload_video.html.twig');
    }

    /**
     * @Route("/su/users", name="users")
     */
    public function users()
    {
        return $this->render('admin/users.html.twig');
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/_all_categories.html.twig',[
            'categories' => $categories,
            'editedCategory' => $editedCategory,
        ]);
    }
}
