<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Vote;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Poll;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class PollController extends Controller
{
    /**
     * @Route("/", name="create")
     */
    public function createAction(Request $request)
    {

        $expirationArray = array(
            '0' => '2 minutes',
            '1' => '10 minutes',
            '2' => '30 minutes',
            '3' => '1 hour',
            '4' => '2 hours',
            '5' => '4 hours',
            '6' => '12 hours',
            '7' => '1 day',
            '8' => '2 days',
            '9' => '3 days',
            '10' => '7 days',
            '11' => '14 days',
        );

        $poll = new Poll();

        $form = $this->createFormBuilder($poll, ['attr' => [
            'id' => 'question-form',
            'novalidate' => null
        ]])
            ->add("question", TextType::class, array(
                'attr' => [
                    'class' => 'input is-large',
                    'placeholder' => 'Question (e.g. What is your favourite food?)'
                ],
                'label' => false
            ))
            ->add("allowMultipleAnswers", CheckboxType::class, array(
                'attr' => array(
                    'class' => 'switch is-info triggers-visibility',
                    'data-trigger-selector' => '#allowed-answer-count'
                ),
                'required' => false
            ))
            ->add("allowedAnswerCount", IntegerType::class, array(
                'attr' => array(
                    'class' => 'input',
                    'min' => '2',
                    'max' => '20',
                    'empty_data' => '2',
                    'value' => '2'
                ),
                'required' => false
            ))
            ->add("expirationDate", RangeType::class, array(
                'attr' => array(
                    'min' => min(array_keys($expirationArray)),
                    'max' => max(array_keys($expirationArray)),
                    'class' => 'slider has-output is-medium',
                    'empty_data' => '0',
                    'value' => '0',
                    'data-choicenames' => json_encode($expirationArray),
                )
            ))
            ->add("answer", CollectionType::class, array(
                // each entry in the array will be an Text field
                'entry_type' => TextType::class,
                'prototype' => true,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                // these options are passed to each "answer" type
                'entry_options' => array(
                    'attr' => array(
                        'class' => 'input is-medium',
                        'placeholder' => 'Answer __number__',
                    ),
                ),
            ))
            ->add("acceptedTos", CheckboxType::class, array(
                'attr' => array(
                    'class' => 'is-checkradio is-info',
                ),
                'required' => true,
                'label' => 'I accept the'
            ))
            ->add('recaptcha', EWZRecaptchaType::class, array(
                'attr'        => array(
                    'options' => array(
                        'theme' => 'light',
                        'type'  => 'image',
                        'size'  => 'normal'
                    )
                ),
                'label' => false,
                'mapped'      => false,
                'constraints' => array(
                    new RecaptchaTrue()
                )
            ))
            ->add("save", SubmitType::class, array(
                'label' => 'Create Poll',
                'attr' => array(
                    'class' => 'button is-primary',
                )
            ))
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

            /**
             * @var $poll Poll
             */
            $poll = $form->getData();

            $nowDateTime = new \DateTime();
            $minutesToAdd = $expirationTimesArray[$poll->getExpirationDate()];
            $nowDateTime->modify("+{$minutesToAdd} minutes");

            $poll->setExpirationDate($nowDateTime);
            if (!$poll->getAllowedAnswerCount()) {
                $poll->setAllowedAnswerCount(2);
            }

            $newAns = array_values($poll->getAnswer());
            $poll->setAnswer($newAns);

            // ... perform some action, such as saving the task to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($poll);
            $entityManager->flush();
            return $this->redirectToRoute('viewPoll', array(
                "pollId" => $poll->getId()
            ));
        }

        return $this->render('poll/create.html.twig', array(
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

        if (!$poll) {
            throw $this->createNotFoundException(
                'No poll found for id ' . $pollId
            );
        }

        $expirDate = $poll->getExpirationDate();
        $nowDate = new \DateTime();

        if ($expirDate <= $nowDate) {
            return $this->redirectToRoute('viewPollResult', [
                'pollId' => $pollId
            ]);
        }

        $timeLeftToVote = $this->getTimeLeft($poll);

        $allowedAnswerCount = 1;

        if ($poll->getAllowMultipleAnswers()) {
            $allowedAnswerCount = $poll->getAllowedAnswerCount();
        }

        $form = $this->createFormBuilder()
            ->add('answers', ChoiceType::class, array(
                'choices' => array_flip($poll->getAnswer()),
                'expanded' => true,
                'label' => false,
                'multiple' => $poll->getAllowMultipleAnswers(),
                'choice_attr' => function($val, $key, $index) {
                    return [
                        'class' => 'is-checkradio is-large'
                    ];
                },
                'attr' => array(
                    'class' => 'is-checkradio'
                )
            ))
            ->add("acceptedTos", CheckboxType::class, array(
                'attr' => array(
                    'class' => 'is-checkradio is-info',
                ),
                'required' => true,
                'label' => 'I accept the'
            ))
//            ->add('recaptcha', EWZRecaptchaType::class, array(
//                'attr'        => array(
//                    'options' => array(
//                        'theme' => 'light',
//                        'type'  => 'image',
//                        'size'  => 'normal'
//                    )
//                ),
//                'label' => false,
//                'mapped'      => false,
//                'constraints' => array(
//                    new RecaptchaTrue()
//                )
//            ))
            ->add('save', SubmitType::class, array(
                'attr' => array(
                    'class' => 'button is-primary'
                ),
                'label' => 'Submit Vote'
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $vote = $form->getData();
            $i = 1;

            if ($poll->getAllowMultipleAnswers()) {
                foreach ($vote['answers'] as $singleVote) {
                    if ($i > $allowedAnswerCount) {
                        break;
                    }

                    $tmp = new Vote();
                    $tmp->setPoll($pollId);
                    $tmp->setAnswerArrayId($singleVote);
                    $tmp->setAcceptedTos($vote['acceptedTos']);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($tmp);
                    $entityManager->flush();

                    $i++;
                }
            } else {
                $tmp = new Vote();
                $tmp->setPoll($pollId);
                $tmp->setAnswerArrayId($vote['answers']);
                $tmp->setAcceptedTos($vote['acceptedTos']);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($tmp);
                $entityManager->flush();
            }


            return $this->redirectToRoute('viewPollResult', [
                'pollId' => $pollId,
            ]);
        }

        return $this->render('poll/vote.html.twig', array(
            'poll' => $poll,
            'form' => $form->createView(),
            'timeLeftToVote' => $timeLeftToVote,
        ));
    }

    /**
     * @Route("/poll/{pollId}/result", name="viewPollResult")
     */
    public function resultAction(Request $request, $pollId)
    {
        $poll = $this->getDoctrine()
            ->getRepository(Poll::class)
            ->find($pollId);

        $answers = $poll->getAnswer();

        foreach (array_flip($answers) as $answer) {
            $votes[$answer] = count($this->getDoctrine()
                ->getRepository(Vote::class)
                ->findBy([
                    'poll' => $poll->getId(),
                    'answerArrayId' => $answer
                ])
            );
        }


        if ($request->isXMLHttpRequest()) {
            return new JsonResponse(array('votes' => $votes));
        }

        $timeLeftToVote = $this->getTimeLeft($poll);

        return $this->render('poll/result.html.twig', [
            'poll' => $poll,
            'votes' => $votes?$votes:null,
            'timeLeftToVote' => $timeLeftToVote,
        ]);

    }

    public function getTimeLeft(Poll $poll) {
        $expirDate = $poll->getExpirationDate();
        $nowDate = new \DateTime();
        $dateDiff = $nowDate->diff($expirDate);
        $timeLeftToVote = [];

        if (!$dateDiff->invert) {
            $timeLeftToVote = [
                $dateDiff->d,
                $dateDiff->h,
                $dateDiff->i,
                $dateDiff->s,
            ];
        }

        return $timeLeftToVote;
    }


}
