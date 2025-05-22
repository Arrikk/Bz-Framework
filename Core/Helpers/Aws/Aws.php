<?php

namespace Core\Helpers\Aws;

use AsyncAws\S3\S3Client;
use Core\Http\Res;

class Aws extends AwsConfig
{
    private string $bucketName;
    private $file;
    private bool $isMultiple = false;
    private S3Client $s3;
    private string $folderName = "folder";

    public static function instance(): Aws
    {
        $aws = (new Aws);
        $aws->s3 = new S3Client(self::awsConfig());
        return $aws;
    }

    public function bucket(string $bucketName): Aws
    {
        $this->bucketName = $bucketName;
        return $this;
    }

    public function files(array|object|string $files): Aws
    {
        $this->file = $files;
        return $this;
    }

    public function filesMultipleV2(array|object $files)
    {
        $this->isMultiple = true;
        $filesF = [];
        foreach($files as $file):
            $filesF[$file->documentID] = $file->file;
        endforeach;
        $this->file = $filesF;

        // Res::send($filesF);
        return $this;
    }

    public function filesMultiple(array $files): Aws
    {
        $filesF = [];
        
        foreach ($files as $key => $file) :
            foreach($file as $fileKey => $fileData ):
                $filesF[$fileKey][$key] = $fileData;
            endforeach;
        endforeach;
        
        $this->isMultiple = true;
        $this->file = $filesF;
        return $this;
    }

    public function upload()
    {
        if($this->isMultiple):
            $uploaded = [];
            foreach($this->file as $fileID => $file):
                $file = (array) $file;
                $key = str_replace(" ", "_", $fileID."_".$file['name']);
                $this->s3Upload($key, $file['tmp_name'], $this->folderName);
                $uploaded[$fileID] = $this->getUploadS3Url($key);
            endforeach;
            return $uploaded;

            // $bd = $this->s3->getObject(['Key' => 'folder/845894_imgWha.jpg', 'Bucket'=>$this->bucketName]);
            // $body = $bd->getExpiration();
            // return file_get_contents($body);
            // Res::send($body);
        endif;
    }

    private function getUploadS3Url (string $key): string
    {
        $schema = "https://"; 
        $bucketName = $this->bucketName;
        $domainName = ".s3.amazonaws.com";
        $folderName = "/".$this->folderName."/";

        return $schema.$bucketName.$domainName.$folderName.$key;
    }

    private function s3Upload($fileName, $fileTmp, $folderName = 'files')
    {
        $objectKey = $folderName.'/' . $fileName;
        $result = $this->s3->putObject([
            'Bucket' => $this->bucketName,
            'Key' => $objectKey,
            'Body' => $fileTmp,
            // 'ACL' => 'public-read',
        ]);
        return $result;
    }
}
