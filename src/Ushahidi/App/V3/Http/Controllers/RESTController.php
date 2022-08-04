<?php

/**
 * Ushahidi REST Base Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Ushahidi\Factory\UsecaseFactory;
use Ushahidi\Multisite\MultisiteManager;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Ushahidi\App\Exceptions\ValidationException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class RESTController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var string Current API version
     * @todo  move to config?
     */
    protected static $version = '3';

    /**
     * @var \Ushahidi\Factory\UsecaseFactory
     */
    protected $usecaseFactory;

    /**
     * @var \Ushahidi\Multisite\MultisiteManager;
     */
    protected $multisite;

    /**
     * @var \Ushahidi\Contracts\Usecase
     */
    protected $usecase;

    public function __construct(UsecaseFactory $usecaseFactory, MultisiteManager $multisite)
    {
        $this->usecaseFactory = $usecaseFactory;
        $this->multisite = $multisite;
    }

    /**
     * List of HTTP methods which may be cached
     *
     * @var array
     */
    protected $cacheableMethods = [
        Request::METHOD_GET,
    ];

    /**
     * Get current api version
     */
    public static function version()
    {
        return self::$version;
    }

    public function demoCheck($filters)
    {
        $isDemoTier = $this->multisite->getSite()->tier === 'demo_1';
        if ($this->multisite->enabled() && $isDemoTier) {
            // Demo deployments are limited to the first 25 posts,
            // if any thing other more than that or a different offset is request
            // none will be returned
            if (array_key_exists('offset', $filters)
                && array_key_exists('limit', $filters)
            ) {
                if ($filters['offset'] + $filters['limit'] > 25) {
                    $diff = 25 - $filters['offset'];
                    $filters['limit'] = $diff > 0 ? $diff : 0;
                }
            } else {
                $limit = 25;
                if (array_key_exists('limit', $filters)) {
                    $limit = $filters['limit'];
                }
                $filters['limit'] = $limit > 25 ? 25 : $limit;
            }
        }

        return $filters;
    }

    /**
     * Get an API URL for a resource.
     *
     * @param  string  $resource
     * @param  mixed   $id
     * @return string
     * @todo  move this somewhere sensible
     */
    public static function url($resource, $id = null)
    {
        $template = 'api/v%d/%s';
        if (! is_null($id)) {
            $template .= '/%s';
        }

        return rtrim(sprintf($template, static::version(), $resource, $id), '/');
    }

    /**
     * Get options for a resource collection.
     *
     * OPTIONS /api/foo
     */
    public function indexOptions(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'options');

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    /**
     * Get options for a resource.
     *
     * OPTIONS /api/foo/:id
     */
    public function showOptions(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'options')
            ->setIdentifiers($this->getRouteParams($request));

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    /**
     * Create An Entity
     *
     * POST /api/foo
     */
    public function store(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'create')
            ->setPayload($request->json()->all());

        $result = $this->executeUsecase($request);

        return $this->prepResponse($result, $request);
    }

    /**
     * Retrieve All Entities
     *
     * GET /api/foo
     */
    public function index(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
            ->setFilters($request->query());

        $result = $this->executeUsecase($request);

        return $this->prepResponse($result, $request);
    }

    /**
     * Retrieve An Entity
     *
     * GET /api/foo/:id
     */
    public function show(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'read')
            ->setIdentifiers($this->getRouteParams($request));

        $result = $this->executeUsecase($request);

        return $this->prepResponse($result, $request);
    }

    /**
     * Update An Entity
     *
     * PUT /api/foo/:id
     */
    public function update(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'update')
            ->setIdentifiers($this->getRouteParams($request))
            ->setPayload($request->json()->all());

        $result = $this->executeUsecase($request);

        return $this->prepResponse($result, $request);
    }

    /**
     * Delete An Entity
     *
     * DELETE /api/foo/:id
     */
    public function destroy(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'delete')
            ->setIdentifiers($this->getRouteParams($request));

        $result = $this->executeUsecase($request);

        return $this->prepResponse($result, $request);
    }

    /**
     * Get the resource name for this endpoint.
     * @return string
     */
    abstract protected function getResource();

    /**
     * Get the identifiers to pass to the usecase. Defaults to the request route params.
     * @return array
     */
    protected function getRouteParams(Request $request)
    {
        return $request->route()->parameters();
    }

    /**
     * Execute the usecase that the controller prepared.
     *
     * @todo  should this take Usecase as a param rather than use $this->usecase?
     * @return array|void
     */
    protected function executeUsecase(Request $request)
    {
        try {
            // Attempt to execute the usecase to get the response
            $responsePayload = $this->usecase->interact();

            return $responsePayload;
        } catch (\Ushahidi\Core\Exception\NotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Ushahidi\Core\Exception\AuthorizerException $e) {
            // If we don't have an Authorization header, return 401
            if (! $request->headers->has('Authorization')) {
                abort(
                    401,
                    'The request is missing an access token in either the Authorization header.',
                    ['www-authenticate' => 'Bearer realm="OAuth"']
                );
            } else {
                // Otherwise throw a 403
                abort(403, $e->getMessage());
            }
        } catch (\Ushahidi\Core\Exception\ThrottlingException $e) {
            abort(429, 'Too Many Requests');
        } catch (\Ushahidi\Core\Exception\ValidatorException $e) {
            throw new ValidationException($e->getMessage(), $e);
        } catch (\InvalidArgumentException $e) {
            abort(400, 'Bad request: '.$e->getMessage());
        }
    }

    /**
     * Prepare response headers and body, formatted based on user request.
     *
     * @param  array|null   $result
     * @param  \Illuminate\Http\Request  $request
     * @todo This should be moved into a middleware
     */
    protected function prepResponse($result, Request $request)
    {
        // Use JSON if the request method is OPTIONS
        if ($request->method() === Request::METHOD_OPTIONS) {
            $type = 'json';
        } else {
            //...Get the requested response format, use JSON for default
            // @todo should just Accept header
            $type = strtolower($request->query('format')) ?: 'json';
        }

        if (empty($result)) {
            // If the payload is empty, return a 204
            // https://tools.ietf.org/html/rfc7231#section-6.3.5
            $response = response('', 204);
        } else {
            $response = response()->json(
                $result,
                200,
                [],
                env('APP_DEBUG', false) ? JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES : null
            );

            if ($type === 'jsonp') {
                $response->withCallback($request->input('callback'));
                // Prevent Opera and Chrome from executing the response as anything
                // other than JSONP
                $response->headers->set('X-Content-Type-Options', 'nosniff');
            }
        }

        // Should we prevent this request from being cached?
        if (! in_array($request->method(), $this->cacheableMethods)) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        }

        $response->headers->set('Content-language', Lang::getLocale());

        return $response;
    }
}
