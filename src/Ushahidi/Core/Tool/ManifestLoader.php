<?php

namespace Ushahidi\Core\Tool;

use Symfony\Component\Yaml\Yaml;
use JsonMapper;

class ManifestLoader
{

    public function loadManifestFromFile(string $path)
    {
        // Check that file exists

        // Check that the size of the file is not crazy

        // Get file contents
        $contents = file_get_contents($path);
        if (!$contents) {
            // raise exception
        }

        // Try to guess from file extension whether we are dealing with yaml or json
        $pathinfo = pathinfo($path);
        if (preg_match('/^ya?ml$/i', $pathinfo['extension'])) {
            // Should be yaml formatted
            $contents_obj = Yaml::parse($contents);
            if (!$contents_obj) {
                // raise exception
                return false;
            }
        } elseif (preg_match('/^json$/i', $pathinfo['extension'])) {
            // Should be json formatted
            $contents_obj = json_decode($contents);
            if (!$contents_obj) {
                // raise exception
                return false;
            }
        } else {
            // Who knows what's in the file? what are we going to do? guess?
            // raise exception
            return false;
        }

        // enforce lower case keys at the root level
        $contents_obj = array_change_key_case($contents_obj, CASE_LOWER);

        return $this->parseManifestArray($contents_obj);
    }

    public function parseManifestArray(array $manifest)
    {
        // look for required keys
        $namespace = $manifest['namespace'];
        $kind = $manifest['kind'];

        if (!$namespace) {
            // raise exception -- missing namescape
        }
        if (!$kind) {
            // raise exception -- missing keys
        }
        if (!array_key_exists('spec', $manifest)) {
            // raise exception -- missing spec
        }

        // look up the target class (based on namespace and kind)
        // TODO: somehow add versioning to this
        $namespace = trim($namespace, '/');
        $target_class = str_replace("/", '\\', $namespace) . '\\ManifestSchemas\\' . $kind;

        // run the mapper
        $mapper = new JsonMapper();
        $spec = json_decode(json_encode($manifest['spec']));
        $manifest = $mapper->map($spec, new $target_class());

        return $manifest;
    }
}
