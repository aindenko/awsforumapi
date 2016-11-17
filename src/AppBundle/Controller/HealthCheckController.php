<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation\JsonResponse;

class HealthCheckController extends Controller
{
    /**
     * @Route("/_healthcheck")
     * @JsonResponse(200)
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return ['status'=>'I`m alive'];
    }
}
