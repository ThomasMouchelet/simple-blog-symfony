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

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class BlogController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

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

        $articles = $repository->findAll();

        $templating = $this->get('templating');

        $html = $templating->render('blog/admin/index.html.twig',compact('articles'));

        return new Response($html);
    }

    /**
     * @Route("/article_delete", name="article_delete")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request)
    {
        $id = $_GET['id'];

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $article = $repository->find($id);

        $em->remove($article);
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', 'Article supprimé.');

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function editAction(Request $request)
    {
        if (isset($_GET['id'])){
            $id = $_GET['id'];
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Article::class);
            $advert = $repository->find($id);

            $request->getSession()->getFlashBag()->add('success', 'Article modifié.');
        }else{
            $advert = new Article();
            $request->getSession()->getFlashBag()->add('success', 'Article ajouté.');
        }

        $form = $this->get('form.factory')->createBuilder(FormType::class, $advert)
            ->add('tittle',     TextType::class)
            ->add('content',   TextareaType::class)
            ->add('envoyer',      SubmitType::class)
            ->getForm()
        ;

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();



                // On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('admin', array('id' => $advert->getId()));
            }
        }

        return $this->render('blog/admin/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}