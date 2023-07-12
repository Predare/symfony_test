<?php

namespace App\Controller;

use App\Entity\FeedbackForm;
use App\Form\FeedbackType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class FeedbackFormController extends AbstractController
{
    #[Route('/feedback/form', name: 'app_feedback_form')]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        // just set up a fresh $task object (remove the example data)
        $feedback = new FeedbackForm();

        $form = $this -> createForm(FeedbackType::class, $feedback);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $feedback = $form->getData(); 
            $entityManager->persist($feedback); 
            $entityManager->flush();
            
            $email = (new Email())
                -> from('Mailgun Sandbox <postmaster@sandbox6429b9b50fc64dd8a710e319f611fb7a.mailgun.org>')
                -> to('Dan rgfhg Kass <predare12000@gmail.com>')
                -> subject('Hello Dan rgfhg Kass')
                -> text('hello');
            $mailer->send($email);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('feedback_form/index.html.twig', [
            'controller_name' => 'FeedbackFormController',
            'form' => $form,
        ]);
    }
}
