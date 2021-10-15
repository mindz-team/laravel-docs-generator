<?php

namespace Mindz\LaravelDocsGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FortifyDocsCommand extends Command
{
    protected $signature = 'docs-generate:fortify';

    protected $description = 'Generate fortify annotations files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $files = config('docs-generator.fortify_endpoints', []);

        $path = config('docs-generator.annotations_path', app_path() . '/Swagger') .'/'. config('docs-generator.actions_directory', 'Actions');

        $client = Storage::createLocalDriver(['root' => $path]);

        collect($files)->each(function ($file) use ($path, $client) {
            $filename = $file . '.php';

            if (file_exists($path . '/' . $filename)) {
                $this->error('File ' . $filename . ' already exists');
                return;
            }

            $fileExploded = explode('/', $file);
            $file = $fileExploded[count($fileExploded) - 1];

            $method = Str::camel($file . 'Stub');
            $client->put($filename, $this->$method());
        });
    }

    protected function loginStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Post(
 *     path="/login",
 *     summary="Authenticates user",
 *     tags={"authenticate"},
 *     operationId="authenticate-login",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *          @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="email",
 *                  example="example@mindz.it",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="password",
 *                  example="",
 *                  type="string"
 *              )
 *         ),
 *     ),
 * )
 */

EOT;
    }

    protected function csrfCookieStub()
    {
        return <<<EOT
<?php
/**
 * @OA\Get(
 *     path="/csrf-cookie",
 *     summary="Initiate session and sets cookies to authorization for all other endpoints",
 *     tags={"authenticate"},
 *     operationId="authenticate-cookie",
 *
 *     @OA\Response(
 *         response=204,
 *         description="No Content",
 *     ),
 *     security={
 *     }
 * )
 */
EOT;
    }

    protected function logoutStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Post(
 *     path="/logout",
 *     summary="Logout user",
 *     tags={"authenticate"},
 *     operationId="authenticate-logout",
 *
 *     @OA\Response(
 *         response=204,
 *         description="No Content",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
*      @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *        {"bearerAuth": {}}
 *    }
 * )
 */
EOT;
    }

    protected function forgetPasswordStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Post(
 *     path="/forgot-password",
 *     summary="Send link to resep password",
 *     tags={"password"},
 *     operationId="forgot-password",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="email",
 *                  example="example@mindz.it",
 *                  type="string"
 *              )
 *         ),
 *     ),
 *     security={}
 * )
 */
EOT;
    }

    protected function resetPasswordStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Post(
 *     path="/reset-password",
 *     summary="Resets user password",
 *     tags={"password"},
 *     operationId="reset-password",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *          @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="email",
 *                  example="example@mindz.it",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="password",
 *                  example="",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="password_confirmation",
 *                  example="",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="token",
 *                  example="",
 *                  type="string"
 *              )
 *         ),
 *     ),
 *     security={}
 * )
 */
EOT;
    }

    protected function registerStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Post(
 *     path="/register",
 *     summary="Register user",
 *     tags={"register"},
 *     operationId="register",
 *
 *     @OA\Response(
 *         response=201,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *          @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *          @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="name",
 *                  example="John Doe",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="email",
 *                  example="example@mindz.it",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="password",
 *                  example="",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="password_confirmation",
 *                  example="",
 *                  type="string"
 *              )
 *         ),
 *     ),
 * )
 */
EOT;
    }

    protected function showStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Get(
 *     path="/user",
 *     summary="User profile",
 *     tags={"profile"},
 *     operationId="user-profile",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *        {"bearerAuth": {}}
 *     }
 * )
 */
EOT;
    }

    protected function updateStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Put(
 *     path="/user/profile-information",
 *     summary="Update profile",
 *     tags={"profile"},
 *     operationId="update-profile",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *          @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="name",
 *                  example="John Doe",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="email",
 *                  example="example@mindz.it",
 *                  type="string"
 *              ),
 *         ),
 *     ),
 *     security={
 *        {"bearerAuth": {}}
 *     }
 * )
 */
EOT;
    }

    protected function updatePasswordStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Put(
 *     path="/user/password",
 *     summary="Update user password",
 *     tags={"profile"},
 *     operationId="update-password",
 *
 *     @OA\Response(
 *         response=204,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *          @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="current_password",
 *                  example="",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="password",
 *                  example="",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="password_confirmation",
 *                  example="",
 *                  type="string"
 *              ),
 *         ),
 *     ),
 *     security={
 *        {"bearerAuth": {}}
 *     }
 * )
 */
EOT;
    }

    protected function confirmPasswordStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Post(
 *     path="/user/confirm-password",
 *     summary="User password confirmation",
 *     tags={"profile"},
 *     operationId="confirm-password",
 *
 *     @OA\Response(
 *         response=201,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *          @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="password",
 *                  example="",
 *                  type="string"
 *              ),
 *         ),
 *     ),
 *     security={
 *        {"bearerAuth": {}}
 *     }
 * )
 */
EOT;
    }

    protected function passwordConfirmationStatusStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Get(
 *     path="/user/confirmed-password-status ",
 *     summary="User password confirmation status",
 *     tags={"profile"},
 *     operationId="confirm-password-status",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *        {"bearerAuth": {}}
 *     }
 * )
 */
EOT;
    }


    protected function sendVerificationEmailStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Post(
 *     path="/email/verification-notification",
 *     summary="Sends message to verify logged user email",
 *     tags={"email-verification"},
 *     operationId="confirm-email",
 *
 *     @OA\Response(
 *         response=202,
 *         description="Accepted",
 *         @OA\MediaType(mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No Content",
 *         @OA\MediaType(mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\MediaType(mediaType="application/json")
 *     ),
 *     security={
 *        {"bearerAuth": {}}
 *     }
 * )
 */
EOT;
    }


    protected function emailVerificationStub()
    {
        return <<<EOT
<?php

/**
 * @OA\Get(
 *     path="/email/verify/{id}/{hash}",
 *     summary="User email verification",
 *     tags={"email-verification"},
 *     operationId="confirm-password",
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User id",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="hash",
 *         in="path",
 *         description="User email sha1 hash",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="expires",
 *         in="query",
 *         description="Expiration timestamp",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="signature",
 *         in="query",
 *         description="Signature hash",
 *         required=true,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
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
*      @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *          @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *        {"bearerAuth": {}}
 *     }
 * )
 */
EOT;
    }

}
