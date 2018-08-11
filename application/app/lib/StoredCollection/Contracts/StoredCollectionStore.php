<?php
namespace StoredCollection\Contracts;

use StoredCollection\StoredCollection;

interface StoredCollectionStore {

    public function store(StoredCollection $storedCollection);
    public function readByKey($key);
    public function readAllByModel($model);
}