<?php

declare(strict_types=1);

namespace Manychois\Butsing;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use Manychois\Butsing\Controllers\AuthenticationMiddleware;
use Manychois\Butsing\Core\EventDispatcher;
use Manychois\Butsing\Core\ListenerProvider;
use Manychois\Butsing\Core\PluginInterface;
use Manychois\Butsing\Core\SessionInterface;
use Manychois\PhpStrong\Collections\ArrayWrapper;
use Manychois\PhpStrong\Preg\Regex;
use Manychois\PhpStrong\StringUtility;
use PDO;
use Psr\Container\ContainerInterface as IContainer;
use Psr\Http\Message\ResponseFactoryInterface as IResponseFactory;
use Slim\App as SlimApp;
use Slim\Routing\RouteCollectorProxy;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Markup;
use Twig\TwigFunction;

use function DI\create;
use function DI\factory;

/**
 * The main application class in Butsing.
 */
class App
{
    private readonly ArrayWrapper $appConfig;
    private readonly ListenerProvider $listenerProvider;
    private readonly EventDispatcher $eventDispatcher;
    /**
     * @var array<class-string> The classes of the plugins to be used.
     */
    private readonly array $pluginClasses;

    /**
     * Initializes a new instance of the App class.
     *
     * @param array<string, mixed> $appConfig     The configuration for the application.
     * @param array<class-string>  $pluginClasses The classes of the plugins to be used.
     */
    public function __construct(array $appConfig, array $pluginClasses = [])
    {
        $this->appConfig = new ArrayWrapper($appConfig);
        $this->listenerProvider = new ListenerProvider();
        $this->eventDispatcher = new EventDispatcher($this->listenerProvider);
        $this->pluginClasses = $pluginClasses;
    }

    /**
     * Runs the application.
     */
    public function run(): void
    {
        $container = $this->setupContainer();
        /**
         * @var SlimApp $slim
         */
        $slim = Bridge::create($container);
        $container->set(IResponseFactory::class, $slim->getResponseFactory());

        foreach ($this->pluginClasses as $pluginClass) {
            if (\is_a($pluginClass, PluginInterface::class, true)) {
                /**
                 * @var class-string<PluginInterface> $pluginClass
                 * @var PluginInterface $plugin
                 */
                $plugin = $container->get($pluginClass);
                $plugin->registerListeners($this->listenerProvider);
            }
        }
        $this->setupRouting($slim, $container);
        $slim->run();
    }

    /**
     * Sets up the dependency injection container.
     *
     * @return Container The dependency injection container.
     */
    protected function setupContainer(): Container
    {
        $cb = new ContainerBuilder();
        $composeryHome = $this->appConfig->get('composery')->getString('home') ?? \sys_get_temp_dir() . '/composery';
        $cb->addDefinitions([
            Environment::class => factory(fn () => $this->setupTwig()),
            EventDispatcher::class => $this->eventDispatcher,
            ListenerProvider::class => $this->listenerProvider,
            PDO::class => factory(fn () => $this->setupPdo()),
            SessionInterface::class => create(Core\Implementations\Session::class),
            \Manychois\Composery\App::class => factory(fn () => new \Manychois\Composery\App($composeryHome)),
        ]);
        foreach ($this->pluginClasses as $pluginClass) {
            if (\method_exists($pluginClass, 'addDefinitions')) {
                $pluginClass::addDefinitions($cb);
            }
        }

        return $cb->build();
    }

    /**
     * Sets up the PDO instance.
     *
     * @return PDO The PDO instance.
     */
    protected function setupPdo(): PDO
    {
        $config = $this->appConfig->get('pdo');
        $dsn = \sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $config->getString('host'),
            $config->getString('dbname')
        );
        $pdo = new PDO($dsn, $config->getString('username'), $config->getString('password'));
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    /**
     * Sets up the routing.
     *
     * @param SlimApp    $slim      The Slim application instance.
     * @param IContainer $container The dependency injection container.
     */
    protected function setupRouting(SlimApp $slim, IContainer $container): void
    {
        $slim->get('/login', [Controllers\UserController::class, 'showLoginPage']);
        $slim->post('/login', [Controllers\UserController::class, 'verifyLogin']);
        $slim->get('/', [Controllers\PageController::class, 'showHome']);

        /**
         * @var AuthenticationMiddleware $auth
         */
        $auth = $container->get(AuthenticationMiddleware::class);
        $slim->group('/butsing-admin', function (RouteCollectorProxy $group) {
            $group->get('/dashboard', [Controllers\AdminController::class, 'showDashboard']);
            $group->get('/composer/info', [Controllers\ComposerController::class, 'showInfo']);
        })->addMiddleware($auth);

        $afterRoutingSet = new Events\AfterRoutingSetEvent($slim);
        $this->eventDispatcher->dispatch($afterRoutingSet);
    }

    /**
     * Sets up the Twig environment.
     *
     * @return Environment The Twig environment.
     */
    protected function setupTwig(): Environment
    {
        $rootPaths = [
            \dirname(__DIR__) . '/views',
        ];
        $loader = new FilesystemLoader($rootPaths);
        $twig = new Environment($loader);
        $twig->addFunction(
            new TwigFunction('hero_icon', static function (string $icon, string $class = '', array $attrs = []) {
                $file = \sprintf('%s/views/heroicons/%s.svg', \dirname(__DIR__), $icon);
                if (\file_exists($file)) {
                    $html = \file_get_contents($file);
                    \assert(\is_string($html));
                    $html = StringUtility::replace($html, ' data-slot="icon"', '');
                    if ($class !== '') {
                        $html = Regex::replace($html, '/\>/', \sprintf(' class="%s">', \htmlspecialchars($class)), 1);
                    }
                    if (\count($attrs) > 0) {
                        $partial = [];
                        foreach ($attrs as $key => $value) {
                            $partial[] = \sprintf('%s="%s"', $key, \htmlspecialchars($value));
                        }
                        $partial = \implode(' ', $partial) . '>';
                        $html = Regex::replace($html, '/\>/', $partial, 1);
                    }
                } else {
                    $html = \sprintf('<!-- hero_icon: %s not found -->', $icon);
                }

                return new Markup($html, 'UTF-8');
            })
        );

        return $twig;
    }
}
