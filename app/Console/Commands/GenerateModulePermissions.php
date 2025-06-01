<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class GenerateModulePermissions extends Command
{
    protected $signature = 'permissions:generate {module? : Optional module name (e.g., users)}';
    protected $description = 'Generate CRUD + custom permissions from Filament Resources';

    public function handle()
    {
        $module = $this->argument('module');

        if ($module) {
            $this->generatePermissionsForModule($module);
        } else {
            $this->generatePermissionsFromResources();
        }

        $this->info('✅ Permission generation complete.');
        return 0;
    }

    protected function generatePermissionsFromResources(): void
    {
        $resourcePath = app_path('Filament/Resources');
        $files        = File::allFiles($resourcePath);

        foreach ($files as $file) {
            if (!Str::endsWith($file->getFilename(), 'Resource.php')) {
                continue;
            }

            $class = $this->getFullClassNameFromFile($file->getRealPath());
            if (!class_exists($class))
                continue;

            $module = Str::of(class_basename($class))
                ->beforeLast('Resource')
                ->plural()
                ->lower()
                ->toString();

            $this->info("🔍 Generating permissions for: {$module}");

            $actions = $this->extractActionsFromResource($class);
            $this->generatePermissions($module, $actions);
        }
    }

    protected function generatePermissionsForModule(string $module): void
    {
        // fallback static actions
        $actions = ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
        $this->generatePermissions($module, $actions);
    }

    protected function generatePermissions(string $module, array $actions): void
    {
        foreach ($actions as $action) {
            $permissionName = "{$module}.{$action}";

            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            if ($permission->wasRecentlyCreated) {
                $this->info("✅ Created: {$permissionName}");
            } else {
                $this->line("⚠️ Exists: {$permissionName}");
            }
        }
    }

    protected function extractActionsFromResource(string $resourceClass): array
    {
        $actions = [];

        // Page permission mapping
        $pageActionMap = [
            'index'  => 'view',
            'create' => 'create',
            'edit'   => 'update',
        ];

        if (method_exists($resourceClass, 'getPages')) {
            $pages = $resourceClass::getPages();

            foreach (array_keys($pages) as $pageMethod) {
                $action    = $pageActionMap[$pageMethod] ?? $pageMethod;
                $actions[] = Str::of($action)->snake()->toString();
            }
        }

        if (method_exists($resourceClass, 'getRelations')) {
            $relations = $resourceClass::getRelations();
            foreach ($relations as $relationClass) {
                $relationBase = class_basename($relationClass);
                $actions[]    = Str::of($relationBase)->snake()->toString();
            }
        }

        return array_unique($actions);
    }

    protected function getFullClassNameFromFile(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);
        if (preg_match('/namespace\s+(.+?);/', $contents, $matches)) {
            $namespace = $matches[1];
            $className = Str::before(basename($filePath), '.php');
            return "{$namespace}\\{$className}";
        }

        return null;
    }
}