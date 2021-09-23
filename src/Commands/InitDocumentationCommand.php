<?php

namespace Mindz\LaravelDocsGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InitDocumentationCommand extends Command
{
    protected $signature = 'docs-generate:init-documentation {--file=}';

    protected $description = 'Generate main annotations file ';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filename = Str::ucfirst(Str::camel($this->option('file') ?? 'annotations'));

        $path = config('docs-generator.annotations_path', app_path() . '/Swagger');
        $client = Storage::createLocalDriver(['root' => $path]);
        $filename = $filename . '.php';

        if (file_exists($path . '/' . $filename)) {
            $this->error('File ' . $filename . ' already exists');
            return;
        }

        $client->put($filename, $this->initFileStub());

        $this->info('Package used some constants for flexibility reasons there fore you have to define some swagger constants in config/l5-swagger.php file in constant sections.');
        $this->line("'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost'),
            'L5_SWAGGER_CONST_TITLE' => env('L5_SWAGGER_CONST_TITLE', 'Project title'),
            'L5_SWAGGER_CONST_EMAIL_CONTACT' => env('L5_SWAGGER_CONST_EMAIL_CONTACT', 'your_email@example.com'),
        ]");
    }

    protected function initFileStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title=L5_SWAGGER_CONST_TITLE,
 *      @OA\Contact(
 *          email=L5_SWAGGER_CONST_EMAIL_CONTACT
 *      )
 * )
 */

/**
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="api server"
 *  )
 * )
 *
 * @OA\SecurityScheme(
 *      type="http",
 *      securityScheme="bearerAuth",
 *      description="Authentication token",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *  )
 *
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="apiToken",
 *     name="X-App-Token"
 * )
 */
EOT;
    }
}
