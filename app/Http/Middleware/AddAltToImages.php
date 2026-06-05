<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AddAltToImages
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Response) {
            $contentType = $response->headers->get('Content-Type');
            if ($contentType && str_contains($contentType, 'text/html')) {
                $originalHtml = $response->getContent();
                $html = $this->stripByteOrderMarkers($originalHtml);
                if ($html !== $originalHtml) {
                    $response->setContent($html);
                }
                if (stripos($html, '<img') !== false) {
                    libxml_use_internal_errors(true);
                    $dom = new \DOMDocument('1.0', 'UTF-8');
                    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

                    $title = '';
                    $titleNodes = $dom->getElementsByTagName('title');
                    if ($titleNodes->length > 0) {
                        $title = trim($titleNodes->item(0)->textContent);
                    }
                    if ($title === '') {
                        $title = ucfirst(trim($request->path(), '/')) ?: config('app.name', 'Gamun');
                    }

                    $imgs = $dom->getElementsByTagName('img');
                    foreach ($imgs as $img) {
                        $alt = $img->getAttribute('alt');
                        if ($alt === null || trim($alt) === '') {
                            $src = $img->getAttribute('src');
                            $fallback = $title;
                            if ($src) {
                                $basename = pathinfo(parse_url($src, PHP_URL_PATH) ?? '', PATHINFO_FILENAME);
                                if ($basename) {
                                    $fallback = str_replace(['-', '_'], ' ', $basename);
                                }
                            }
                            $img->setAttribute('alt', $fallback);
                        }
                    }

                    $newHtml = $dom->saveHTML();
                    $response->setContent($newHtml);
                    libxml_clear_errors();
                }
            }
        }

        return $response;
    }

    private function stripByteOrderMarkers(string $html): string
    {
        // DOMDocument turns BOM text before <html> into visible body markup.
        return str_replace(["\xEF\xBB\xBF", "\u{FEFF}", '&#65279;', '&ZeroWidthNoBreakSpace;'], '', $html);
    }
}
