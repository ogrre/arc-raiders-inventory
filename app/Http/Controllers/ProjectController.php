<?php

namespace App\Http\Controllers;

use App\Services\ProjectService;
use App\Services\ItemService;
use App\Services\UserInventoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private ItemService $itemService,
        private UserInventoryService $inventoryService
    ) {}

    /**
     * Display all projects.
     */
    public function index(Request $request): View
    {
        $projects = $this->projectService->all();

        return view('projects.index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show project details with requirements calculator.
     */
    public function show(Request $request, string $projectId): View
    {
        $project = $this->projectService->find($projectId);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $userInventory = $this->inventoryService->getUserInventory($request->user()->id);

        // Calculate requirements for each phase
        $phasesWithRequirements = [];
        foreach ($project['phases'] ?? [] as $phase) {
            $phaseRequirements = $this->projectService->calculateRequirements(
                $projectId,
                $phase['phase'],
                $userInventory
            );

            // Enrich with item details
            foreach ($phaseRequirements as &$req) {
                $req['item'] = $this->itemService->find($req['item_id']);
            }

            $phasesWithRequirements[] = [
                'phase' => $phase,
                'requirements' => $phaseRequirements,
                'completed' => collect($phaseRequirements)->every('completed'),
            ];
        }

        // Calculate total requirements
        $totalRequirements = $this->projectService->calculateTotalRequirements(
            $projectId,
            $userInventory
        );

        // Enrich with item details
        foreach ($totalRequirements as &$req) {
            $req['item'] = $this->itemService->find($req['item_id']);
        }

        return view('projects.show', [
            'project' => $project,
            'phasesWithRequirements' => $phasesWithRequirements,
            'totalRequirements' => collect($totalRequirements),
        ]);
    }
}
