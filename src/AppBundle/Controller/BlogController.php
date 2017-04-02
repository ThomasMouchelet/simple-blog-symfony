<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 31/03/2017
 * Time: 14:55
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class BlogController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        /*$test = new Article();
        $test->setTittle('Titre')
             ->setContent('Lorem dsffsdfsdfsd')
             ->setPublishDate(new \DateTime());*/
        $em = $this->getDoctrine()->getManager();

        /*$em->persist($test);
        $em->flush();*/

        $repository = $em->getRepository(Article::class);
        $articles = $repository->findAll();

        $templating = $this->get('templating');

        $html = $templating->render('blog/index.html.twig', compact('articles'));

        return new Response($html);
    }

    /**
     * @Route("/article", name="article")
     */
    public function articleAction()
    {
        $id = $_GET['id'];

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $article = $repository->find($id);

        $templating = $this->get('templating');

        $html = $templating->render('blog/article.html.twig', compact('article'));

        return new Response($html);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(Article::class);

        if (isset($_POST['content'])){
            if (isset($_POST['id'])) {
                $article = $repository->find($_POST['id']);
            }else{
                $article = new Article();
            }

            $article->setTittle($_POST['tittle'])
                ->setContent($_POST['content']);

            if (isset($_POST['id'])) {
                $article->setEditDate(new \DateTime());
            }else{
                $article->setPublishDate(new \DateTime());
            }

            $em->persist($article);
            $em->flush();
        }

        $articles = $repository->findAll();

        $templating = $this->get('templating');

        $html = $templating->render('blog/admin/index.html.twig',compact('articles'));

        return new Response($html);
    }

    /**
     * @Route("/article_delete", name="article_delete")
     * @Security("has_role('ROLE_AUTEUR') and has_role('ROLE_AUTRE')")
     */
    public function deleteAction()
    {
        $id = $_GET['id'];

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $article = $repository->find($id);

        $em->remove($article);
        $em->flush();

        $articles = $repository->findAll();

        $templating = $this->get('templating');

        $html = $templating->render('blog/admin/index.html.twig',compact('articles'));

        return new Response($html);
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function editAction()
    {
        $id = $_GET['id'];

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $article = $repository->find($id);

        $templating = $this->get('templating');

        $html = $templating->render('blog/admin/edit.html.twig', compact('article'));

        return new Response($html);
    }

    /**
     * @Route("/add", name="add")
     */
    public function addtAction()
    {
        $templating = $this->get('templating');
        $html = $templating->render('blog/admin/add.html.twig');

        return new Response($html);
    }

}