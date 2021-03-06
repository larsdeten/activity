<?php

declare(strict_types=1);

namespace Sajya\Server;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Sajya\Server\Exceptions\MethodNotFound;
use Sajya\Server\Http\Parser;
use Sajya\Server\Http\Request;
use Sajya\Server\Http\Response;

class Guide
{
    protected const DEFAULT_DELIMITER = '@';

    /**
     * Stores all available RPC commands.
     *
     * @var Collection
     */
    protected Collection $map;

    /**
     * Stores delimiter
     *
     * @var string
     */
    protected string $delimiter;

    /**
     * Guide constructor.
     *
     * @param string[]    $procedures
     * @param null|string $delimiter
     */
    public function __construct(array $procedures = [], ?string $delimiter = self::DEFAULT_DELIMITER)
    {
        $this->map = collect($procedures)
            ->each(fn (string $class) => abort_unless(
                is_subclass_of($class, Procedure::class),
                500,
                "Class '$class' must extends ".Procedure::class
            ));
        $this->delimiter = $delimiter ?? self::DEFAULT_DELIMITER;
    }

    /**
     * @param string $content
     *
     * @return Response[]|Response
     */
    public function handle(string $content = '')
    {
        $parser = new Parser($content);

        $result = collect($parser->makeRequests())
            ->map(
                fn ($request) => $request instanceof Request
                    ? $this->handleProcedure($request, $request->isNotification())
                    : $this->makeResponse($request)
            )
            ->reject(fn (Response $response) => $response->isNotification())
            ->values();

        return $parser->isBatch()
            ? $result->all()
            : $result->first();
    }

    /**
     * @param mixed        $result
     * @param Request|null $request
     *
     * @return Response
     */
    public function makeResponse($result = null, Request $request = null): Response
    {
        $request ??= new Request();

        return tap(
            new Response(),
            fn (Response $response) => $response->setId($request->getId())
            ->setVersion($request->getVersion())
            ->setResult($result)
        );
    }

    /**
     * @param Request $request
     * @param bool    $notification
     *
     * @return Response
     */
    public function handleProcedure(Request $request, bool $notification): Response
    {
        \request()->replace($request->getParams()->toArray());

        $procedure = $this->findProcedure($request);

        if ($procedure === null) {
            return $this->makeResponse(new MethodNotFound(), $request);
        }

        $result = $notification
            ? HandleProcedure::dispatchAfterResponse($procedure, $request)
            : HandleProcedure::dispatchNow($procedure, $request);

        return $this->makeResponse($result, $request);
    }

    /**
     * @param Request $request
     *
     * @return null|string
     */
    public function findProcedure(Request $request): ?string
    {
        $class = Str::beforeLast($request->getMethod(), $this->delimiter);
        $method = Str::afterLast($request->getMethod(), $this->delimiter);

        return $this->map
            ->filter(fn (string $procedure) => $this->getProcedureName($procedure) === $class)
            ->filter(fn (string $procedure) => $this->checkExistPublicMethod($procedure, $method))
            ->map(fn (string $procedure)    => Str::finish($procedure, '@'.$method))
            ->first();
    }

    /**
     * @param string $procedure
     * @param string $method
     *
     * @return bool
     */
    private function checkExistPublicMethod(string $procedure, string $method): bool
    {
        return (new ReflectionMethod($procedure, $method))->isPublic();
    }

    /**
     * @param string $procedure
     *
     * @return string
     */
    private function getProcedureName(string $procedure): string
    {
        return (new ReflectionClass($procedure))->getStaticPropertyValue('name');
    }
}
