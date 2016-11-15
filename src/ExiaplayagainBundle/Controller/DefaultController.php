<?php

namespace ExiaplayagainBundle\Controller;

use ExiaplayagainBundle\Entity\UsersVotes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $session = $request->getSession();

        return $this->render('ExiaplayagainBundle:Default:index.html.twig', array(
            'session' => $session->all(),
        ));
    }

    public function votesAction(Request $request)
    {
        $session = $request->getSession();

        if($this->checkConnected($session))
        {
            $votes_repo = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('ExiaplayagainBundle:Votes');

            $votes_upcoming = $votes_repo
                ->createQueryBuilder('u')
                ->where('u.limitedDate > :datetimecourant')
                ->setParameter('datetimecourant', new \DateTime(date('Y-m-d G:i:s')))
                ->orderBy('u.limitedDate', 'ASC')
                ->getQuery()
                ->getResult()
            ;

            foreach ($votes_upcoming as $vote_upcoming)
            {
                $vote_upcoming->setUserHasVoted($this->hasUserVoted($session, $vote_upcoming));

                if ($vote_upcoming->getUserHasVoted())
                {
                    $vote_upcoming->setVotedGame($this->getVotedGame($session, $vote_upcoming));
                    $this->getVoteStats($vote_upcoming);
                }
            }

            $votes_passed = $votes_repo
                ->createQueryBuilder('p')
                ->where('p.limitedDate < :datetimecourant')
                ->setParameter('datetimecourant', new \DateTime(date('Y-m-d G:i:s')))
                ->orderBy('p.limitedDate', 'DESC')
                ->getQuery()
                ->getResult()
            ;

            foreach ($votes_passed as $vote_passed)
            {
                $vote_passed->setUserHasVoted($this->hasUserVoted($session, $vote_passed));

                if ($vote_passed->getUserHasVoted())
                {
                    $vote_passed->setVotedGame($this->getVotedGame($session, $vote_passed));
                }

                $this->getVoteStats($vote_passed);
            }

            return $this->render('ExiaplayagainBundle:Default:votes.html.twig', array(
                'session' => $session->all(),
                'votes_upcoming' => $votes_upcoming,
                'votes_passed' => $votes_passed,
            ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function dovoteAction(Request $request, $voteid)
    {
        $session = $request->getSession();

        if($this->checkConnected($session))
        {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $user = $em
                ->getRepository('ExiaplayagainBundle:Users')
                ->findOneBy(array('username' => $session->get('login')));

            $vote = $em
                ->getRepository('ExiaplayagainBundle:Votes')
                ->find($voteid);

            if ($request->isMethod('POST'))
            {
                if(!$this->hasUserVoted($session, $vote))
                {
                    if (isset($_POST['radiovote'.$voteid]))
                    {
                        $game = $em
                            ->getRepository('ExiaplayagainBundle:Games')
                            ->find($_POST['radiovote'.$voteid]);

                        $userVote = new UsersVotes();
                        $userVote->setUser($user);
                        $userVote->setVote($vote);
                        $userVote->setGame($game);

                        $em->persist($userVote);

                        $em->flush();

                        $session->getFlashBag()->add('notice', 'Your vote was registered successfully');
                        return $this->redirectToRoute('exiaplayagain_votes');
                    }
                    else
                    {
                        $session->getFlashBag()->add('error', 'Select a game before you click on the vote button');
                        return $this->redirectToRoute('exiaplayagain_votes');
                    }
                }
                else
                {
                    $session->getFlashBag()->add('error', "You have already voted for this particular vote");
                    return $this->redirect($this->generateUrl('exiaplayagain_votes'));
                }
            }
            else
                return $this->redirect($this->generateUrl('exiaplayagain_votes'));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function gamesAction(Request $request)
    {
        $session = $request->getSession();$session = $request->getSession();

        if ($this->checkConnected($session)) {
            $games = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('ExiaplayagainBundle:Games')
                ->findBy(
                    array(),    //where
                    array('name' => 'ASC') //order
                );

            return $this->render('ExiaplayagainBundle:Default:games.html.twig', array(
                'session' => $session->all(),
                'games' => $games,
            ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    private function checkConnected($session)
    {
//        if(!$session->has('login') or $session->get('login') == '')
//            return false;

        $user = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ExiaplayagainBundle:Users')
            ->findOneByUsername($session->get('login'));

        if($user)
            return true;
        else
            return false;
    }

    private function hasUserVoted($session, $vote)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $user = $em->getRepository('ExiaplayagainBundle:Users')
            ->findOneByUsername($session->get('login'));

        $userVote = $em->getRepository('ExiaplayagainBundle:UsersVotes')
            ->findBy(
                array(
                    'user' => $user,
                    'vote' => $vote
                )
            );

        if ($userVote)
            return true;
        else
            return false;
    }

    private function getVotedGame($session, $vote)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $user = $em->getRepository('ExiaplayagainBundle:Users')
            ->findOneByUsername($session->get('login'));

        $votedGame = $em->getRepository('ExiaplayagainBundle:UsersVotes')
            ->findOneBy(
                array(
                    'user' => $user,
                    'vote' => $vote
                )
            )
            ->getGame();

        return $votedGame;
    }

    private function getVoteStats($vote)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $vote->setNumVotesGame1($this->getNumVotes($vote, $vote->getGame1()));
        $vote->setNumVotesGame2($this->getNumVotes($vote, $vote->getGame2()));
        $vote->setNumVotesGame3($this->getNumVotes($vote, $vote->getGame3()));
        $vote->setNumVotesGame4($this->getNumVotes($vote, $vote->getGame4()));

        $voteTotalVotes =
            $vote->getNumVotesGame1() +
            $vote->getNumVotesGame2() +
            $vote->getNumVotesGame3() +
            $vote->getNumVotesGame4();

        if ($voteTotalVotes != 0)
        {
            $vote->setPercentVotesGame1(round(($vote->getNumVotesGame1()/$voteTotalVotes)*100));
            $vote->setPercentVotesGame2(round(($vote->getNumVotesGame2()/$voteTotalVotes)*100));
            $vote->setPercentVotesGame3(round(($vote->getNumVotesGame3()/$voteTotalVotes)*100));
            $vote->setPercentVotesGame4(round(($vote->getNumVotesGame4()/$voteTotalVotes)*100));
        }
    }

    private function getNumVotes($vote, $game)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $numVotes = count($em->getRepository('ExiaplayagainBundle:UsersVotes')
            ->findBy(
                array(
                    'vote' => $vote,
                    'game' => $game
                )));

        return $numVotes;
    }
}

