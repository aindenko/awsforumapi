<?php

namespace AppBundle\Controller;

use AppBundle\Annotation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostController extends Controller
{
    /**
     * @Route("posts")
     * @Method({"GET"})
     * @JsonResponse(serializerGroups={"list"})
     */
    public function getPostsAction(Request $request)
    {
        $totalViews = $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getTotalViews();
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getPosts();
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
                'totalViews' => $totalViews['views']
            ),
            'posts' => $paginator->getIterator()->getArrayCopy(),
        );
        return $results;
    }

    /**
     * @Route("posts")
     * @Method({"POST"})
     * @JsonResponse(serializerGroups={"detail"})
     */
    public function postPostAction(Request $request)
    {
        $imageData = $request->get('image_data');
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        file_put_contents($path,base64_decode($imageData));

        $s3Data = array(
            'ACL' => 'public-read',
            'Body' => base64_decode($imageData),
            'Bucket' => 'rjbucketvloop',
            'Key' => 'blabla.png'
        );

        $object = $this->container->get('aws.s3')->PutObject($s3Data);

        $path_new = $object['ObjectURL'];

       $response = new Response(file_get_contents($path_new));
       $response->headers->set('Content-Type', 'image/png');
        return $response;
    }


    /**
     * @Route("post/{id}")
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

    /**
     * @Route("post/{id}/view")
     * @Method({"PUT"})
     * @JsonResponse()
     */
    public function putPostViewAction($id, Request $request)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        if(!$post){
            throw new HttpException('404', 'Post not found');
        }
        $post->setViews($post->getViews()+1);

        $em = $this->get('doctrine')->getManager();
        $em->persist($post);
        $em->flush();
        return '';
    }
}
