<?php

declare(strict_types=1);

namespace Manychois\Butsing\Core;

use Psr\Http\Message\ResponseInterface as IResponse;
use Twig\Environment;

/**
 * The helper class for creating responses.
 */
class ResponseHelper
{
    /**
     * Returns a JSON response.
     *
     * @param IResponse $response The original response.
     * @param mixed     $data     The data to be encoded as JSON.
     * @param int       $status   The HTTP status code.
     *
     * @return IResponse The response with the JSON data.
     */
    public static function json(IResponse $response, mixed $data, int $status = 200): IResponse
    {
        $output = \json_encode($data, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR);
        $response->getBody()->write($output);

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    /**
     * Returns a response with a redirect header.
     *
     * @param IResponse $response The original response.
     * @param string    $url      The URL to redirect to.
     * @param int       $status   The HTTP status code.
     *
     * @return IResponse The response with the redirect header.
     */
    public static function redirect(IResponse $response, string $url, int $status = 302): IResponse
    {
        return $response->withHeader('Location', $url)->withStatus($status);
    }

    /**
     * Returns a response with the content rendered from a Twig template.
     *
     * @param IResponse    $response     The original response.
     * @param Environment  $twig         The Twig environment.
     * @param string       $templateName The name of the Twig template.
     * @param array<mixed> $context      The context data for the template.
     * @param int          $status       The HTTP status code.
     *
     * @return IResponse The response with the rendered content.
     */
    public static function twig(
        IResponse $response,
        Environment $twig,
        string $templateName,
        array $context = [],
        int $status = 200
    ): IResponse {
        $html = $twig->render($templateName, $context);
        $response->getBody()->write($html);

        return $response->withHeader('Content-Type', 'text/html')->withStatus($status);
    }
}
