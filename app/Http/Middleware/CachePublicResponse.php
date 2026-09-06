<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menambahkan HTTP caching (ETag + Cache-Control) pada response publik.
 *
 * ETag dibangkitkan dari konten response. Jika browser mengirim
 * If-None-Match yang cocok, server membalas 304 Not Modified sehingga
 * body tidak perlu dikirim ulang — menghemat bandwidth untuk halaman
 * yang jarang berubah seperti artikel berita.
 */
class CachePublicResponse
{
    public function handle(Request $request, Closure $next, string $maxAge = '300'): Response
    {
        $response = $next($request);

        // Hanya cache GET/HEAD yang sukses dan bukan untuk user terautentikasi
        if (! in_array($request->method(), ['GET', 'HEAD'])
            || ! $response->isSuccessful()
            || $request->user()) {
            return $response;
        }

        $maxAge = (int) $maxAge;

        // Gunakan ETag kustom jika controller menyediakannya (lebih stabil),
        // jika tidak, fallback ke hash konten.
        if (! $response->headers->has('ETag')) {
            $response->setEtag('"'.md5($response->getContent()).'"');
        }

        $response->setPublic();
        $response->setMaxAge($maxAge);
        $response->headers->set('Cache-Control', "public, max-age={$maxAge}");

        // Balas 304 jika cache browser masih valid
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $response;
    }
}
