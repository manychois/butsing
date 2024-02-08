<?php

declare(strict_types=1);

namespace Manychois\Butsing\Controllers;

use Manychois\Butsing\Core\ResponseHelper;
use Manychois\Composery\App as ComposeryApp;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Twig\Environment;

/**
 * The controller for the composer-related pages.
 */
class ComposerController
{
    /**
     * Shows the info page.
     *
     * @param IRequest     $request   The incoming request.
     * @param IResponse    $response  The original response.
     * @param Environment  $twig      The Twig environment.
     * @param ComposeryApp $composery The Composery application.
     *
     * @return IResponse The response to be sent to the client.
     */
    public function showInfo(
        IRequest $request,
        IResponse $response,
        Environment $twig,
        ComposeryApp $composery
    ): IResponse {
        $packages = [];

        $output = $composery->runInput('show --format json');
        $json = \implode('', $output->getLines());
        $json = \json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
        \assert(\is_array($json));
        $packages = $json['installed'] ?? [];

        $viewContext = AdminController::getViewContext($request);
        $viewContext = \array_merge($viewContext, [
            'packages' => $packages,
            'debug' => $json,
        ]);

        return ResponseHelper::twig($response, $twig, 'butsing-admin/composer-info.twig', $viewContext);
    }
}
