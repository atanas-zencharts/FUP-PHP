<?php

namespace app\models;

use app\models\WalletHistoryTypes;

class WalletHistoryTypesBL extends WalletHistoryTypes
{
    const TYPE_ADD = 1;
    const TYPE_WITHDRAWAL = 2;
    const TYPE_BUY = 3;
    const TYPE_SELL = 4;
}