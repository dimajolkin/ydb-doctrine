<?php

namespace Dimajolkin\YdbDoctrine\Yql\Parser;

class SqlParser
{
    private const ORDER_REGEX = '/(?<field>(?<table>\w+).(?<column>\w+) (?<order>ASC|DESC))/';
    private const FIELD_REGEX = '/((?<field>(?<table>\w+).(?<column>\w+) (AS|as) (?<alias>\w+))(?![^()]*+\\)))/';
    /** @var Field[]  */
    private array $fields;
    /** @var OrderField[]  */
    private array $orders;

    public function __construct(
        private string $sql,
    )
    {
        $this->fields = $this->parseFields();
        $this->orders = $this->parseOrders();
    }

    private function parseFields(): array
    {
        $list = [];
        preg_match_all(self::FIELD_REGEX, $this->sql, $match, PREG_SET_ORDER);
        foreach ($match as $item) {
            if ($item['field']) {
                $list[] = new Field($item['table'], $item['column'], $item['alias']);
            }
        }
        return $list;
    }

    private function findFieldByTableNameAndColumn(string $tableName, string $columnName): ?Field
    {
        foreach ($this->fields as $field) {
            if ($field->tableName === $tableName && $field->columnName === $columnName) {
                return $field;
            }
        }

        return null;
    }

    private function parseOrders(): array
    {
        $list = [];
        preg_match_all(self::ORDER_REGEX, $this->sql, $math, PREG_SET_ORDER);
        foreach ($math as $item) {
            $field = $this->findFieldByTableNameAndColumn($item['table'], $item['column']);
            if ($field) {
                $list[] = new OrderField($field, $item['order']);
            }
        }

        return $list;
    }

    public function replaceOrder(OrderField $order): void
    {
        $replace = $order->field->getKey();
        $this->sql = preg_replace(self::ORDER_REGEX, $replace . ' ' . $order->order, $this->sql);
    }

    public function fetchFields(): array
    {
        return $this->fields;
    }

    public function fetchOrders(): array
    {
        return $this->orders;
    }

    public function getSql(): string
    {
        return $this->sql;
    }
}
