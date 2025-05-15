<?php

// This is a handy class for quick conversion of PHP arrays objects to JSON.
class ArrayValue implements JsonSerializable {
    private $array;
    public function __construct(array $array) {
        $this->array = $array;
    }

    public function jsonSerialize(): mixed {
        return $this->array;
    }
}

?>