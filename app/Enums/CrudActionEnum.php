<?php


namespace App\Enums;


use BenSampo\Enum\Enum;

class CrudActionEnum extends Enum
{
    public const INDEX = 'index';
    public const STORE = 'store';
    public const UPDATE = 'update';
    public const SHOW = 'show';
    public const DESTROY = 'destroy';
}
