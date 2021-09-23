<?php

namespace Mindz\LaravelDocsGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateDocCommand extends Command
{
    protected $signature = 'docs-generate:endpoint {path : endpoint without prefix}  {--summary= : Short description }  {--method= : Http methods like: GET,POST,UPDATE,DELETE } {--tag= : Custom endpoints tag } {--security= : Endpoint security. Default is bearerAuth } {--success-response= : Response code in case of success }';

    protected $description = 'Generate swagger documentation for single endpoint';

    private string $path;
    private string $tag;
    private int $successResponse;
    private string $summary;
    private string $method;
    private string $security;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->path = ltrim($this->argument('path'), '/');
        $this->summary = $this->option('summary') ?? 'Example description';
        $this->tag = $this->option('tag') ?? Arr::first($this->explodedPath());
        $this->method = $this->option('method') ?? "GET";

        if (!in_array(Str::lower($this->method), ['get', "post", "patch", "delete"])) {
            $this->error('Allowed methods are get.post,patch and delete');
            return;
        }

        $this->successResponse = $this->option('success-response') ?? 200;
        $this->security = $this->option('security') ?? 'bearerAuth';

        $path = config('docs-generator.annotations_path', app_path() . '/Swagger');
        $client = Storage::createLocalDriver(['root' => $path]);

        $actionPath = config('docs-generator.actions_directory', 'Actions') . '/' . Str::ucfirst(Str::plural($this->tag));

        $filename = $path . '/' . $actionPath . '/' . Str::ucfirst(Arr::last($this->explodedPath())) . '.php';

        if(file_exists($filename)){
            $this->error('File ' .  $actionPath . '/' . Str::ucfirst(Arr::last($this->explodedPath())) . '.php' . ' already exists');
        }

        $client->put($actionPath . '/' . Str::ucfirst(Arr::last($this->explodedPath())) . '.php', $this->customEndpoint());
    }

    private function explodedPath(): array
    {
        return collect(explode('/', $this->path))
            ->reject(fn($segment) => !$segment || Str::startsWith($segment, '{'))
            ->toArray();
    }

    protected function customEndpoint()
    {
        $lastPath = Arr::last($this->explodedPath());
        $method = sprintf("@OA\%s", Str::ucfirst(Str::lower($this->method)));
        $model = Str::ucfirst(Arr::last($this->explodedPath()));

        $requestBody = in_array(Str::lower($this->method), ['post', 'path']) ? <<<EOT
 *     @OA\RequestBody(
 *         description="{$model} model",
 *         required=true,
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="example_id",
 *                  example=10,
 *                  type="integer"
 *              )
 *       )
 *     ),
EOT: ' *';

        $pathParameter = $this->parameterInPath();
        $pathParameterSegment = $pathParameter ? <<<EOT
 *
 *     @OA\Parameter(
 *         name="{$pathParameter}",
 *         in="path",
 *         description="{$pathParameter} Id",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
EOT: ' *';

        return <<<EOT
<?php

/**
 * {$method}(
 *     path="/{$this->path}",
 *     summary="{$this->summary}",
 *     tags={"{$this->tag}"},
 *     operationId="custom-{$lastPath}",
{$pathParameterSegment}
{$requestBody}
 *     @OA\Response(
 *         response={$this->successResponse},
 *         description="Success",
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

    private function parameterInPath()
    {
        preg_match('/[{](.*)[}]/', $this->path, $matches);
        return Arr::first($matches);
    }
}
