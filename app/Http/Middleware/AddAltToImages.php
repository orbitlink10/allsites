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
                    $title = $this->extractTitle($html);
                    if ($title === '') {
                        $title = ucfirst(trim($request->path(), '/')) ?: config('app.name', 'Gamun');
                    }

                    $html = preg_replace_callback('/<img\b[^>]*>/i', function ($matches) use ($title) {
                        $tag = $matches[0];
                        if (preg_match('/\salt\s*=/i', $tag)) {
                            return $tag;
                        }

                        $fallback = $title;
                        if (preg_match('/\ssrc\s*=\s*(["\'])(.*?)\1/i', $tag, $srcMatch) || preg_match('/\ssrc\s*=\s*([^\s>]+)/i', $tag, $srcMatch)) {
                            $src = $srcMatch[2] ?? $srcMatch[1] ?? '';
                            $basename = pathinfo(parse_url($src, PHP_URL_PATH) ?? '', PATHINFO_FILENAME);
                            if ($basename) {
                                $fallback = str_replace(['-', '_'], ' ', $basename);
                            }
                        }

                        return preg_replace('/\s*\/?>$/', ' alt="' . e($fallback) . '">', $tag) ?? $tag;
                    }, $html) ?? $html;

                    $response->setContent($html);
                }
            }
        }

        return $response;
    }

    private function extractTitle(string $html): string
    {
        if (preg_match('/<title\b[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            return trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return '';
    }

    private function stripByteOrderMarkers(string $html): string
    {
        // DOMDocument turns BOM text before <html> into visible body markup.
        return str_replace(["\xEF\xBB\xBF", "\u{FEFF}", '&#65279;', '&ZeroWidthNoBreakSpace;'], '', $html);
    }
}
