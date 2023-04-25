<?php

namespace App\Enums;

enum ActionRequestEnum: string
{
    case APPROVED = "approved";
    case DECLINED = "declined";
    case PENDING = "pending";
    case REQUEST_CREATE = "create";
    case REQUEST_UPDATE = "update";
    case REQUEST_DELETE = "delete";
}
