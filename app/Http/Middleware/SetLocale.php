<?php
namespace Ushahidi\App\Http\Middleware;

use Closure;
use \Negotiation\LanguageNegotiator;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$features
     * @return mixed
     */
    public function handle($request, Closure $next, ...$features)
    {
        $langHeader = $request->header('Accept-language');
        if ($langHeader) {
            $negotiator = new LanguageNegotiator();
            $priorities = config('language.locales');
    
            /**
             * NOTE: the negotiation library does not support providing a list of ordered elements in its
             * stable version so defaulting to english is the best we can do right now without a more complex
             * custom class. I'd rather we support the willduran/negotiation library if we go down that path.
             */
            $type = $fallback = $this->getPriorityFallback($langHeader, $priorities, 'en');
            /**
             * TODO:
             * decide if we would like to work on extending willduran/negotiation and bring
             * v3.0.3 to stable so that we have getOrderedElements available if it's still
             * being maintained (or I guess fork if it is no longer mantained)
            */
            $bestLanguage = $negotiator->getBest($langHeader, $priorities);
            if ($bestLanguage) {
                $type = $bestLanguage->getType();
            }
            app('translator')->setLocale($type);
            app('translator')->setFallback($fallback);
        }
        
        return $next($request);
    }
    /**
     * Find a secondary language as fallback within our list of available lang codes without region
     * or use the default ('en' normally) if none is available.
     * When we migrate to v3 of negotiation package we can just use the orderedElements function
     * and take q=0.x into account easily.
     */
    private function getPriorityFallback($languageCode, $locales, $default)
    {
        $decision = $default;
        $splitLangCode = substr($languageCode, 0, 2);
        if (array_search($splitLangCode, $locales) > -1) {
            $decision = $splitLangCode;
        }
        return $decision;
    }
}
