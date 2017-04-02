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
    public function addAction(Request $request)
    {
        // On crée un objet Advert
        $advert = new Article();

        // J'ai raccourci cette partie, car c'est plus rapide à écrire !
        $form = $this->get('form.factory')->createBuilder(FormType::class, $advert)
            ->add('tittle',     TextType::class)
            ->add('content',   TextareaType::class)
            ->add('envoyer',      SubmitType::class)
            ->getForm()
        ;

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            // (Nous verrons la validation des objets en détail dans le prochain chapitre)
            if ($form->isValid()) {
                // On enregistre notre objet $advert dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add('success', 'Article bien enregistrée.');

                // On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('admin', array('id' => $advert->getId()));
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('blog/admin/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}