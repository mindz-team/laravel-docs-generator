<?php

namespace Mindz\LaravelDocsGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateCrudControllerDocsCommand extends Command
{
    const INDEX_ACTION = 'index';

    const SHOW_ACTION = 'show';

    const STORE_ACTION = 'store';

    const UPDATE_ACTION = 'update';

    const DESTROY_ACTION = 'destroy';

    const ACTIONS = [
        self::INDEX_ACTION,
        self::SHOW_ACTION,
        self::STORE_ACTION,
        self::UPDATE_ACTION,
        self::DESTROY_ACTION,
    ];

    protected $signature = 'docs-generate:crud-controller {name : Resource name }  {--schema= : custom schema name } {--tag= : custom endpoints tag } {--only= : comma separated action that should only be generated} {--except= : comma separated action that should not be generated} {--security= : endpoint security. Default is bearerAuth }';

    protected $description = 'Generate swagger documentation for crud controller';

    private string $resource;
    private string $tag;
    private string $schema;
    private string $security;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->resource = Str::kebab(Str::plural($this->argument('name')));
        $this->tag = $this->option('tag') ?? $this->resource;
        $this->schema = Str::singular(Str::title($this->option('schema') ?? $this->resource));
        $this->security = $this->option('security') ?? 'bearerAuth';

        $path = config('docs-generator.annotations_path', app_path() . '/Swagger');
        $client = Storage::createLocalDriver(['root' => $path]);

        $actionPath = config('docs-generator.actions_directory', 'Actions') . '/' . Str::ucfirst(Str::camel($this->resource));
        $schemaPath = config('docs-generator.schemas_directory', 'Schemas') . '/' . $this->schema;

        $actions = collect(self::ACTIONS);

        if ($this->option('only')) {
            $allowedActions = explode(',', $this->option('only'));
            $actions = $actions->reject(fn($action) => !in_array($action, $allowedActions));
        }

        if ($this->option('except')) {
            $allowedActions = explode(',', $this->option('except'));
            $actions = $actions->reject(fn($action) => in_array($action, $allowedActions));
        }

        if (file_exists($path . '/' . $schemaPath . '.php')) {
            $this->error('File ' . $schemaPath . '.php' . ' already exists');
        }

        $client->put($schemaPath . '.php', $this->schemaStub());

        $actions->transform(fn($action) => $action . 'Stub')
            ->each(function ($method) use ($client, $actionPath, $path) {
                $actionFilename = $actionPath . '/' . Str::ucfirst(Str::remove('Stub', $method)) . '.php';

                if (file_exists($path . '/' . $actionFilename)) {
                    $this->error('File ' . $actionFilename . ' already exists');
                }

                $client->put($actionFilename, $this->$method());
            });
    }

    protected function schemaStub()
    {
        $schemaDirectory = config('docs-generator.schemas_directory', 'Schemas');
        $namespace = sprintf("App\Swagger\%s", $schemaDirectory);
        return <<<EOT
<?php

namespace {$namespace};

/**
 * Class {$this->schema}
 * @OA\Schema(
 *     title="{$this->schema}",
 *     description="{$this->schema} model",
 * )
 */
class {$this->schema}
{
    /**
     * @OA\Property(
     *     format="integer",
     *     description="Id of object",
     *     title="ID",
     *     readOnly=true
     * )
     * @var string
     */
    private \$id;

    /**
     * @OA\Property(
     *     format="string",
     *     description="Name",
     *     title="Name"
     * )
     * @var string
     */
    private \$name;

}
EOT;
    }

    protected function indexStub()
    {
        $resourceTitle = str_replace('-', ' ', Str::title($this->resource));

        return <<<EOT
<?php

/**
 * @OA\Get(
 *     path="/{$this->resource}",
 *     summary="Return list of {$resourceTitle}",
 *     tags={"{$this->tag}"},
 *     operationId="index-{$this->resource}",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/{$this->schema}"
 *         ),
 *
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *         {"{$this->security}": {}}
 *     }
 * )
 */
EOT;
    }

    protected function storeStub()
    {
        $resourceSingular = Str::singular(Str::snake($this->resource));
        $resourceTitle = str_replace('-', ' ', Str::title($this->resource));

        return <<<EOT
<?php

/**
 * @OA\Post(
 *     path="/{$this->resource}",
 *     summary="Add new {$resourceTitle}",
 *     tags={"{$this->tag}"},
 *     operationId="store-{$this->resource}",
 *
 *     @OA\Response(
 *         response=201,
 *         description="Created",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/{$this->schema}"
 *         ),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *          @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\RequestBody(
 *          @OA\JsonContent(
 *             allOf={
 *                 @OA\Schema( ref="#/components/schemas/{$this->schema}"),
 *             },
 *             required={},
 *         )
 *     ),
 *     security={
 *         {"{$this->security}": {}}
 *     }
 * )
 */

EOT;
    }

    protected function showStub()
    {
        $resourceSingular = Str::singular(Str::snake($this->resource));
        $resourceTitle = str_replace('-', ' ', Str::title($this->resource));

        return <<<EOT
<?php

/**
 * @OA\Get(
 *     path="/{$this->resource}/{{$resourceSingular}}",
 *     summary="Show {$resourceTitle}",
 *     tags={"{$this->tag}"},
 *     operationId="show-{$this->resource}",
 *
 *     @OA\Parameter(
 *         name="{$resourceSingular}",
 *         in="path",
 *         description="{$resourceTitle} id",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Ok",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/{$this->schema}"
 *         ),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *         {"{$this->security}": {}}
 *     }
 * )
 */

EOT;
    }

    protected function updateStub()
    {
        $resourceSingular = Str::singular(Str::snake($this->resource));
        $resourceTitle = str_replace('-', ' ', Str::title($this->resource));

        return <<<EOT
<?php

/**
 * @OA\Patch(
 *     path="/{$this->resource}/{{$resourceSingular}}",
 *     summary="Update {$resourceTitle}",
 *     tags={"{$this->tag}"},
 *     operationId="update-{$this->resource}",
 *
 *     @OA\Parameter(
 *         name="{$resourceSingular}",
 *         in="path",
 *         description="{$resourceTitle} id",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Ok",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/{$this->schema}"
 *         ),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             allOf={
 *                 @OA\Schema( ref="#/components/schemas/{$this->schema}"),
 *             },
 *             required={},
 *         )
 *     ),
 *     security={
 *         {"{$this->security}": {}}
 *     }
 * )
 */

EOT;
    }

    protected function destroyStub()
    {
        $resourceSingular = Str::singular(Str::snake($this->resource));
        $resourceTitle = str_replace('-', ' ', Str::title($this->resource));

        return <<<EOT
<?php

/**
 * @OA\Delete (
 *     path="/{$this->resource}/{{$resourceSingular}}",
 *     summary="Delete {$resourceTitle}",
 *     tags={"{$this->tag}"},
 *     operationId="destroy-{$this->resource}",
 *
 *     @OA\Parameter(
 *         name="{$resourceSingular}",
 *         in="path",
 *         description="{$resourceTitle} id",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=204,
 *         description="No Content",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *         {"{$this->security}": {}}
 *     }
 * )
 */

EOT;
    }

}
