<?php

namespace Dimajolkin\YdbDoctrine\Parser;

class YdbUriParser
{
    public function parse(string $url): array
    {
        $data = parse_url($url);
        if ($data['scheme'] !== 'ydb') {
            throw new \Exception();
        }

        $endpoint = $data['host'] ?? throw new \Exception();
        if (isset($data['port'])) {
            $endpoint .= ':' . $data['port'];
        }

        $query = [];
        parse_str($data['query'], $query);
        array_walk_recursive($query, fn (&$value) => match ($value) {
            'true' => $value = true,
            'false' => $value = false,
        });

        return [
            'database'    => $data['path'],
            'endpoint'    => $endpoint,
            'discovery'   => false,
        ] + $query;
    }
}
