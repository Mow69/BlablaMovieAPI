<?php


namespace App\Service\Movie;


use App\Controller\MovieController;
use App\Model\Movie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class MovieService
{
    private $serializer;


    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    final public function deserialise($response)
    {

        $this->response = $response;
        $desresponse = $this->serializer->deserialize($response, Movie::class,'json');
        return $desresponse;
    }
}