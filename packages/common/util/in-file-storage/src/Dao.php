<?php

namespace Phoenix\Util\InFileStorage;

interface Dao
{
    public function put(int $id, $value): bool;
    public function get(int $id);
    public function getFromEnd(int $id);
    public function remove(int $id): bool;
    public function getIterator(): iterable;
    public function getReverseIterator(): iterable;
    public function nextId(): int;
}
