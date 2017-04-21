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

abstract class RESTController extends Controller {

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

    public function __construct(UsecaseFactory $usecaseFactory) {
        $this->usecaseFactory = $usecaseFactory;
        // $this->middleware('oauth', ['scope:'.$this->getScope()]);
        $this->middleware('cors');
        // $this->middleware('rest', ['resource:'.$this->getResource()]);
        // @todo add 'Allow' header based on controller methods
    }

    /**
     * @var Object Request Payload
     */
    protected $_request_payload = NULL;

    /**
     * @var Object Response Payload
     */
    protected $_response_payload = NULL;

    /**
     * @var array Map of HTTP methods -> actions
     */
    // protected $_action_map = array
    // (
    //  Http_Request::POST    => 'post',   // Typically Create..
    //  Http_Request::GET     => 'get',
    //  Http_Request::PUT     => 'put',    // Typically Update..
    //  Http_Request::DELETE  => 'delete',
    //  Http_Request::OPTIONS => 'options'
    // );

    /**
     * @var array List of HTTP methods which may be cached
     */
    protected $cacheableMethods = array
    (
        Request::METHOD_GET,
    );

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

        return $this->prepResponse($this->executeUsecase(), $request);
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

        return $this->prepResponse($this->executeUsecase(), $request);
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

        return $this->prepResponse($this->executeUsecase(), $request);
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

        return $this->prepResponse($this->executeUsecase(), $request);
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

        return $this->prepResponse($this->executeUsecase(), $request);
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

        return $this->prepResponse($this->executeUsecase(), $request);
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

        return $this->prepResponse($this->executeUsecase(), $request);
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
     * @throws HTTP_Exception_400
     * @throws HTTP_Exception_403
     * @throws HTTP_Exception_404
     * @return void
     */
    protected function executeUsecase()
    {
        try
        {
            // Attempt to execute the usecase to get the response
            $responsePayload = $this->usecase->interact();

            return $responsePayload;
        }
        catch (\Ushahidi\Core\Exception\NotFoundException $e)
        {
            abort(404, $e->getMessage());
        }
        catch (\Ushahidi\Core\Exception\AuthorizerException $e)
        {
            abort(403, $e->getMessage());
        }
        catch (\Ushahidi\Core\Exception\ValidatorException $e)
        {
            // @todo Custom output view for validation exceptions
            abort(422, $e->getMessage());
            // throw new HTTP_Exception_422(
            //  'Validation Error',
            //  NULL,
            //  $e
            // );
        }
        catch (\InvalidArgumentException $e)
        {
            abort(400, "Bad request: ". $e->getMessage());
        }
    }

    /**
     * Prepare response headers and body, formatted based on user request.
     * @throws HTTP_Exception_400
     * @throws HTTP_Exception_500
     * @return void
     */
    protected function prepResponse(Array $responsePayload, Request $request)
    {
        // Add CORS headers to the response
        // $this->add_cors_headers($this->response);

        // Use JSON if the request method is OPTIONS
        if ($request->method() === Request::METHOD_OPTIONS)
        {
            $type = 'json';
        } else {
            //...Get the requested response format, use JSON for default
            // @todo should just Accept header
            $type = strtolower($request->query('format')) ?: 'json';
        }

        try
        {
            //$format = service("formatter.output.$type");

            // $body = $format($this->_response_payload);
            // $mime = $format->getMimeType();
            // $this->response->headers('Content-Type', $mime);

            if (empty($responsePayload))
            {
                // If the payload is empty, return a 204
                // https://tools.ietf.org/html/rfc7231#section-6.3.5
                $response = response('', 204);
            } else {
                $response = response()->json($responsePayload, 200, array(), env('APP_DEBUG', false) ? JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES : null);

                if ($type === 'jsonp')
                {
                    $response->withCallback($request->input('callback'));
                    // Prevent Opera and Chrome from executing the response as anything
                    // other than JSONP, see T455.
                    $response->headers->set('X-Content-Type-Options', 'nosniff');
                }
            }

            // Should we prevent this request from being cached?
            if ( ! in_array($request->method(), $this->cacheableMethods))
            {
                $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            }

            return $response;
        }
        catch (\Aura\Di\Exception\ServiceNotFound $e)
        {
            abort(400, 'Unknown response format:' . $type);
        }
        catch (\InvalidArgumentException $e)
        {
            abort(400, 'Bad formatting parameters:' . $e->getMessage());
        }
        catch (\Ushahidi\Core\Exception\FormatterException $e)
        {
            abort(500, 'Error while formatting response:' . $e->getMessage());
        }
    }
}
