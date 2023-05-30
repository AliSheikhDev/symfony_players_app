<?php

namespace App\Controller;

use App\Form\PurchasePlayersType;
use App\Repository\PlayersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\TeamsRepository;

#[Route('/purchase/players')]
class PurchasePlayersController extends AbstractController
{
    #[Route('/', name: 'app_purchase_players_index', methods: ['GET'])]
    public function index(PlayersRepository $playersRepository, PaginatorInterface $paginator, Request $request, TeamsRepository $teamRepository): Response
    {
        $teamId = $request->query->get('team_id');
        $players = $playersRepository->createQueryBuilder('e')
        ->where('e.team_id != :team_id')
        ->setParameter('team_id', $teamId)
        ->getQuery()->getResult();
        foreach($players as $player) {

            $team = $teamRepository->find($player->getTeamId());
            $player->setTeam($team);
        }
        $pagination = $paginator->paginate(
            $players,
            $request->query->getInt('page', 1), // page number
            4 // items per page
        );
        return $this->render('purchase_players/index.html.twig', [
            'purchase_players' => $pagination,
            'current_team_id' => $teamId
        ]);
    }



    #[Route('/purchase', name: 'app_purchase_players_purchase', methods: ['GET'])]
    public function purchase(Request $request, PlayersRepository $playersRepository, TeamsRepository $teamRepository): Response
    {
        $playerId = $request->query->get('id');
        $teamId = $request->query->get('purchasing_team_id');
        $purchasingTeamData = $teamRepository->find($teamId);
        $purchasingTeamBalance = $purchasingTeamData->getMoneyBalance();
        $player = $playersRepository->find($playerId);
        $playerAmount = $player->getPurchaseAmount();
        $oldTeamId = $player->getTeamId();
        $player->setTeamId($teamId);
        if($purchasingTeamBalance >= $playerAmount) {
            $playersRepository->save($player, true);
            $newBalanceForPurchasingTeam = $purchasingTeamBalance - $playerAmount;
            $oldTeamData = $teamRepository->find($oldTeamId);
            $oldTeamBalance = $oldTeamData->getMoneyBalance();
            $newBalanceForOldTeam = $playerAmount + $oldTeamBalance;
            $purchasingTeamData->setMoneyBalance($newBalanceForPurchasingTeam);
            $oldTeamData->setMoneyBalance($newBalanceForOldTeam);
            $teamRepository->save($purchasingTeamData, true);
            $teamRepository->save($oldTeamData, true);

        }
        return $this->redirectToRoute('app_purchase_players_index', [], Response::HTTP_SEE_OTHER);
    }
}
