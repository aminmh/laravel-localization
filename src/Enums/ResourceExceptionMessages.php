<?php

namespace Bugloos\LaravelLocalization\Enums;

enum ResourceExceptionMessages: string
{
    case NOT_FOUND = "The %s not found!";
    case ADD_FAILED = "The creation of %s failed!";
    case DELETE_FAILED = "Failed remove!";
    case UPDATE_FAILED = "Update of %s failed!";
    case FAILED_TRANSLATION = "Label %s from category %s not translate in %s language!";
    case BULK_TRANSLATION = "The bulk translation failed!";
}
