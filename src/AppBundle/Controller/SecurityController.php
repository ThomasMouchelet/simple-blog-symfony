<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 31/03/2017
 * Time: 16:55
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SecurityController extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }

}