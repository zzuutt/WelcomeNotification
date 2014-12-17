<?php
/*************************************************************************************/
/*      Module WelcomeNotification pour Thelia                                         */
/*                                                                                   */
/*      Copyright (©)                                               */
/*      email : zzuutt34@free.fr                                         */
/*                                                                                   */
/*                                                         test utf-8 ä,ü,ö,ç,é,â,µ  */
/*************************************************************************************/

namespace WelcomeNotification;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Model\ConfigQuery;
use Thelia\Module\BaseModule;
use Thelia\Install\Database;

class WelcomeNotification extends BaseModule
{
    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con);

        $database->insertSql(null, [__DIR__ . '/Config/insert.sql']);

    }
}
