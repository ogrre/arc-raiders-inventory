<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ProjectService
{
    private ?Collection $projects = null;

    /**
     * Get all projects.
     */
    public function all(): Collection
    {
        if ($this->projects === null) {
            $this->loadProjects();
        }

        return $this->projects;
    }

    /**
     * Find a project by ID.
     */
    public function find(string $id): ?array
    {
        return $this->all()->firstWhere('id', $id);
    }

    /**
     * Calculate what items are needed for a project phase.
     */
    public function calculateRequirements(string $projectId, int $phase, Collection $userInventory): array
    {
        $project = $this->find($projectId);

        if (!$project) {
            return [];
        }

        $phaseData = collect($project['phases'] ?? [])->firstWhere('phase', $phase);

        if (!$phaseData) {
            return [];
        }

        $requirements = [];

        foreach ($phaseData['requirementItemIds'] ?? [] as $requirement) {
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
     * Calculate total requirements for all phases.
     */
    public function calculateTotalRequirements(string $projectId, Collection $userInventory): array
    {
        $project = $this->find($projectId);

        if (!$project) {
            return [];
        }

        $allRequirements = [];

        foreach ($project['phases'] ?? [] as $phase) {
            foreach ($phase['requirementItemIds'] ?? [] as $requirement) {
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
     * Load projects from JSON file.
     */
    private function loadProjects(): void
    {
        $jsonPath = storage_path('app/arc-data/projects.json');

        if (!file_exists($jsonPath)) {
            $this->projects = collect([]);
            return;
        }

        $content = file_get_contents($jsonPath);
        $data = json_decode($content, true);

        $this->projects = collect($data ?? []);
    }
}
