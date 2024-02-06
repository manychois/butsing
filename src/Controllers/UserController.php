<?php

declare(strict_types=1);

namespace Manychois\Butsing\Controllers;

use Manychois\Butsing\Core\ResponseHelper;
use Manychois\Butsing\Core\SessionInterface as ISession;
use Manychois\Butsing\Persistence\UserRepository;
use Manychois\PhpStrong\Collections\ArrayWrapper;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Twig\Environment;

/**
 * Handles requests related to user authentication, registration, and management.
 */
class UserController
{
    /**
     * Shows the login page.
     *
     * @param IRequest    $request  The incoming request.
     * @param IResponse   $response The original response.
     * @param Environment $twig     The Twig environment.
     *
     * @return IResponse The response to be sent to the client.
     */
    public function showLoginPage(IRequest $request, IResponse $response, Environment $twig): IResponse
    {
        return ResponseHelper::twig($response, $twig, 'login.twig');
    }

    /**
     * Verifies the login credentials.
     *
     * @param IRequest       $request  The incoming request.
     * @param IResponse      $response The original response.
     * @param Environment    $twig     The Twig environment.
     * @param UserRepository $userRepo The user repository.
     * @param ISession       $session  The session.
     *
     * @return IResponse The response to be sent to the client.
     */
    public function verifyLogin(
        IRequest $request,
        IResponse $response,
        Environment $twig,
        UserRepository $userRepo,
        ISession $session
    ): IResponse {
        $params = $request->getParsedBody();
        \assert(\is_array($params));
        $params = new ArrayWrapper($params);
        $username = $params->getString('username') ?? '';
        $password = $params->getString('password') ?? '';

        $user = $userRepo->getUserByLogin($username, $password);
        if ($user === null) {
            $viewContext = [
                'formError' => 'Invalid username, email or password.',
                'username' => $username,
            ];

            return ResponseHelper::twig($response, $twig, 'login.twig', $viewContext, 401);
        }

        $session->setInt('user.id', $user->getId());

        return ResponseHelper::redirect($response, '/butsing-admin/dashboard');
    }
}
