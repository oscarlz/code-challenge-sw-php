<?php

interface ConnectorInferface 
{
    public function run();

    public function lastInsertID(): int;

    public function affectedRows(): int;

	public function getBindParamsTypes($args): string;
}