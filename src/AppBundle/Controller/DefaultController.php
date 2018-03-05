<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Vote;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Poll;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request)
    {

        $expirationArray = array(
            '0' => '2min',
            '1' => '10min',
            '2' => '30min',
            '3' => '1h',
            '4' => '2h',
            '5' => '4h',
            '6' => '12h',
            '7' => '1d',
            '8' => '2d',
            '9' => '3d',
            '10' => '7d',
            '11' => '14d',
        );


        $poll = new Poll();

        $form = $this->createFormBuilder($poll)
            ->add("question", TextType::class)
            ->add("visibleToPublic", CheckboxType::class)
            ->add("isActive", CheckboxType::class)
            ->add("expirationDate", RangeType::class, array(
                'attr' => array(
                    'min' => min(array_keys($expirationArray)),
                    'max' => max(array_keys($expirationArray)),
                    'class' => 'slider has-output',
                    'empty_data' => '0',
                    'value' => '0',
                    'data-choicenames' => json_encode($expirationArray),
                )
            ))
            ->add("answer", CollectionType::class, array(
                // each entry in the array will be an "answer" field
                'entry_type' => TextType::class,
                'allow_add' => true,
                // these options are passed to each "answer" type
                'entry_options' => array(//                    'attr' => array('class' => 'email-box'),
                ),
            ))
            ->add('save', SubmitType::class, array('label' => 'Create Poll'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $expirationTimesArray = array(
                '0' => '2',
                '1' => '10',
                '2' => '30',
                '3' => '60',
                '4' => '120',
                '5' => '240',
                '6' => '720',
                '7' => '1440',
                '8' => '2880',
                '9' => '4320',
                '10' => '10080',
                '11' => '20160',
            );

            $poll = $form->getData();

            $nowDateTime = new \DateTime();
            $minutesToAdd = $expirationTimesArray[$poll->getExpirationDate()];
            $nowDateTime->modify("+{$minutesToAdd} minutes");

            $poll->setExpirationDate($nowDateTime);

            // ... perform some action, such as saving the task to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($poll);
            $entityManager->flush();
            return $this->redirectToRoute('viewPoll', array(
                "pollId" => $poll->getId()
            ));
        }

        return $this->render('poll/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/poll/{pollId}", name="viewPoll")
     */
    public function viewAction(Request $request, $pollId)
    {
        $poll = $this->getDoctrine()
            ->getRepository(Poll::class)
            ->find($pollId);

//        $vote = new Vote();
//        $vote->setPoll($poll->getId());
//
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->persist($vote);
//        $entityManager->flush();

        $votes = $this->getDoctrine()
            ->getRepository(Vote::class)
            ->findBy([
                'poll' => $poll->getId()
            ]);

        dump($votes);
        exit;

        if (!$poll) {
            throw $this->createNotFoundException(
                'No poll found for id ' . $pollId
            );
        }

        $expirationDate = $poll->getExpirationDate();
        $nowDate = new \DateTime('now');

        if ($expirationDate <= $nowDate) {
            return $this->render('poll/result.html.twig', array(
                'poll' => $poll
            ));
        }

        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('name', ChoiceType::class, array(
                'choices' => array_flip($poll->getAnswer()),
            ))
            ->add('send', SubmitType::class)
            ->getForm();

        return $this->render('poll/show.html.twig', array(
            'poll' => $poll,
            'form' => $form->createView(),
        ));
    }

}
