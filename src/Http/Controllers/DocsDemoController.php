<?php

namespace Nyumbani\Essentials\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nyumbani\Essentials\Docs\DocsRegistry;
use Throwable;

class DocsDemoController
{
    /**
     * Run an interactive demo of a package's docs with what the reader typed
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function __invoke(Request $request, DocsRegistry $registry, string $package, string $demo): JsonResponse
    {
        //  Only a demo a package registered can run
        $docs = $registry->find($package);
        abort_unless($docs?->hasDemo($demo), 404);

        //  Keep the typed values only, as strings
        $input = collect($request->json()->all())
            ->filter(fn ($value) => is_scalar($value))
            ->map(fn ($value) => (string) $value)
            ->all();

        //  Answer with the rows, or with why the demo could not run
        try {
            return response()->json(['rows' => $docs->runDemo($demo, $input)]);
        } catch (Throwable $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        }
    }
}
