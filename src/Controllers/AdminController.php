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
        $viewContext = [
            'nav' => $this->getNav(),
        ];

        return ResponseHelper::twig($response, $twig, 'butsing-admin/dashboard.twig', $viewContext);
    }

    /**
     * Gets the navigation items.
     *
     * @return array<mixed> The navigation items.
     */
    private function getNav(): array
    {
        $navs = [
            ['href' => '/butsing-admin/dashboard', 'label' => 'Dashboard'],
            ['type' => 'group', 'id' => 'group1', 'label' => 'group1'],
            ['href' => '#', 'label' => 'Group Item 1', 'group' => 'group1'],
            ['href' => '#', 'label' => 'Group Item 2', 'group' => 'group1'],
            ['href' => '#', 'label' => 'Group Item 3', 'group' => 'group1'],
            ['type' => 'group', 'id' => 'group2', 'label' => 'group2'],
            ['href' => '#', 'label' => 'Group Item 4', 'group' => 'group2'],
            ['href' => '#', 'label' => 'Group Item 5', 'group' => 'group2'],
            ['href' => '#', 'label' => 'Item 6'],
            ['href' => '#', 'label' => 'Item 7'],
        ];

        for ($i = 0; $i < \count($navs); $i++) {
            $navs[$i]['active'] = false;
        }
        $navs[0]['active'] = true;
        // $navs[1]['active'] = true;
        // $navs[3]['active'] = true;

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

        return $hierarchy;
    }
}
