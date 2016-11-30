<?php
namespace AppBundle\Controller;
use AppBundle\Annotation\JsonResponse;
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
        if(!$login || !$password){
            throw new HttpException('400', 'Please check input parameters');
        }
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('login'=>$login));
        if(!password_verify($password, $user->getPasswordHash())) {
            $this->get('logger')->warning('Incorrect password for user ' . $login);
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

            $this->get('logger')->debug('Api key regenerated for ' . $login);
        }
        return array('X-Auth'=> $token->getHash());
    }
}
