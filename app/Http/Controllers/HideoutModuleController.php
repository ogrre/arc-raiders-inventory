<?php

namespace App\Http\Controllers;

use App\Services\HideoutModuleService;
use App\Services\ItemService;
use App\Services\UserInventoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HideoutModuleController extends Controller
{
    public function __construct(
        private HideoutModuleService $moduleService,
        private ItemService $itemService,
        private UserInventoryService $inventoryService
    ) {}

    /**
     * Display all hideout modules.
     */
    public function index(Request $request): View
    {
        $modules = $this->moduleService->all();

        return view('hideout.index', [
            'modules' => $modules,
        ]);
    }

    /**
     * Show module details with requirements calculator.
     */
    public function show(Request $request, string $moduleId): View
    {
        $module = $this->moduleService->find($moduleId);

        if (!$module) {
            abort(404, 'Module not found');
        }

        $userInventory = $this->inventoryService->getUserInventory($request->user()->id);
        $currentLevel = $request->get('current_level', 0);

        // Calculate requirements for each level
        $levelsWithRequirements = [];
        foreach ($module['levels'] ?? [] as $levelData) {
            $levelRequirements = $this->moduleService->calculateLevelRequirements(
                $moduleId,
                $levelData['level'],
                $userInventory
            );

            // Enrich with item details
            foreach ($levelRequirements as &$req) {
                $req['item'] = $this->itemService->find($req['item_id']);
            }

            $levelsWithRequirements[] = [
                'level' => $levelData,
                'requirements' => $levelRequirements,
                'completed' => collect($levelRequirements)->every('completed'),
            ];
        }

        // Calculate total requirements from current to max level
        $totalRequirements = $this->moduleService->calculateRequirementsToMaxLevel(
            $moduleId,
            $currentLevel,
            $userInventory
        );

        // Enrich with item details
        foreach ($totalRequirements as &$req) {
            $req['item'] = $this->itemService->find($req['item_id']);
        }

        return view('hideout.show', [
            'module' => $module,
            'currentLevel' => $currentLevel,
            'levelsWithRequirements' => $levelsWithRequirements,
            'totalRequirements' => collect($totalRequirements),
        ]);
    }
}
