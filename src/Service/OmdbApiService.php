<?php

namespace App\Service;

class OmdbApiService
{

    /**
     * @return bool|string
     */
    public function getAllSpaceMovies()
    {
        return $this->callAPI("GET", "http://www.omdbapi.com/?s=space&apikey=ceda12d7");
    }

    /**
     * @param $id
     * @return bool
     */
    public function checkIfAMovieExistsById($id)
    {
        $response = $this->callAPI("GET", "http://www.omdbapi.com/?apikey=ceda12d7&i=" . $id);
        $responseJSON = json_decode($response, true);
        $exists = $responseJSON['Response'] == 'True' ? true : false;
        return $exists;
    }

    /**
     * @param $type
     * @param $url
     * @return bool|string
     */
    private function callAPI($type, $url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Cookie: __cfduid=d4ff25bd3f89afba769679b3c0f9bec981570026554",
                "Host: www.omdbapi.com",
                "Postman-Token: 48c37314-822b-49b8-af9b-4679db54704d,c98ca524-6039-44e9-8b19-bb1365593877",
                "User-Agent: PostmanRuntime/7.17.1",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}