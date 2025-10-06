<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImportService
{
    public function __construct(
        private HttpClientInterface $client,
    ){}

    public function importData(): string
    {
        $token = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IjFFUVBZQWZQaTZzZHpyVFNrV1dBTCJ9.eyJodHRwczovL2JhbGx0aW1lLmNvL2VtYWlsIjoibDNmdGg0bmRAZ29vZ2xlbWFpbC5jb20iLCJpc3MiOiJodHRwczovL2F1dGguYmFsbHRpbWUuY29tLyIsInN1YiI6ImF1dGgwfDY4ZGU5MzRjNWQ3NmE2YWI5MzRlM2RiNiIsImF1ZCI6WyJodHRwczovL2JhY2tlbmQuYmFsbHRpbWUuY28iLCJodHRwczovL2Rldi1yNmw5MzJyMi51cy5hdXRoMC5jb20vdXNlcmluZm8iXSwiaWF0IjoxNzU5NjA3OTI2LCJleHAiOjE3NjIxOTk5MjYsInNjb3BlIjoib3BlbmlkIHByb2ZpbGUgZW1haWwiLCJhenAiOiIzdnZXQ3ZUVG92U1JudVkwRldpQWptRlgxZWc4a05VaiJ9.n1J4LcFeIpZvsMclhe388z15X8DBuiJt2dqtjHsqa60t1y-uflxKTsfiUl8Zc9JSyBYlJdgW-ECRXYwPgJmkgySwvpxJjMDIxQRL3n5Iy_-a64xrljCowEVN-mGvVr7sjX44faXG0GIfiXdCaSZL0UEuVkRsyy0w0PxfVfwCxKXc-DXGMECLRZIE2LD4OngD0AtwlrlJhSmzTvczkC_1bWWgRfYqf6SG76_rQmzAuyfp2NwgkA1bhMFR9EH7PKRakCY3ZBBNUysv43BhuRcPuap6uOjQ2nrDRQtHBVDBO2W5srre9vp3cbh55tTau4ePDYoHQsmFTxUxa1OEAYPIvw';

        $bearerToken = "Bearer $token";

        $jsonBody = '{
  "video_ids": [
    "7029cad5-7da8-3b14-bea4-6ab188a68a00"
  ],
  "filters": {
    "firstBallSideOut": false
  }
}';


        $response = $this->client->request(
            method: 'POST',
            url: 'https://backend.balltime.com/generate-multi-video-stats?',
            options: [
                'auth_bearer'=>$token,
                'body'=> $jsonBody,
                'headers' => [
                    'Cache-Control' => 'no-cache',
                    'Content-Type' => 'application/json',
                    'Content-Length' => strlen($jsonBody),
                ],
            ],
        );

        var_dump($response->getStatusCode());
        return $response->getContent(false);
    }
}

