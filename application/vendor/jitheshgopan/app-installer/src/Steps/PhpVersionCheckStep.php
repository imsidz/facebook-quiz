<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 02/03/15
 * Time: 11:56 PM
 */

namespace Jitheshgopan\AppInstaller\Steps;


class PhpVersionCheckStep extends VersionCheckStep{

    public function check($version, $comparisonOperator = '>='){
        return $this->checkVersion('php', $version, $comparisonOperator);
    }
}