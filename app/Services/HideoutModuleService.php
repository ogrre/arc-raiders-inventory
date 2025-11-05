<?php

namespace App\Services;

use Illuminate\Support\Collection;

class HideoutModuleService
{
    private ?Collection $modules = null;

    /**
     * Get all hideout modules.
     */
    public function all(): Collection
    {
        if ($this->modules === null) {
            $this->loadModules();
        }

        return $this->modules;
    }

    /**
     * Find a module by ID.
     */
    public function find(string $id): ?array
    {
        return $this->all()->firstWhere('id', $id);
    }

    /**
     * Calculate requirements for a specific level.
     */
    public function calculateLevelRequirements(string $moduleId, int $level, Collection $userInventory): array
    {
        $module = $this->find($moduleId);

        if (!$module) {
            return [];
        }

        $levelData = collect($module['levels'] ?? [])->firstWhere('level', $level);

        if (!$levelData) {
            return [];
        }

        $requirements = [];

        foreach ($levelData['requirementItemIds'] ?? [] as $requirement) {
            $itemId = $requirement['itemId'];
            $needed = $requirement['quantity'];
            $owned = $userInventory->firstWhere('item_id', $itemId)['quantity'] ?? 0;

            $requirements[] = [
                'item_id' => $itemId,
                'needed' => $needed,
                'owned' => $owned,
                'missing' => max(0, $needed - $owned),
                'completed' => $owned >= $needed,
            ];
        }

        return $requirements;
    }

    /**
     * Calculate total requirements to reach max level from current level.
     */
    public function calculateRequirementsToMaxLevel(
        string $moduleId,
        int $currentLevel,
        Collection $userInventory
    ): array {
        $module = $this->find($moduleId);

        if (!$module) {
            return [];
        }

        $allRequirements = [];

        foreach ($module['levels'] ?? [] as $levelData) {
            if ($levelData['level'] <= $currentLevel) {
                continue;
            }

            foreach ($levelData['requirementItemIds'] ?? [] as $requirement) {
                $itemId = $requirement['itemId'];
                $quantity = $requirement['quantity'];

                if (!isset($allRequirements[$itemId])) {
                    $allRequirements[$itemId] = [
                        'item_id' => $itemId,
                        'needed' => 0,
                        'owned' => $userInventory->firstWhere('item_id', $itemId)['quantity'] ?? 0,
                    ];
                }

                $allRequirements[$itemId]['needed'] += $quantity;
            }
        }

        // Calculate missing and completed
        foreach ($allRequirements as &$req) {
            $req['missing'] = max(0, $req['needed'] - $req['owned']);
            $req['completed'] = $req['owned'] >= $req['needed'];
        }

        return array_values($allRequirements);
    }

    /**
     * Load modules from JSON file.
     */
    private function loadModules(): void
    {
        $jsonPath = storage_path('app/arc-data/hideoutModules.json');

        if (!file_exists($jsonPath)) {
            $this->modules = collect([]);
            return;
        }

        $content = file_get_contents($jsonPath);
        $data = json_decode($content, true);

        $this->modules = collect($data ?? []);
    }
}
