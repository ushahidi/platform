<?php

namespace Tests\Unit\Modules\V5\Http\Resources\Media;

use Illuminate\Support\Facades\Storage;
use Ushahidi\Modules\V5\Http\Resources\Media\MediaResource;
use Ushahidi\Tests\TestCase;

class MediaResourceTest extends TestCase
{
    /**
     * Helper: call the protected resolveMediaUrl method via reflection.
     */
    private function callResolveMediaUrl(string $value): ?string
    {
        $resource = new MediaResource((object) []);
        $method = new \ReflectionMethod($resource, 'resolveMediaUrl');
        $method->setAccessible(true);
        return $method->invoke($resource, $value);
    }

    /**
     * Helper: call the protected formatOFilename method via reflection.
     */
    private function callFormatOFilename($value): ?string
    {
        $resource = new MediaResource((object) []);
        $method = new \ReflectionMethod($resource, 'formatOFilename');
        $method->setAccessible(true);
        return $method->invoke($resource, $value);
    }

    /**
     * Mock Storage::url() so it returns a URL only for the given $knownPaths.
     *
     * @param array<string, string> $knownPaths  map of storage path => returned URL
     */
    private function mockStorageUrl(array $knownPaths): void
    {
        Storage::shouldReceive('url')
            ->andReturnUsing(function (string $path) use ($knownPaths) {
                return $knownPaths[$path] ?? null;
            });
    }

    // ---------------------------------------------------------------
    //  formatOFilename: empty / null handling
    // ---------------------------------------------------------------

    public function testFormatOFilenameReturnsNullForEmptyString(): void
    {
        $this->assertNull($this->callFormatOFilename(''));
    }

    public function testFormatOFilenameReturnsNullForNull(): void
    {
        $this->assertNull($this->callFormatOFilename(null));
    }

    // ---------------------------------------------------------------
    //  1st try: raw filename found — URL returned as-is
    // ---------------------------------------------------------------

    public function testRawFilenameFoundReturnsUrlAsIs(): void
    {
        // File "uploads/photo.jpg" exists with its raw name in storage.
        // Storage returns a properly-encoded URL — no % escaping needed.
        $this->mockStorageUrl([
            'uploads/photo.jpg' => 'https://s3.example.com/uploads/photo.jpg',
        ]);

        $result = $this->callResolveMediaUrl('uploads/photo.jpg');

        $this->assertSame('https://s3.example.com/uploads/photo.jpg', $result);
    }

    public function testRawFilenameWithSpacesFoundReturnsUrlAsIs(): void
    {
        // File "uploads/some file.jpg" exists with spaces in its name.
        // S3 returns the URL with %20 already encoded — no extra escaping.
        $this->mockStorageUrl([
            'uploads/some file.jpg' => 'https://s3.example.com/uploads/some%20file.jpg',
        ]);

        $result = $this->callResolveMediaUrl('uploads/some file.jpg');

        // The %20 from S3 must NOT be escaped to %2520
        $this->assertSame('https://s3.example.com/uploads/some%20file.jpg', $result);
    }

    // ---------------------------------------------------------------
    //  2nd try: single-encoded name found — % escaped to %25
    // ---------------------------------------------------------------

    public function testSingleEncodedFallbackEscapesPercent(): void
    {
        // DB has "uploads/some file.jpg" but in storage the object is literally
        // named "some%20file.jpg" (old code used rawurlencode before saving).
        // 1st try ("uploads/some file.jpg") → not found (null).
        // 2nd try ("uploads/some%20file.jpg") → found.
        $this->mockStorageUrl([
            'uploads/some%20file.jpg' => 'https://cdn.example.com/uploads/some%20file.jpg',
        ]);

        $result = $this->callResolveMediaUrl('uploads/some file.jpg');

        // The % must be escaped to %25 so browsers don't decode %20 as a space.
        // "some%20file.jpg" → "some%2520file.jpg"
        $this->assertSame('https://cdn.example.com/uploads/some%2520file.jpg', $result);
    }

    // ---------------------------------------------------------------
    //  3rd try: double-encoded name found — % escaped to %25
    // ---------------------------------------------------------------

    public function testDoubleEncodedFallbackEscapesPercent(): void
    {
        // DB has "uploads/some file.jpg" but in storage the object is literally
        // named "some%2520file.jpg" (doubly encoded).
        // 1st try ("uploads/some file.jpg") → not found.
        // 2nd try ("uploads/some%20file.jpg") → not found.
        // 3rd try ("uploads/some%2520file.jpg") → found.
        $this->mockStorageUrl([
            'uploads/some%2520file.jpg' => 'https://cdn.example.com/uploads/some%2520file.jpg',
        ]);

        $result = $this->callResolveMediaUrl('uploads/some file.jpg');

        // % escaped to %25 in the returned URL
        $this->assertSame('https://cdn.example.com/uploads/some%252520file.jpg', $result);
    }

    // ---------------------------------------------------------------
    //  Not found in any strategy — returns null
    // ---------------------------------------------------------------

    public function testReturnsNullWhenFileNotFoundInAnyStrategy(): void
    {
        // Storage returns null for every path tried
        $this->mockStorageUrl([]);

        $result = $this->callResolveMediaUrl('uploads/missing.jpg');

        $this->assertNull($result);
    }

    // ---------------------------------------------------------------
    //  Simple filenames (no spaces or special chars)
    // ---------------------------------------------------------------

    public function testSimpleFilenameNoSpecialChars(): void
    {
        // A plain filename with no encoding concerns.
        // rawurlencode("photo.jpg") === "photo.jpg", so 1st and 2nd try
        // resolve to the same path. The 1st try should match.
        $this->mockStorageUrl([
            'uploads/photo.jpg' => 'https://s3.example.com/uploads/photo.jpg',
        ]);

        $result = $this->callResolveMediaUrl('uploads/photo.jpg');

        $this->assertSame('https://s3.example.com/uploads/photo.jpg', $result);
    }

    // ---------------------------------------------------------------
    //  Deeply nested paths
    // ---------------------------------------------------------------

    public function testNestedDirectoryPathPreserved(): void
    {
        $this->mockStorageUrl([
            'a/b/c/photo.jpg' => 'https://s3.example.com/a/b/c/photo.jpg',
        ]);

        $result = $this->callResolveMediaUrl('a/b/c/photo.jpg');

        $this->assertSame('https://s3.example.com/a/b/c/photo.jpg', $result);
    }

    // ---------------------------------------------------------------
    //  Priority: 1st try wins even if 2nd would also match
    // ---------------------------------------------------------------

    public function testFirstTryTakesPriorityOverSecond(): void
    {
        // Both raw and encoded paths exist. 1st try should win,
        // and the URL should NOT have % escaping applied.
        $this->mockStorageUrl([
            'uploads/some file.jpg'    => 'https://s3.example.com/uploads/some%20file.jpg',
            'uploads/some%20file.jpg'  => 'https://cdn.example.com/uploads/some%20file.jpg',
        ]);

        $result = $this->callResolveMediaUrl('uploads/some file.jpg');

        // 1st try URL returned as-is (no %25 escaping)
        $this->assertSame('https://s3.example.com/uploads/some%20file.jpg', $result);
    }
}
