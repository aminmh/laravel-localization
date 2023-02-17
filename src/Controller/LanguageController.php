<?php

namespace Bugloos\LaravelLocalization\Controller;

use App\Http\Controllers\Controller;
use Bugloos\LaravelLocalization\Facades\LocalizationFacade;
use Bugloos\LaravelLocalization\Models\Language;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LanguageController extends Controller
{

    public function languages(Request $request): JsonResponse
    {
    }

    /**
     * @throws FileNotFoundException
     */
    public function flag(string $locale): BinaryFileResponse
    {
        $path = LocalizationFacade::flagPath($locale);

        $mimeType = pathinfo($path, PATHINFO_EXTENSION);

        if (file_exists($path)) {
            return response()->file($path, ["Content-Type: image/{$mimeType}"]);
        }

        throw new FileNotFoundException(sprintf("There are no any flag for given locale %s", $locale), 404);
    }
}
