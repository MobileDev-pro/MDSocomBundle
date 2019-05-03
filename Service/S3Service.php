<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 25/04/19
 * Time: 10:42
 */

namespace MD\SocomBundle\Service;

use Aws\Result;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class S3Service
{
    /**
     * @var S3Client
     */
    private $client;

    /**
     * @var string
     */
    private $bucket;

    /**
     * Uploader constructor.
     * @param S3Client $client
     * @param string $awsBucket
     */
    public function __construct(S3Client $client, string $awsSocomBucket)
    {
        $this->client = $client;
        $this->bucket = $awsSocomBucket;
    }

    /**
     * @return S3Client
     */
    protected function getClient(): S3Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    protected function getBucket(): string
    {
        return $this->bucket;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key): Response
    {
        try {
            $result = $this->client->getObject(array(
                'Bucket' => $this->bucket,
                'Key'    => $key
            ));
        } catch (\Exception $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return new Response($result['Body'], 200, array(
            'Content-Type' => $result['ContentType'],
            'Content-Disposition' => 'inline; filename="'.$key.'"'
        ));
    }


    /**
     * @param string $filePath
     * @param string|null $folderPath
     * @return Result
     */
    public function upload(string $filePath, string $folderPath = ''): Result
    {
        if (!file_exists($filePath)) {
            throw new \LogicException("The file ($filePath) does not exist");
        }

        $pi = pathinfo($filePath);

        return $this->client->upload(
            $this->bucket,
            $this->buildPath($folderPath, $pi["extension"]),
            file_get_contents($filePath)
        );
    }

    /**
     * @param $output
     * @param string $fullPath
     * @return Result
     */
    public function uploadOutput($output, string $fullPath): Result
    {
        return $this->client->upload(
            $this->bucket,
            $fullPath,
            $output
        );

        return $this->client->putObject([
            'Bucket'     => $this->bucket,
            'SourceFile' => $file->getRealPath(),
            'Key'        => $this->buildPath($folderPath, $file->guessClientExtension())
        ]);
    }

    /**
     * @param string $fileName
     * @return Result
     */
    public function remove(string $fileName): Result
    {
        return $this->client->deleteObject(array(
            'Bucket' => $this->bucket,
            'Key'    => $fileName
        ));
    }

    /**
     * @param null|string $folderPath
     * @return string
     */
    private function buildPath(string $folderPath, string $ext): string
    {
        if ($folderPath !== '' && substr($folderPath, -1) != '/') {
            $folderPath .= '/';
        }

        $dir = str_replace('//', '/', $folderPath . uniqid() . '.' . $ext);

        return $dir;
    }
}
