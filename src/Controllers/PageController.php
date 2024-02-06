<?php

declare(strict_types=1);

namespace Manychois\Butsing\Controllers;

use Manychois\Butsing\Core\ResponseHelper;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Twig\Environment;

/**
 * Handles requests related to general pages.
 */
class PageController
{
    /**
     * Shows the home page.
     *
     * @param IRequest    $request  The incoming request.
     * @param IResponse   $response The original response.
     * @param Environment $twig     The Twig environment.
     *
     * @return IResponse The response to be sent to the client.
     */
    public function showHome(IRequest $request, IResponse $response, Environment $twig): IResponse
    {
        return ResponseHelper::twig($response, $twig, 'home.twig', ['content' => 'Hello, world?!']);
    }
}
