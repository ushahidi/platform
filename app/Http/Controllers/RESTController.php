<?php

/**
 * Ushahidi REST Base Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Http\Controllers;

use Ushahidi\Factory\UsecaseFactory;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuth2Exception;
use League\OAuth2\Server\Exception\MissingAccessTokenException;
use Ushahidi\App\Exceptions\ValidationException;

abstract class RESTController extends Controller
{

    /**
     * @var Current API version
     * @todo  move to config?
     */
    protected static $version = '3';

    /**
     * @var Ushahidi\Factory\UsecaseFactory
     */
    protected $usecaseFactory;

    /**
     * @var Ushahidi\Core\Usecase
     */
    protected $usecase;

    public function __construct(UsecaseFactory $usecaseFactory)
    {
        $this->usecaseFactory = $usecaseFactory;
    }

    /**
     * @var array List of HTTP methods which may be cached
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

    /**
     * Get an API URL for a resource.
     * @param  string  $resource
     * @param  mixed   $id
     * @return string
     * @todo  move this somewhere sensible
     */
    public static function url($resource, $id = null)
    {
        $template = 'api/v%d/%s';
        if (!is_null($id)) {
            $template .= '/%s';
        }
        return rtrim(sprintf($template, static::version(), $resource, $id), '/');
    }

    /**
     * Get options for a resource collection.
     *
     * OPTIONS /api/foo
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public function store(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'create')
            ->setPayload($request->json()->all());

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    /**
     * Retrieve All Entities
     *
     * GET /api/foo
     *
     * @return void
     */
    public function index(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
            ->setFilters($request->query());

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    /**
     * Retrieve An Entity
     *
     * GET /api/foo/:id
     *
     * @return void
     */
    public function show(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'read')
            ->setIdentifiers($this->getRouteParams($request));

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    /**
     * Update An Entity
     *
     * PUT /api/foo/:id
     *
     * @return void
     */
    public function update(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'update')
            ->setIdentifiers($this->getRouteParams($request))
            ->setPayload($request->json()->all());

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    /**
     * Delete An Entity
     *
     * DELETE /api/foo/:id
     *
     * @return void
     */
    public function destroy(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'delete')
            ->setIdentifiers($this->getRouteParams($request));

        return $this->prepResponse($this->executeUsecase($request), $request);
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
        return $request->route()[2];
    }

    /**
     * Execute the usecase that the controller prepared.
     *
     * @todo  should this take Usecase as a param rather than use $this->usecase?
     *
     * @throws HTTP_Exception_400
     * @throws HTTP_Exception_403
     * @throws HTTP_Exception_404
     * @return void
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
            if (!$request->headers->has('Authorization')) {
                throw abort(
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
            abort(400, "Bad request: ". $e->getMessage());
        }
    }

    /**
     * Prepare response headers and body, formatted based on user request.
     * @throws HTTP_Exception_400
     * @throws HTTP_Exception_500
     * @return void
     */
    protected function prepResponse(array $responsePayload = null, Request $request)
    {
        // Use JSON if the request method is OPTIONS
        if ($request->method() === Request::METHOD_OPTIONS) {
            $type = 'json';
        } else {
            //...Get the requested response format, use JSON for default
            // @todo should just Accept header
            $type = strtolower($request->query('format')) ?: 'json';
        }

        if (empty($responsePayload)) {
            // If the payload is empty, return a 204
            // https://tools.ietf.org/html/rfc7231#section-6.3.5
            $response = response('', 204);
        } else {
            $response = response()->json(
                $responsePayload,
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

        return $response;
    }
}
