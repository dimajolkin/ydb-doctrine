<?php

namespace Dimajolkin\YdbDoctrine\Yql;

class YqlFixer
{
    private function distinct(string $sql)
    {
        if (str_contains($sql, 'SELECT DISTINCT')) {
            if (preg_match('/(\w+\.\w+ AS \w+)/', $sql, $math)) {
                if (count($math) !== count(array_unique($math))) {
                    $sql = str_replace($math[0] . ', ', '', $sql);
                }
            }
        }
        return $sql;
    }
    public function fixed(string $sql): string
    {
        $sql = $this->distinct($sql);

        return $sql;
    }
}
