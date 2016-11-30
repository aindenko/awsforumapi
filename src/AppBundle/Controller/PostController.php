<?php

namespace AppBundle\Controller;

use AppBundle\Annotation\JsonResponse;
use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use ZipArchive;

class PostController extends Controller
{
    /**
     * @Route("posts")
     * @Method({"GET"})
     * @JsonResponse(serializerGroups={"list"})
     */
    public function getPostsAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getPosts();
        $limit = (int) $request->query->get('limit', 10);
        $offset = (int) $request->query->get('offset', 0);

        $posts->setFirstResult($offset);
        $posts->setMaxResults($limit);

        $paginator = new Paginator($posts, false);

        //Increment views
        $returnPosts = $paginator->getIterator()->getArrayCopy();

        foreach ($returnPosts as $post){
            $post->setViews($post->getViews()+1);

            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush($post);
        }
        $totalViews = $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getTotalViews();

        $results = array(
            '_metadata' => array(
                'totalCount' => count($paginator),
                'limit' => $limit,
                'offset' => $offset,
                'totalViews' => $totalViews['views']
            ),
            'posts' => $returnPosts
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
        $title = $request->get('title');
        $imageData = $request->get('image_data');
        if(!$imageData){
            throw new HttpException('400', 'Please check input parameters');
        }
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        file_put_contents($path,base64_decode($imageData));

        $filename = sha1($imageData . time());
        $s3BucketFull = 'rjbucketvloop';
        $s3BucketThumb = 'rjbucketvloop';
        $s3Data = array(
            'ACL' => 'public-read',
            'Body' => base64_decode($imageData),
            'Bucket' => $s3BucketFull,
            'Key' => $filename.'.jpg'
        );

        $object = $this->container->get('aws.s3')->PutObject($s3Data);
        if(!$object) {
            $this->get('logger')->error('S3 error');
            throw new HttpException('500', 'Internal server error');
        }

        $pathFull = $object['ObjectURL'];
        $pathThumb = str_replace($s3BucketFull, $s3BucketThumb, $pathFull);

        $post = new Post();
        $post->setTitle($title);
        $post->setImageFullUrl($pathFull);
        $post->setImageThumbUrl($pathThumb);
        $post->setCreatedAt(new \DateTime());

        $em = $this->get('doctrine')->getManager();
        $em->persist($post);
        $em->flush();
        return $post;
    }


    /**
     * @Route("posts/{id}", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @JsonResponse(serializerGroups={"detail"})
     */
    public function getPostAction($id, Request $request)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        if(!$post){
            throw new HttpException('404', 'Post not found');
        }
        $post->setViews($post->getViews()+1);

        $em = $this->get('doctrine')->getManager();
        $em->persist($post);
        $em->flush();
        return $post;
    }

    /**
     * @Route("posts/{id}/view")
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

    /**
     * @Route("posts/{id}")
     * @Method({"DELETE"})
     * @JsonResponse(200)
     */
    public function deletePostAction($id, Request $request)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        if(!$post){
            throw new HttpException('404', 'Post not found');
        }

        //delete from S3 buckets
        $s3BucketFull = 'rjbucketvloop';
        $s3BucketThumb = 'rjbucketvloop';

        $s3Data = array(
            'Bucket' => $s3BucketFull,
            'Key' => basename($post->getImageFullUrl())
        );
        $this->container->get('aws.s3')->PutObject($s3Data);
        $s3Data = array(
            'Bucket' => $s3BucketThumb,
            'Key' => basename($post->getImageFullUrl())
        );
        //Replace with cascade Lambda
        $this->container->get('aws.s3')->PutObject($s3Data);
        $em = $this->get('doctrine')->getManager();
        $em->remove($post);
        $em->flush();


        return $post;
    }

    /**
     * @Route("posts/export")
     * @Method({"GET"})
     */
    public function exportPostsAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();

        $columns = array('Title', 'Image');
        $handle = tmpfile();
        $metaDatas = stream_get_meta_data($handle);
        $tmpFile = $metaDatas['uri'];
        fputcsv($handle, $columns, ',');
        $zip = new \ZipArchive();
        $zipName = 'Export-'.time().".zip";
        $zip->open($zipName,  \ZipArchive::CREATE);
        foreach ($posts as $post){

            $fileRow = array(
                $post->getTitle(),
                $post->getImageFullUrl()
            );
            fputcsv($handle, $fileRow, ',');
            //Replace with Lambda and add to zip every uploaded file
            $zip->addFromString('img/'.basename($post->getImageFullUrl()), file_get_contents($post->getImageFullUrl()));
        }
        $zip->addFromString('posts.csv',  file_get_contents($tmpFile));
        $zip->close();
        $response = new Response();
        $response->headers->set('Content-type', 'application/zip');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $zipName));
        $response->headers->set('Content-Length', filesize($zipName));
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent(file_get_contents($zipName));

        return $response;

    }


}
