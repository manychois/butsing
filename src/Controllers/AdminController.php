<?php

declare(strict_types=1);

namespace Manychois\Butsing\Controllers;

use Manychois\Butsing\Core\ResponseHelper;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Twig\Environment;

/**
 * The controller for the admin dashboard.
 */
class AdminController
{
    /**
     * Shows the dashboard.
     *
     * @param IRequest    $request  The incoming request.
     * @param IResponse   $response The original response.
     * @param Environment $twig     The Twig environment.
     *
     * @return IResponse The response to be sent to the client.
     */
    public function showDashboard(IRequest $request, IResponse $response, Environment $twig): IResponse
    {
        $viewContext = self::getViewContext($request);

        return ResponseHelper::twig($response, $twig, 'butsing-admin/dashboard.twig', $viewContext);
    }

    /**
     * Returns the general view context.
     *
     * @param IRequest $request The incoming request.
     *
     * @return array<mixed> The general view context.
     */
    public static function getViewContext(IRequest $request): array
    {
        return [
            'nav' => self::getNav($request),
        ];
    }

    /**
     * Gets the navigation items.
     *
     * @param IRequest $request The incoming request.
     *
     * @return array<mixed> The navigation items.
     */
    private static function getNav(IRequest $request): array
    {
        $path = $request->getUri()->getPath();
        $navs = [
            ['href' => '/butsing-admin/dashboard', 'label' => 'Dashboard'],
            ['type' => 'group', 'id' => 'composer', 'label' => 'Composer'],
            ['href' => '/butsing-admin/composer/info', 'label' => 'Info', 'group' => 'composer'],
            ['type' => 'group', 'id' => 'group2', 'label' => 'group2'],
            ['href' => '#', 'label' => 'Group Item 4', 'group' => 'group2'],
            ['href' => '#', 'label' => 'Group Item 5', 'group' => 'group2'],
            ['href' => '#', 'label' => 'Item 6'],
            ['href' => '#', 'label' => 'Item 7'],
        ];

        for ($i = 0; $i < \count($navs); $i++) {
            if (($navs[$i]['href'] ?? '') === $path) {
                $navs[$i]['active'] = true;
            } else {
                $navs[$i]['active'] = false;
            }
        }

        $hierarchy = [];
        $groupLookup = [];
        foreach ($navs as $nav) {
            if (isset($nav['type']) && $nav['type'] === 'group') {
                $nav['children'] = [];
                $hierarchy[] = $nav;
                /**
                 * @var string
                 */
                $id = $nav['id'];
                $groupLookup[$id] = \count($hierarchy) - 1;
            } elseif (isset($nav['group'])) {
                $j = $groupLookup[$nav['group']] ?? -1;
                if ($j >= 0) {
                    $hierarchy[$j]['children'][] = $nav;
                }
            } else {
                $hierarchy[] = $nav;
            }
        }

        foreach ($hierarchy as &$nav) {
            if (isset($nav['children']) && \count($nav['children']) > 0) {
                $active = false;
                foreach ($nav['children'] as $child) {
                    if ($child['active']) {
                        $active = true;
                        break;
                    }
                }
                if ($active) {
                    $nav['active'] = true;
                }
            }
        }

        return $hierarchy;
    }
}
