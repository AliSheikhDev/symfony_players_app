<?php

namespace App\Controller;

use App\Entity\Players;
use App\Form\PlayersType;
use App\Repository\PlayersRepository;
use App\Repository\TeamsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/players')]
class PlayersController extends AbstractController
{
    #[Route('/{team_id}', name: 'app_players_index', methods: ['GET'])]
    public function index($team_id, PlayersRepository $playersRepository, PaginatorInterface $paginator, Request $request, TeamsRepository $teamRepository): Response
    {
        $players = $playersRepository->findBy(
            ['team_id' => $team_id]
        );
        foreach($players as $player) {

            $team = $teamRepository->find($player->getTeamId());
            $player->setTeam($team);
        }
        // dd($players);
        $pagination = $paginator->paginate(
            $players,
            $request->query->getInt('page', 1), // page number
            2 // items per page
        );
        return $this->render('players/index.html.twig', [
            'players' => $pagination,
            'team_id' => $team_id
        ]);

    }

    #[Route('/{team_id}/new', name: 'app_players_new', methods: ['GET', 'POST'])]
    public function new($team_id, Request $request, PlayersRepository $playersRepository): Response
    {
        // dd(123);
        $player = new Players();
        $player->setTeamId($team_id);
        $form = $this->createForm(PlayersType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playersRepository->save($player, true);

            return $this->redirectToRoute('app_players_index', ['team_id'=>$team_id], Response::HTTP_SEE_OTHER);
        }
        // dd($team_id);
        return $this->renderForm('players/new.html.twig', [
            'player' => $player,
            'form' => $form,
            'team_id' => $team_id
        ]);
    }

    #[Route('/{team_id}/{id}', name: 'app_players_show', methods: ['GET'])]
    public function show($team_id, Players $player): Response
    {
        return $this->render('players/show.html.twig', [
            'player' => $player,
            'team_id' => $team_id
        ]);
    }

    #[Route('/{team_id}/{id}/edit', name: 'app_players_edit', methods: ['GET', 'POST'])]
    public function edit($team_id, Request $request, Players $player, PlayersRepository $playersRepository): Response
    {
        $form = $this->createForm(PlayersType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playersRepository->save($player, true);

            return $this->redirectToRoute('app_players_index', ['team_id'=>$team_id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('players/edit.html.twig', [
            'player' => $player,
            'form' => $form,
            'team_id' => $team_id
        ]);
    }

    #[Route('/{team_id}/{id}', name: 'app_players_delete', methods: ['POST'])]
    public function delete($team_id, Request $request, Players $player, PlayersRepository $playersRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$player->getId(), $request->request->get('_token'))) {
            $playersRepository->remove($player, true);
        }

        return $this->redirectToRoute('app_players_index', ['team_id'=>$team_id], Response::HTTP_SEE_OTHER);
    }
}
