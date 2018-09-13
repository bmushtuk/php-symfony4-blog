<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController'
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */

    public function home()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();

        return $this->render('blog/home.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */

    public function form(Article $article=null, Request $request, ObjectManager $manager) {
        
        $article = new Article();

        $form = $this->createFormBuilder($article)
                    ->add('title', TextType::class, [
                        'attr' => [
                            'placeholder' => "Title of the article",
                            'class' => "form-control"
                        ]
                    ])
                    ->add('content', TextareaType::class, [
                        'attr' => [
                            'placeholder' => "Content of the article",
                            'class' => "form-control"
                        ]
                    ])
                    ->add('image', TextType::class, [
                        'attr' => [
                            'placeholder' => "URL of the image",
                            'class' => "form-control"
                        ]
                    ])
                    ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
                'formArticle' => $form->createView(),
                'editMode' => $article->getId() !==null
        ]);
   
    }
    /**
     * @Route("/blog/{id}", name="blog_show")
     */

    public function show($id)
    {   $repo = $this->getDoctrine()->getRepository(Article::class);

        $article = $repo->find($id);

        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]
        );
    }
}
