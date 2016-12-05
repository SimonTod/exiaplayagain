<?php

namespace ExiaplayagainBundle\Controller;

use ExiaplayagainBundle\Entity\Users;
use ExiaplayagainBundle\Entity\Games;
use ExiaplayagainBundle\Entity\Votes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use ExiaplayagainBundle\Form\GamesType;
use ExiaplayagainBundle\Form\VotesType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminController extends Controller
{
    private $superAdminUsers = array('simon', 'kevinsiry');

    private $megaAdminUsers = array('simon');

    public function userslistAction(Request $request)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session)) {
            $users = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('ExiaplayagainBundle:Users')
                ->findBy(
                    array(),    //where
                    array('id' => 'ASC')//order
                );

            return $this->render('ExiaplayagainBundle:Admin:userslist.html.twig', array(
                'session' => $session->all(),
                'users' => $users,
            ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function adduserAction(Request $request)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session))
        {
            if($request->isMethod('POST') && $_POST['password1'] == $_POST['password2'] && $this->checkAvailableUsername($_POST['username']))
            {
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $user = new Users();

                $user->setUsername($_POST['username']);
                $user->setName($_POST['name']);
                $user->setPassword(password_hash($_POST['password1'], PASSWORD_DEFAULT));
                $user->setIsAdmin(false);

                $em->persist($user);

                $em->flush();

                $session->getFlashBag()->add('notice', "Utilisateur ".$_POST['username']." créé avec succès");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
            elseif ($request->isMethod('POST') && !$this->checkAvailableUsername($_POST['username']))
            {
                $session->getFlashBag()->add('error', "Le nom d'utilisateur ".$_POST['username']." est déjà présent dans la liste des utilisateurs");
                return $this->render('ExiaplayagainBundle:Admin:adduser.html.twig', array(
                    'session' => $session->all(),
                ));
            }
            elseif ($request->isMethod('POST') && $_POST['password1'] != $_POST['password2'])
            {
                $session->getFlashBag()->add('error', 'Veuillez entrer 2 fois le même nouveau mot de passe');
                return $this->render('ExiaplayagainBundle:Admin:adduser.html.twig', array(
                    'session' => $session->all(),
                ));
            }
            else
                return $this->render('ExiaplayagainBundle:Admin:adduser.html.twig', array(
                    'session' => $session->all(),
                ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function removeuserAction(Request $request, $username)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session))
        {
            if(isset($username) && $username != $session->get('login') && !in_array($username, $this->superAdminUsers))
            {
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $user = $em
                    ->getRepository('ExiaplayagainBundle:Users')
                    ->findOneByUsername($username);

                $userVotes = $em
                    ->getRepository('ExiaplayagainBundle:UsersVotes')
                    ->findBy(
                        array('user' => $user));

                foreach($userVotes as $userVote)
                {
                    $em->remove($userVote);
                }

                $em->remove($user);
                $em->flush();

                $session->getFlashBag()->add('notice', "Utilisateur ".$username." supprimé avec succès. Attention, si l'utilisateur avait effectué des votes, les statistiques des votes ont été modifiés");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
            else if ($username == $session->get('login'))
            {
                $session->getFlashBag()->add('error', "Vous ne pouvez pas supprimé l'utilisateur avec lequel vous êtes connectés");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
            else if (in_array($username, $this->superAdminUsers))
            {
                $session->getFlashBag()->add('error', "Vous ne pouvez pas supprimé un Super Administrateur");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function changeisadminAction(Request $request, $username)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session))
        {
            if(isset($username) && $username != $session->get('login') && !in_array($username, $this->superAdminUsers))
            {
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $user = $em
                    ->getRepository('ExiaplayagainBundle:Users')
                    ->findOneByUsername($username);

                if ($user->getIsAdmin() == true)
                {
                    $user->setIsAdmin(false);

                    $session->getFlashBag()->add('notice', "L'utilisateur ".$username." n'est plus un administrateur");
                }
                else
                {
                    $user->setIsAdmin(true);

                    $session->getFlashBag()->add('notice', "L'utilisateur ".$username." est maintenant un administrateur");
                }

                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
            else if ($username == $session->get('login'))
            {
                $session->getFlashBag()->add('error', "Vous ne pouvez pas enlver les droits d'administrations pour l'utilisateur avec lequel vous êtes connectés");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
            else if (in_array($username, $this->superAdminUsers))
            {
                $session->getFlashBag()->add('error', "Vous ne pouvez pas enlever les droites d'administrations pour un Super Administrateur");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function gameslistAction(Request $request)
    {
        $session = $request->getSession();$session = $request->getSession();

        if ($this->checkAdmin($session)) {
            $games = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('ExiaplayagainBundle:Games')
                ->findBy(
                    array(),    //where
                    array('name' => 'ASC') //order
                );

            return $this->render('ExiaplayagainBundle:Admin:gameslist.html.twig', array(
                'session' => $session->all(),
                'games' => $games,
            ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function addgameAction(Request $request)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session)) {
            $game = new Games();
            $form = $this->createForm(GamesType::class, $game);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $game = $form->getData();

                $file = $game->getImage();

                if (!is_null($file))
                {
                    if ($this->checkFileIsImage($file->guessExtension()))
                    {
                        $filename = md5(uniqid()).'.'.$file->guessExtension();

                        $file->move(
                            $this->getParameter('img_games_directory'),
                            $filename
                        );

                        $game->setImage($filename);
                    }
                    else
                    {
                        $session->getFlashBag()->add('error', 'File is not a supported format (jpg, jpeg, png)');

                        return $this->render('ExiaplayagainBundle:Admin:addgame.html.twig', array(
                            'session' => $session->all(),
                            'form' => $form->createView(),
                        ));
                    }

                }

                if(is_null($game->getPrice()))
                    $game->setPrice(0);

                $em->persist($game);
                $em->flush();

                $session->getFlashBag()->add('notice', 'Game was added');
                return $this->redirect($this->generateUrl('exiaplayagain_gameslist'));

            }
            else
                return $this->render('ExiaplayagainBundle:Admin:addgame.html.twig', array(
                    'session' => $session->all(),
                    'form' => $form->createView(),
                ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function editgameAction(Request $request, $gameid)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session)) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $game = $em
                ->getRepository('ExiaplayagainBundle:Games')
                ->findOneBy(
                    array('id' => $gameid));
            $gameImage = $game->getImage();

            $form = $this->createForm(GamesType::class, $game);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $game = $form->getData();

                $file = $game->getImage();

                if (!is_null($file))
                {
                    if ($this->checkFileIsImage($file->guessExtension()))
                    {
                        $filename = md5(uniqid()).'.'.$file->guessExtension();

                        $file->move(
                            $this->getParameter('img_games_directory'),
                            $filename
                        );

                        $game->setImage($filename);
                    }
                    else
                    {
                        $session->getFlashBag()->add('error', 'File is not a supported format (jpg, jpeg, png)');

                        return $this->render('ExiaplayagainBundle:Admin:editgame.html.twig', array(
                            'session' => $session->all(),
                            'form' => $form->createView(),
                        ));
                    }

                }
                else if ($gameImage != '')
                {
                    $game->setImage($gameImage);
                }

                if(is_null($game->getPrice()))
                    $game->setPrice(0);

                $em->persist($game);
                $em->flush();

                $session->getFlashBag()->add('notice', 'Game was eddited');
                return $this->redirect($this->generateUrl('exiaplayagain_gameslist'));

            }
            else
                return $this->render('ExiaplayagainBundle:Admin:editgame.html.twig', array(
                    'session' => $session->all(),
                    'form' => $form->createView(),
                ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function removegameAction(Request $request, $gameid)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session))
        {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $game = $em
                ->getRepository('ExiaplayagainBundle:Games')
                ->findOneBy(
                    array('id' => $gameid));

            $gamename = $game->getName();

            $votesAssociated = $em
                ->getRepository('ExiaplayagainBundle:Votes')
                ->createQueryBuilder('v')
                ->where('v.game1 = :game')
                ->orWhere('v.game2 = :game')
                ->orWhere('v.game3 = :game')
                ->orWhere('v.game4 = :game')
                ->setParameter('game', $game)
                ->getQuery()
                ->getResult();

            if ($votesAssociated)
            {
                $session->getFlashBag()->add('error', "The game ".$gamename." has votes associated to it. You should think again before removing it. If you still want to remove the game, please remove associated votes before");
                return $this->redirect($this->generateUrl('exiaplayagain_gameslist'));
            }
            else
            {
                $em->remove($game);
                $em->flush();

                $session->getFlashBag()->add('notice', "The game ".$gamename." was successfully deleted");
                return $this->redirect($this->generateUrl('exiaplayagain_gameslist'));
            }
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function voteslistAction(Request $request)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session)) {

            $votes_upcoming = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('ExiaplayagainBundle:Votes')
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

                if (in_array($session->get('login'), $this->megaAdminUsers))
                {
                    $this->getVoteStats($vote_upcoming);
                }
            }

            $votes_passed = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('ExiaplayagainBundle:Votes')
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

            return $this->render('ExiaplayagainBundle:Admin:voteslist.html.twig', array(
                'session' => $session->all(),
                'votes_upcoming' => $votes_upcoming,
                'votes_passed' => $votes_passed,
            ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function addvoteAction(Request $request)
    {
        $session = $request->getSession();$session = $request->getSession();

        if ($this->checkAdmin($session)) {
            $vote = new Votes();
            $form = $this->createForm(VotesType::class, $vote);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $vote = $form->getData();

                $em->persist($vote);
                $em->flush();

                $session->getFlashBag()->add('notice', 'Vote was added');
                return $this->redirect($this->generateUrl('exiaplayagain_voteslist'));

            }
            else {
                $neverPlayedGames = $this->listNeverPlayedGames();
                $playedGames = $this->listPlayedGames();

                return $this->render('ExiaplayagainBundle:Admin:addvote.html.twig', array(
                    'session' => $session->all(),
                    'form' => $form->createView(),
                    'neverPlayedGames' => $neverPlayedGames,
                    'playedGames' => $playedGames,
                ));
            }
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function removevoteAction(Request $request, $voteid)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session)) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $vote = $em
                ->getRepository('ExiaplayagainBundle:Votes')
                ->findOneBy(
                    array('id' => $voteid));

            $usersVotes = $em
                ->getRepository('ExiaplayagainBundle:UsersVotes')
                ->findBy(
                    array('vote' => $vote));

            foreach ($usersVotes as $userVotes)
            {
                $em->remove($userVotes);
            }

            $em->remove($vote);
            $em->flush();

            $session->getFlashBag()->add('notice', "The vote was successfully deleted");
            return $this->redirect($this->generateUrl('exiaplayagain_voteslist'));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    private function checkAdmin($session)
    {
        if(!$session->has('login'))
            return false;

        $user = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ExiaplayagainBundle:Users')
            ->findOneByUsername($session->get('login'));

        if($user->getIsAdmin())
            return true;
        else
            return false;
    }

    private function checkAvailableUsername($username)
    {
        $user = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ExiaplayagainBundle:Users')
            ->findOneByUsername($username);

        if (empty($user))
            return true;
        else
            return false;
    }

    private function checkFileIsImage($imageExtension)
    {
        $acceptedExtensions = array('jpg', 'JPG', 'jpeg', 'JPEG','png', 'PNG');

        if (in_array($imageExtension, $acceptedExtensions))
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

        $vote->setTotalUsersVotes(
            $vote->getNumVotesGame1() +
            $vote->getNumVotesGame2() +
            $vote->getNumVotesGame3() +
            $vote->getNumVotesGame4());

        if ($vote->getTotalUsersVotes() != 0)
        {
            $vote->setPercentVotesGame1(round(($vote->getNumVotesGame1()/$vote->getTotalUsersVotes())*100));
            $vote->setPercentVotesGame2(round(($vote->getNumVotesGame2()/$vote->getTotalUsersVotes())*100));
            $vote->setPercentVotesGame3(round(($vote->getNumVotesGame3()/$vote->getTotalUsersVotes())*100));
            $vote->setPercentVotesGame4(round(($vote->getNumVotesGame4()/$vote->getTotalUsersVotes())*100));
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

    private function listNeverPlayedGames(){
        $em = $this
            ->getDoctrine()
            ->getManager();

        $neverPlayedGames = $em->getRepository('ExiaplayagainBundle:Games')
            ->findBy(
                array('lastPlayed' => null), //where
                array('name' => 'ASC') //order
            );

        return $neverPlayedGames;
    }

    private function listPlayedGames(){
        $em = $this
            ->getDoctrine()
            ->getManager();

        $playedGames = $em->getRepository('ExiaplayagainBundle:Games')
            ->createQueryBuilder('q')
            ->where('q.lastPlayed IS NOT NULL')
            ->orderBy('q.lastPlayed', 'ASC')
            ->getQuery()
            ->getResult();

        return $playedGames;
    }
}
