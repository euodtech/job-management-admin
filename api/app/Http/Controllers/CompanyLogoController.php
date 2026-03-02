<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyLogoController extends Controller
{
    public function show($filename)
    {
        // Validate filename: only allow alphanumeric, dots, hyphens, underscores
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
            return response()->json(['Success' => false, 'Message' => 'Invalid filename'], 400);
        }

        $logoDir = env('COMPANY_LOGO_DIR') ?: __DIR__ . '/../../../../assets/dist/img/company_logo';
        $path = realpath($logoDir . '/' . $filename);

        // Ensure resolved path is still within the expected directory (prevent traversal)
        $baseDir = realpath($logoDir);
        if ($path === false || $baseDir === false || strpos($path, $baseDir) !== 0) {
            return response()->json(['Success' => false, 'Message' => 'File not found'], 404);
        }

        if (!is_file($path)) {
            return response()->json(['Success' => false, 'Message' => 'File not found'], 404);
        }

        $mimeTypes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'bmp'  => 'image/bmp',
        ];

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $contentType = isset($mimeTypes[$ext]) ? $mimeTypes[$ext] : 'application/octet-stream';

        return response(file_get_contents($path), 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
