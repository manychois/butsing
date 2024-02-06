<?php

declare(strict_types=1);

namespace Manychois\Butsing\Controllers;

use Manychois\Butsing\Core\SessionInterface as ISession;
use Psr\Http\Message\ResponseFactoryInterface as IResponseFactory;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;

/**
 * Checks if the user is authenticated.
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    private readonly IResponseFactory $responseFactory;
    private readonly ISession $session;

    /**
     * Initializes a new instance of the AuthenticationMiddleware class.
     *
     * @param IResponseFactory $responseFactory The response factory.
     * @param ISession         $session         The session.
     */
    public function __construct(IResponseFactory $responseFactory, ISession $session)
    {
        $this->responseFactory = $responseFactory;
        $this->session = $session;
    }

    #region implements MiddlewareInterface

    /**
     * @inheritDoc
     */
    public function process(IRequest $request, IRequestHandler $handler): IResponse
    {
        $userId = $this->session->getInt('user.id') ?? 0;
        if ($userId <= 0) {
            return $this->responseFactory->createResponse(401);
        }

        return $handler->handle($request);
    }

    #endregion implements MiddlewareInterface
}
