<?php

namespace AppBundle\Controller;

use AppBundle\Annotation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostController extends Controller
{
    /**
     * @Route("/v1/posts")
     * @Method({"GET"})
     * @JsonResponse(serializerGroups={"list"})
     */
    public function getPostsAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getPosts(array(),array());
        $limit = (int) $request->query->get('limit', 10);
        $offset = (int) $request->query->get('offset', 0);

        $posts->setFirstResult($offset);
        $posts->setMaxResults($limit);

        $paginator = new Paginator($posts, false);

        $results = array(
            '_metadata' => array(
                'totalCount' => count($paginator),
                'limit' => $limit,
                'offset' => $offset,
            ),
            'posts' => $paginator->getIterator()->getArrayCopy(),
        );
        return $results;
    }

    /**
     * @Route("/v1/post/{id}")
     * @Method({"GET"})
     * @JsonResponse(serializerGroups={"detail"})
     */
    public function getPostAction($id, Request $request)
    {

        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        if(!$post){
            throw new HttpException('404', 'Post not found');
        }
        return $post;
    }
}
