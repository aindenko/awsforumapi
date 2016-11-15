<?php
namespace AppBundle\Controller;
use AppBundle\Annotation\JsonResponse;
use AppBundle\Entity\Token;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    /**
     * @Route("auth")
     * @Method({"POST"})
     * @JsonResponse(serializerGroups={"detail"})
     */
    public function postAuthAction(Request $request)
    {
        $login = $request->get('login');
        $password = $request->get('password');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('login'=>$login));
        if(!password_verify($password, $user->getPasswordHash())) {
            throw new HttpException('400', 'Incorrect password');
        }
        //generate APiKey and write to table
        $now = new \DateTime();
        $expired = new \DateTime("+10 minutes");
        $token = $this->getDoctrine()->getRepository('AppBundle:Token')->findOneBy(array('user'=>$user));
        if($now > $token->getExpiredAt()){
            $token->setHash(sha1($user->getPasswordHash().time()))
                ->setExpiredAt($expired);
            $em = $this->get('doctrine')->getManager();
            $em->persist($token);
            $em->flush();
        }
        return array('X-Auth'=> $token->getHash());
    }
}
