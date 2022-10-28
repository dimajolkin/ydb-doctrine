<?php

namespace Dimajolkin\YdbDoctrine\Yql;

use Dimajolkin\YdbDoctrine\Yql\Parser\SqlParser;

class YqlFixer
{
    private function orderBy(string $sql)
    {
        if (str_contains($sql, 'SELECT') && str_contains($sql, 'ORDER BY')) {
            $parser = new SqlParser($sql);
            foreach ($parser->fetchOrders() as $order) {
                $parser->replaceOrder($order);
            }
            return $parser->getSql();
        }

        return $sql;
    }

    public function fixed(string $sql): string
    {
        $sql = $this->orderBy($sql);

        return $sql;
    }
}
